<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\sociocraticGovernance\models\Configuration;

/** Builds a visibility-safe, deterministic tree for both directory views. */
final class CircleDirectory
{
    /** Active, visible members, once per person, with all their circle roles. */
    public static function people($circle): array
    {
        if (!Access::read($circle->space) || $circle->space->isArchived()) { return []; }
        $people = [];
        foreach ($circle->space->getMemberListService()->getQuery()->all() as $user) {
            if ((int) $user->status !== \humhub\modules\user\models\User::STATUS_ENABLED) { continue; }
            $labels = [];
            foreach ($circle->roles as $role) {
                if ((int) $role->user_id === (int) $user->id) {
                    $labels[] = \humhub\modules\sociocraticGovernance\models\Role::LABELS[$role->role_key] ?? $role->role_key;
                }
            }
            $people[] = ['user' => $user, 'label' => $labels ? implode(', ', $labels) : 'Kreismitglied'];
        }
        usort($people, static fn($a, $b) => strnatcasecmp($a['user']->displayName, $b['user']->displayName)
            ?: (int) $a['user']->id <=> (int) $b['user']->id);
        return $people;
    }

    public function data(): array
    {
        $visible = Access::visibleCircles();
        $byId = [];
        foreach ($visible as $circle) { $byId[(int) $circle->space_id] = $circle; }

        $children = [];
        foreach ($byId as $id => $circle) {
            $parentId = (int) $circle->parent_space_id;
            if ($parentId && isset($byId[$parentId])) { $children[$parentId][] = $id; }
        }
        foreach ($children as &$ids) {
            usort($ids, fn(int $a, int $b): int => strnatcasecmp($byId[$a]->space->name, $byId[$b]->space->name));
        }
        unset($ids);

        $rootId = (int) (Configuration::findOne(1)?->root_space_id ?? 0);
        $roots = isset($byId[$rootId]) ? [$rootId] : [];
        foreach ($byId as $id => $circle) {
            if ((!$circle->parent_space_id || !isset($byId[(int) $circle->parent_space_id])) && !in_array($id, $roots, true)) {
                $roots[] = $id;
            }
        }
        usort($roots, fn(int $a, int $b): int => strnatcasecmp($byId[$a]->space->name, $byId[$b]->space->name));

        $rows = [];
        $nodes = [];
        $leaf = 0;
        $visit = function (int $id, int $depth) use (&$visit, &$rows, &$nodes, &$leaf, $byId, $children): float {
            $circle = $byId[$id];
            $rows[] = ['circle' => $circle, 'depth' => $depth];
            $childIds = $children[$id] ?? [];
            $childX = [];
            foreach ($childIds as $childId) { $childX[] = $visit($childId, $depth + 1); }
            $x = $childX ? array_sum($childX) / count($childX) : $leaf++;
            $people = self::people($circle);
            $nodes[$id] = ['people' => $people, 'diameter' => max(260, (int) ceil(count($people) * 46 / M_PI + 64)), 'circle' => $circle, 'depth' => $depth, 'x' => $x, 'parentId' => (int) $circle->parent_space_id];
            return $x;
        };
        foreach ($roots as $root) { $visit($root, 0); }

        $roleSpaceIds = [];
        $userId = Yii::$app->user->id;
        foreach ($nodes as $id => $node) {
            foreach ($node['circle']->roles as $role) {
                if ((int) $role->user_id === (int) $userId) { $roleSpaceIds[] = $id; break; }
            }
        }
        return ['rows' => $rows, 'nodes' => $nodes, 'focusSpaceIds' => $roleSpaceIds, 'hasConfiguredRoot' => isset($byId[$rootId])];
    }
}
