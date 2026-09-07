<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\widgets;

use humhub\modules\sociocraticGovernance\services\{VCardData, VCardTemplate};
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/** Adapter for the existing Popover VCard 1.2.x views, without vendor patches. */
class GovernanceVCard extends \humhub\components\Widget
{
    public $user;
    public $space;

    public function run()
    {
        if (\Yii::$app->user->isGuest) { return ''; }
        $config = \Yii::$app->getModule('popover-vcard')->getConfiguration();
        if ($this->user instanceof User) {
            $user = $this->user;
            if (!$config->userEnabled || !User::find()->where(['id' => $user->id])->visible()->exists()) { return ''; }
            $description = VCardTemplate::render((string) $config->userContent, [
                'user' => array_merge($user->getAttributes(), ['rolls' => VCardData::roles($user)]),
                'profile' => $user->profile->getAttributes(),
            ]);
            return $this->renderCard('@humhub/modules/popovervcard/widgets/views/vcard-user', compact('user', 'description'));
        }
        if ($this->space instanceof Space) {
            $space = $this->space;
            if (!$config->spaceEnabled || !Space::find()->where(['space.id' => $space->id])->visible()->exists()
                || ($space->visibility == Space::VISIBILITY_NONE && !$space->isMember())
                || $space->isArchived() || $space->isBlockedForUser()) { return ''; }
            $memberCount = $space->getMemberListService()->getQuery()->count();
            $description = VCardTemplate::render((string) $config->spaceContent, [
                'space' => ['name' => $space->name, 'description' => $space->description,
                    'purpose' => VCardData::purpose($space), 'mandate' => VCardData::mandate($space)],
                'memberCount' => $memberCount,
            ]);
            return $this->renderCard('@humhub/modules/popovervcard/widgets/views/vcard-space', compact('space', 'description', 'memberCount'));
        }
        return '';
    }
    private function renderCard(string $view, array $params): string
    {
        $previous = VCardGovernance::$renderedDescription;
        VCardGovernance::$renderedDescription = $params['description'];
        try { return $this->render($view, $params); }
        finally { VCardGovernance::$renderedDescription = $previous; }
    }

}
