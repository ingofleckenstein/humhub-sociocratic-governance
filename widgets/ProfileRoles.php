<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\widgets;
use humhub\modules\sociocraticGovernance\models\{Role, PermanentMembership};
use humhub\modules\sociocraticGovernance\services\Access;
use yii\helpers\Html;
class ProfileRoles extends \humhub\components\Widget
{
    public $user;
    public function run()
    {
        if (\Yii::$app->user->isGuest || !$this->user || (int) $this->user->status !== \humhub\modules\user\models\User::STATUS_ENABLED) { return ''; }
        $lines = [];
        foreach (Role::find()->where(['user_id' => $this->user->id])->with(['circle.space'])->all() as $role) {
            $space = $role->circle ? $role->circle->space : null;
            if (Access::read($space) && $space->isMember($this->user->id) && !$space->isArchived()) {
                $lines[] = Html::tag('li', Html::a(Html::encode('AK ' . $space->name . ' – ' . Role::LABELS[$role->role_key]), $space->createUrl('/sociocratic-governance/circle/index')));
            }
        }
        foreach (PermanentMembership::find()->where(['user_id' => $this->user->id])->with('space')->all() as $item) {
            if (Access::read($item->space) && $item->space->isMember($this->user->id) && !$item->space->isArchived()) {
                $lines[] = Html::tag('li', Html::a(Html::encode('AK ' . $item->space->name . ' – ' . $item->reason . ' (dauerhaft)'), $item->space->createUrl('/sociocratic-governance/circle/index')));
            }
        }
        if (!$lines) { return ''; }
        return Html::tag('div', Html::tag('div', '<strong>Kreisrollen</strong>', ['class' => 'panel-heading']) .
            Html::tag('div', Html::tag('ul', implode('', $lines)), ['class' => 'panel-body']), ['class' => 'panel panel-default']);
    }
}
