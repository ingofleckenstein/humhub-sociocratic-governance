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
    /** Scoped by GovernanceVCard while the original view renders its addon stack. */
    public static ?string $renderedDescription = null;

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
            return $content === '' ? '' : Html::tag('div', $content, ['class' => 'sg-vcard-details']);
        }
        return '';
    }

    public static function alreadyRendered(string $value): bool
    {
        if (self::$renderedDescription === null || trim($value) === '') { return false; }
        $plain = html_entity_decode(strip_tags(self::$renderedDescription), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalize = static fn($text) => preg_replace('/\s+/u', ' ', trim($text));
        return str_contains($normalize($plain), $normalize($value));
    }

    private function block(string $label, string $value): string
    {
        if (self::alreadyRendered($value)) { return ''; }
        return Html::tag('div', Html::tag('strong', Html::encode($label)) . '<br>' . Html::encode($value), ['class' => 'sg-vcard-detail']);
    }
}
