<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use humhub\modules\sociocraticGovernance\models\{Circle, WorkItem};
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

final class WorkAccess
{
    public static function activeUser(): bool
    {
        return !\Yii::$app->user->isGuest && User::find()->where([
            'id' => \Yii::$app->user->id, 'status' => User::STATUS_ENABLED,
        ])->exists();
    }
    public static function read(WorkItem $item): bool
    {
        if (!self::activeUser() || !Access::read($item->space)) { return false; }
        // A transfer never declassifies earlier private discussions or text versions.
        foreach (array_unique(array_column($item->events, 'space_id')) as $id) {
            if (!Access::read(Space::findOne($id))) { return false; }
        }
        return true;
    }
    /**
     * Equivalent of read() for a notification recipient. A transferred item
     * remains protected by every circle that appears in its event history.
     */
    public static function readForUser(WorkItem $item, User $user): bool
    {
        if (!Access::readForUser($item->space, $user)) { return false; }
        foreach (array_unique(array_column($item->events, 'space_id')) as $id) {
            if (!Access::readForUser(Space::findOne($id), $user)) { return false; }
        }
        return true;
    }
    public static function reviewers(Space $space): array
    {
        $result = [];
        $circle = Circle::findOne($space->id);
        foreach ($circle?->roles ?? [] as $role) {
            if (in_array($role->role_key, ['leader', 'delegate'], true) && $role->user
                && (int) $role->user->status === User::STATUS_ENABLED && $space->isMember($role->user_id)) {
                $result[$role->role_key] = (int) $role->user_id;
            }
        }
        return $result;
    }
    public static function reviewer(Space $space): bool
    {
        return self::activeUser() && Access::write($space)
            && in_array((int) \Yii::$app->user->id, self::reviewers($space), true);
    }
    public static function neighbours(Space $space): array
    {
        $circle = Circle::findOne($space->id);
        if (!$circle) { return []; }
        $result = [];
        foreach (Access::visibleCircles() as $other) {
            if ((int) $other->space_id === (int) $circle->parent_space_id
                || (int) $other->parent_space_id === (int) $circle->space_id) {
                $result[(int) $other->space_id] = $other->space->name;
            }
        }
        return $result;
    }
}
