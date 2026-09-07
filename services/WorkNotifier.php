<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\sociocraticGovernance\models\WorkItem;
use humhub\modules\sociocraticGovernance\notifications\WorkItemNotification;
use humhub\modules\user\models\User;

/** Sends in-app-only notices after a committed work-item change. */
final class WorkNotifier
{
    public static function send(WorkItem $item, string $action, array $before = []): void
    {
        if (!Yii::$app->has('notification')) { return; }
        $ids = WorkNotificationRecipients::ids($item, $action, $before);
        if (!$ids) { return; }
        try {
            $notification = WorkItemNotification::instance()
                ->from(Yii::$app->user->getIdentity())
                ->about($item)
                ->event($action);
            // Do not enqueue the creation of the notification itself. A queued
            // notification can be lost or delayed when no queue worker is running,
            // which is especially harmful for a newly submitted idea. Web delivery
            // remains in-app only through the notification category.
            Yii::$app->notification->sendBulk($notification, User::find()->where(['id' => $ids]));
        } catch (\Throwable $e) {
            // A notification outage must not roll back an already valid governance action.
            Yii::warning($e->getMessage(), 'sociocratic-governance.notification');
        }
    }
}
