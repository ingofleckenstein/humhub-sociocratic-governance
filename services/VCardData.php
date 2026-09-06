<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use humhub\modules\sociocraticGovernance\models\{Circle, Configuration, Role};
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/** Values shown by the optional Popover VCard integration. */
final class VCardData
{
    /**
     * Returns `AK-Name:Rolle` values ordered from the configured core circle
     * towards its descendants. Invisible, archived and inactive assignments
     * never appear in a card.
     */
    public static function roles(User $user): string
    {
        if ((int) $user->status !== User::STATUS_ENABLED) {
            return '';
        }
        $rootId = (int) (Configuration::findOne(1)?->root_space_id ?? 0);
        $parentRows = Circle::find()->select(['space_id', 'parent_space_id'])->asArray()->all();
        $parents = array_column($parentRows, 'parent_space_id', 'space_id');
        $entries = [];
        foreach (Role::find()->where(['user_id' => $user->id])->with(['circle.space'])->all() as $role) {
            $space = $role->circle?->space;
            if (!$space || !$space->isMember($user->id) || $space->isArchived() || !Access::read($space)) {
                continue;
            }
            $entries[] = [
                'distance' => self::distanceToRoot((int) $space->id, $rootId, $parents),
                'label' => $space->name . ':' . (Role::LABELS[$role->role_key] ?? $role->role_key),
            ];
        }
        usort($entries, static function (array $a, array $b): int {
            return $a['distance'] <=> $b['distance'] ?: strnatcasecmp($a['label'], $b['label']);
        });
        return implode(', ', array_column($entries, 'label'));
    }

    public static function purpose(Space $space): string
    {
        $circle = self::visibleCircle($space);
        return $circle ? trim((string) $circle->purpose) : '';
    }

    public static function mandate(Space $space): string
    {
        $circle = self::visibleCircle($space);
        if (!$circle) {
            return '';
        }
        $summary = $circle->mandateSummary();
        if ($summary !== '') {
            return $summary;
        }
        return trim(implode(' ', array_filter([
            (string) $circle->responsibility,
            (string) $circle->authority,
            (string) $circle->boundaries,
        ])));
    }

    private static function visibleCircle(Space $space): ?Circle
    {
        if ($space->isArchived() || !Access::read($space)) {
            return null;
        }
        return Circle::findOne(['space_id' => $space->id]);
    }

    private static function distanceToRoot(int $spaceId, int $rootId, array $parents): int
    {
        if (!$rootId) {
            return PHP_INT_MAX;
        }
        $distance = 0;
        $seen = [];
        while ($spaceId && !isset($seen[$spaceId])) {
            if ($spaceId === $rootId) {
                return $distance;
            }
            $seen[$spaceId] = true;
            $spaceId = (int) ($parents[$spaceId] ?? 0);
            $distance++;
        }
        return PHP_INT_MAX;
    }
}
