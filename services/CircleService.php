<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, Configuration, Role};

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
            $circle->purpose = $form->purpose;
            $circle->mandate = $form->mandate;
            $circle->mandate_summary = $form->mandate_summary;
            $circle->responsibility = $form->responsibility;
            $circle->authority = $form->authority;
            $circle->boundaries = $form->boundaries;
            $circle->budget = $form->budget;
            $circle->reelection_interval = $form->reelection_interval;
            $circle->review = $form->review;
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
}
