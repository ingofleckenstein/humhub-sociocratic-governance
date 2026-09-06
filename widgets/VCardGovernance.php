<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\widgets;

use humhub\modules\sociocraticGovernance\services\VCardData;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\helpers\Html;

/** Optional content block injected into Popover VCard 1.2.1 cards. */
class VCardGovernance extends \humhub\components\Widget
{
    public $container;

    public function run()
    {
        if ($this->container instanceof User) {
            $roles = VCardData::roles($this->container);
            return $roles === '' ? '' : $this->block('Kreisrollen', $roles);
        }
        if ($this->container instanceof Space) {
            $purpose = VCardData::purpose($this->container);
            $mandate = VCardData::mandate($this->container);
            if ($purpose === '' && $mandate === '') {
                return '';
            }
            $content = ($purpose === '' ? '' : $this->block('Zweck', $purpose)) .
                ($mandate === '' ? '' : $this->block('Mandat', $mandate));
            return Html::tag('div', $content, ['class' => 'sg-vcard-details']);
        }
        return '';
    }

    private function block(string $label, string $value): string
    {
        return Html::tag('div', Html::tag('strong', Html::encode($label)) . '<br>' . Html::encode($value), ['class' => 'sg-vcard-detail']);
    }
}
