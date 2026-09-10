<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\content\models\Content;
use humhub\modules\post\models\Post;
use humhub\modules\user\models\User;
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, Configuration, Role};
use yii\helpers\Url;

final class CircleService
{
    public function save(Space $space, CircleForm $form): bool
    {
        if (!Access::write($space)) { throw new \yii\web\ForbiddenHttpException('Nur Kreismitglieder dürfen schreiben.'); }
        if (!$form->validate()) { return false; }
        $db = Yii::$app->db;
        $tx = $db->beginTransaction();
        try {
            // Serialize changes to the hierarchy, including concurrently created circles.
            $sql = 'SELECT [[id]] FROM {{%sg_config}} WHERE [[id]]=1';
            if ($db->driverName !== 'sqlite') { $sql .= ' FOR UPDATE'; }
            $db->createCommand($sql)->queryScalar();
            $circle = Circle::findOne($space->id);
            if ((int) $form->revision !== ($circle ? (int) $circle->revision : -1)) {
                throw new \DomainException('Inzwischen wurde der Kreis geändert. Bitte Seite neu laden und Änderungen erneut eintragen.');
            }
            $parents = [];
            foreach (Circle::find()->all() as $item) {
                $parents[(int) $item->space_id] = $item->parent_space_id === null ? null : (int) $item->parent_space_id;
            }
            $parentId = $form->parent_space_id === null ? null : (int) $form->parent_space_id;
            if ($parentId !== null && !Access::read(Space::findOne($parentId))) {
                throw new \DomainException('Der Oberkreis ist nicht verfügbar.');
            }
            Rules::assertParent((int) $space->id, $parentId, $parents);
            $config = Configuration::findOne(1);
            if ((int) $config->root_space_id === (int) $space->id && $parentId !== null) {
                throw new \DomainException('Der Kernkreis hat keinen Oberkreis.');
            }
            $roles = $form->roleValues();
            Rules::assertRoles($roles, array_keys(Access::memberOptions($space)));
            $circle = $circle ?? new Circle(['space_id' => $space->id, 'revision' => -1]);
            $color = $form->color ?: Circle::suggestedColor((int) $space->id);
            if (Circle::hasAvailableColor((int) $space->id)
                && Circle::find()->where(['color' => $color])->andWhere(['<>', 'space_id', $space->id])->exists()) {
                throw new \DomainException('Diese Farbe ist bereits einem anderen Kreis zugeordnet. Bitte eine freie Farbe wählen.');
            }
            $circle->type = $form->type;
            $circle->purpose = $form->purpose;
            $circle->mandate = $form->mandate;
            $circle->mandate_summary = $form->mandate_summary;
            $circle->responsibility = $form->responsibility;
            $circle->authority = $form->authority;
            $circle->boundaries = $form->boundaries;
            $circle->budget = $form->budget;
            $circle->reelection_interval = $form->reelection_interval;
            $circle->review = $form->review;
            $circle->color = $color;
            $circle->parent_space_id = $parentId;
            $circle->revision = (int) $circle->revision + 1;
            $circle->updated_at = time();
            $circle->updated_by = Yii::$app->user->id;
            if (!$circle->save(false)) { throw new \RuntimeException('Speichern fehlgeschlagen.'); }
            Role::deleteAll(['space_id' => $space->id]);
            foreach ($roles as $key => $id) {
                if ($id !== null) {
                    $role = new Role(['space_id' => $space->id, 'user_id' => $id, 'role_key' => $key]);
                    if (!$role->save(false)) { throw new \RuntimeException('Rolle konnte nicht gespeichert werden.'); }
                }
            }
            $leaderId = $roles['leader'] ?? null;
            if ($leaderId !== null && method_exists($space, 'isSpaceOwner') && !$space->isSpaceOwner($leaderId)) {
                if (method_exists($space, 'isAdmin') && !$space->isAdmin()) {
                    throw new \DomainException('Nur Space-Besitzer*innen oder -Administrator*innen dürfen die Kreisleitung mit dem Space-Besitz verbinden.');
                }
                if (!$space->setSpaceOwner($leaderId)) {
                    throw new \RuntimeException('Die Kreisleitung konnte nicht als Space-Besitzer*in gesetzt werden.');
                }
            }
            $tx->commit();
            return true;
        } catch (\DomainException $e) {
            $tx->rollBack();
            $form->addError('purpose', $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            $tx->rollBack();
            throw $e;
        }
    }

    /** Publishes a completed circle and creates exactly one welcome post. */
    public function publish(Space $space): void
    {
        if (!Access::admin($space)) {
            throw new \yii\web\ForbiddenHttpException('Nur Space-Administrator*innen dürfen einen Kreis veröffentlichen.');
        }
        $db = Yii::$app->db;
        $tx = $db->beginTransaction();
        try {
            $sql = 'SELECT [[space_id]] FROM {{%sg_circle}} WHERE [[space_id]]=:spaceId';
            if ($db->driverName !== 'sqlite') { $sql .= ' FOR UPDATE'; }
            $db->createCommand($sql, [':spaceId' => $space->id])->queryScalar();
            $circle = Circle::findOne($space->id);
            if (!$circle || trim($circle->mandateSummary()) === '') {
                throw new \DomainException('Bitte hinterlege zuerst „Mandat in Kürze“, bevor du den Space veröffentlichst.');
            }
            if ($circle->is_published) {
                throw new \DomainException('Dieser Space ist bereits veröffentlicht.');
            }
            $configSql = 'SELECT [[id]] FROM {{%sg_config}} WHERE [[id]]=1';
            if ($db->driverName !== 'sqlite') { $configSql .= ' FOR UPDATE'; }
            $db->createCommand($configSql)->queryScalar();
            $config = Configuration::findOne(1);
            $account = $config && $config->company_user_id ? User::findOne((int) $config->company_user_id) : null;
            if (!$account || (int) $account->status !== User::STATUS_ENABLED) {
                throw new \DomainException('Bitte wähle im Governance-Backend ein aktives Kommunikationskonto für die Veröffentlichungsnachricht.');
            }

            $space->visibility = Space::VISIBILITY_REGISTERED_ONLY;
            $space->join_policy = Space::JOIN_POLICY_APPLICATION;
            $space->default_content_visibility = Content::VISIBILITY_PUBLIC;
            if (!$space->save(false, ['visibility', 'join_policy', 'default_content_visibility'])) {
                throw new \RuntimeException('Die Sichtbarkeit des Space konnte nicht veröffentlicht werden.');
            }
            $circle->is_published = 1;
            $circle->published_at = time();
            $circle->published_by = Yii::$app->user->id;
            if (!$circle->save(false, ['is_published', 'published_at', 'published_by'])) {
                throw new \RuntimeException('Der Veröffentlichungsstatus konnte nicht gespeichert werden.');
            }
            $post = new Post($space);
            $post->content->created_by = $account->id;
            $post->content->updated_by = $account->id;
            $post->message = $this->publicationMessage($space, $circle);
            if (!$post->save()) {
                throw new \RuntimeException('Die Veröffentlichungsnachricht konnte nicht gespeichert werden.');
            }
            $tx->commit();
        } catch (\Throwable $e) {
            $tx->rollBack();
            throw $e;
        }
    }

    private function publicationMessage(Space $space, Circle $circle): string
    {
        $name = $this->markdownText((string) $space->name);
        $summary = $this->markdownText($circle->mandateSummary());
        $url = Url::to($space->createUrl('/sociocratic-governance/circle/index'), true);
        $link = '[Kreis ansehen](' . str_replace(['(', ')'], ['%28', '%29'], $url) . ')';
        return "🎉 **Neuer Kreis: {$name}**\n\n**Mandat in Kürze:** {$summary}\n\n"
            . 'Falls dieses Thema dich interessiert, kannst du dem Kreis jederzeit beitreten oder auch wieder austreten. '
            . 'Wenn du nur über Aktivitäten informiert werden möchtest, kannst du dem Kreis folgen.'
            . "\n\n{$link}";
    }

    private function markdownText(string $value): string
    {
        return strtr(trim(strip_tags($value)), [
            '\\' => '\\\\', '*' => '\\*', '_' => '\\_', '[' => '\\[', ']' => '\\]',
            '(' => '\\(', ')' => '\\)', '`' => '\\`', '<' => '\\<', '>' => '\\>',
        ]);
    }
}
