<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

final class Access
{
    public static function enabled(?Space $space): bool
    {
        return $space !== null && $space->moduleManager->isEnabled('sociocratic-governance');
    }
    public static function read(?Space $space): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        return self::readForUser($space, User::findOne((int) Yii::$app->user->id));
    }
    /**
     * Access checks for background work must use the recipient, not the
     * currently logged-in person. This keeps notifications from disclosing
     * private circle history after a delegation.
     */
    public static function readForUser(?Space $space, ?User $user): bool
    {
        if (!$user || (int) $user->status !== User::STATUS_ENABLED || !self::enabled($space)
            || $space->isArchived() || $space->isBlockedForUser($user)) {
            return false;
        }
        return $space->visibility != Space::VISIBILITY_NONE || $space->isMember($user->id);
    }
    public static function write(?Space $space): bool
    {
        return self::read($space) && $space->isMember() && !$space->isArchived();
    }
    public static function memberOptions(Space $space): array
    {
        $items = [];
        foreach ($space->getMemberListService()->getQuery()->all() as $user) {
            $items[(int) $user->id] = $user->displayName;
        }
        return $items;
    }
    public static function visibleCircles(): array
    {
        $result = [];
        foreach (\humhub\modules\sociocraticGovernance\models\Circle::find()->with(['space', 'roles.user'])->all() as $circle) {
            if (self::read($circle->space)) { $result[] = $circle; }
        }
        return $result;
    }
}
