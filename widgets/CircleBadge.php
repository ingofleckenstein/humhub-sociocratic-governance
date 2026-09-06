<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\widgets;
use humhub\modules\sociocraticGovernance\services\Access;
use yii\helpers\Html;
class CircleBadge extends \humhub\components\Widget
{
    public $space;
    public function run()
    {
        if (!Access::read($this->space)) { return ''; }
        return Html::tag('div', Html::tag('div', '<strong>Arbeitskreis</strong>', ['class' => 'panel-heading']) .
            Html::tag('div', 'Verantwortung im vereinbarten Mandat. ' .
                Html::a('Kreis öffnen', $this->space->createUrl('/sociocratic-governance/circle/index')),
                ['class' => 'panel-body']), ['class' => 'panel panel-default']);
    }
}
