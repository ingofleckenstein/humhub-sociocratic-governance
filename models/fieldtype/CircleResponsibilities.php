<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models\fieldtype;

use Yii;
use humhub\helpers\Html;
use humhub\modules\sociocraticGovernance\assets\GovernanceAsset;
use humhub\modules\sociocraticGovernance\models\{Circle, Role};
use humhub\modules\sociocraticGovernance\services\Access;
use humhub\modules\user\models\User;
use humhub\modules\user\models\fieldtype\BaseTypeVirtual;

/** Read-only profile field showing a user's visible circle memberships and roles. */
final class CircleResponsibilities extends BaseTypeVirtual
{
    protected function getVirtualUserValue(User $user, bool $raw = true, bool $encode = true): string
    {
        $rows = [];
        foreach (Circle::find()->with(['space', 'roles'])->all() as $circle) {
            $space = $circle->space;
            if (!$space || !$space->isMember($user->id) || $space->isArchived() || !Access::read($space)) {
                continue;
            }
            $roles = [];
            foreach ($circle->roles as $role) {
                if ((int) $role->user_id === (int) $user->id && isset(Role::LABELS[$role->role_key])) {
                    $roles[] = Role::LABELS[$role->role_key];
                }
            }
            $rows[] = ['circle' => $circle, 'space' => $space, 'roles' => $roles];
        }
        if (!$rows) {
            return '';
        }
        usort($rows, static fn(array $a, array $b): int => [$a['circle']->type, $a['space']->name] <=> [$b['circle']->type, $b['space']->name]);
        if (Yii::$app instanceof \yii\web\Application) {
            GovernanceAsset::register(Yii::$app->view);
        }
        $items = [];
        foreach ($rows as $row) {
            $circle = $row['circle'];
            $space = $row['space'];
            $type = Circle::TYPES[$circle->type] ?? Circle::TYPES['project'];
            $roles = $row['roles'] ? implode(' · ', $row['roles']) : 'Mitglied';
            $items[] = Html::tag('li',
                Html::tag('span', Html::encode($type), ['class' => 'sg-profile-circle-type'])
                . Html::a(Html::encode($space->name), $space->createUrl($circle->isCompetenceCircle() ? '/space/space/home' : '/sociocratic-governance/circle/index'))
                . Html::tag('span', Html::encode($roles), ['class' => 'sg-profile-circle-roles'])
            );
        }
        return Html::tag('ul', implode('', $items), ['class' => 'sg-profile-responsibilities']);
    }
}
