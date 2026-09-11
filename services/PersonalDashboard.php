<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\sociocraticGovernance\models\{Circle, WorkItem, WorkResourceContribution};
use humhub\modules\notification\models\Notification;
use humhub\modules\space\models\Membership;
use humhub\modules\user\models\Follow;

/** Builds the personal overview while preserving the same visibility rules as the rest of the module. */
final class PersonalDashboard
{
    public function data(): array
    {
        $user = Yii::$app->user->getIdentity();
        $spacesById = [];
        foreach (Membership::findByUser($user)->with('space')->all() as $membership) {
            if ($membership->space) { $spacesById[(int) $membership->space->id] = $membership->space; }
        }
        foreach (Follow::getFollowedSpacesQuery($user)->all() as $space) {
            $spacesById[(int) $space->id] = $space;
        }

        $circleBySpaceId = $spacesById
            ? Circle::find()->where(['space_id' => array_keys($spacesById)])->with('space')->indexBy('space_id')->all()
            : [];
        $circles = [];
        $spaces = [];
        foreach ($spacesById as $spaceId => $space) {
            $circle = $circleBySpaceId[$spaceId] ?? null;
            if ($circle) {
                if (Access::read($space)) { $circles[] = $circle; }
            } else {
                $spaces[] = $space;
            }
        }
        usort($circles, static fn($a, $b) => strnatcasecmp($a->space->name, $b->space->name));
        usort($spaces, static fn($a, $b) => strnatcasecmp($a->name, $b->name));

        $tasks = [];
        foreach (WorkItem::find()->where(['assignee_id' => $user->id, 'archived_at' => null])
            ->andWhere(['not in', 'status', ['done', 'rejected']])->with(['space', 'circle'])->orderBy(['updated_at' => SORT_DESC])->all() as $item) {
            if (WorkAccess::read($item)) { $tasks[] = $item; }
        }

        $contributions = [];
        foreach (WorkResourceContribution::find()->where(['user_id' => $user->id])->andWhere(['>', 'amount', 0])
            ->with(['resource.workItem.space', 'resource.workItem.circle'])->orderBy(['updated_at' => SORT_DESC])->all() as $contribution) {
            if ($contribution->resource?->workItem && WorkAccess::read($contribution->resource->workItem)) {
                $contributions[] = $contribution;
            }
        }

        $notifications = [];
        $unseenNotificationCount = 0;
        if (class_exists(Notification::class)) {
            try {
                $unseenNotificationCount = Notification::findUnseen($user)->count();
                foreach (Notification::findUnseen($user)->limit(3)->all() as $notification) {
                    $baseNotification = $notification->getBaseModel();
                    if (!$baseNotification) { continue; }
                    $label = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($baseNotification->render()))));
                    if ($label === '') { continue; }
                    $notifications[] = [
                        'label' => $label,
                        'url' => ['/notification/entry/index', 'id' => (int) $notification->id],
                    ];
                }
            } catch (\Throwable $e) {
                Yii::warning($e, __METHOD__);
            }
        }

        return compact('circles', 'spaces', 'tasks', 'contributions', 'notifications', 'unseenNotificationCount');
    }
}
