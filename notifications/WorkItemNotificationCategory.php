<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\notifications;

use Yii;
use humhub\modules\notification\components\NotificationCategory;
use humhub\modules\notification\targets\WebTarget;

/** In-app notices for people directly involved in a governance work item. */
final class WorkItemNotificationCategory extends NotificationCategory
{
    public $id = 'sociocratic-governance-work';

    public function getTitle() { return Yii::t('SociocraticGovernanceModule.base', 'Vorhaben'); }

    public function getDescription()
    {
        return Yii::t('SociocraticGovernanceModule.base', 'Hinweise zu Ideen, Aufgaben und Abnahmen, an denen du beteiligt bist.');
    }

    public function getDefaultSetting($target)
    {
        // Deliberately in-app only: no address or work-item metadata leaves HumHub.
        return $target->id === WebTarget::getId();
    }
}
