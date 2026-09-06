<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\space\models\Space;

final class Access
{
    public static function enabled(?Space $space): bool
    {
        return $space !== null && $space->moduleManager->isEnabled('sociocratic-governance');
    }
    public static function read(?Space $space): bool
    {
        if (Yii::$app->user->isGuest || !self::enabled($space) || $space->isBlockedForUser()) {
            return false;
        }
        return $space->visibility != Space::VISIBILITY_NONE || $space->isMember();
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
