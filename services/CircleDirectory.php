<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\sociocraticGovernance\models\Configuration;

/** Builds a visibility-safe, deterministic tree for both directory views. */
final class CircleDirectory
{
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
        if (!$roots) {
            foreach ($byId as $id => $circle) {
                if (!$circle->parent_space_id || !isset($byId[(int) $circle->parent_space_id])) { $roots[] = $id; }
            }
            usort($roots, fn(int $a, int $b): int => strnatcasecmp($byId[$a]->space->name, $byId[$b]->space->name));
        }

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
            $nodes[$id] = ['circle' => $circle, 'depth' => $depth, 'x' => $x, 'parentId' => (int) $circle->parent_space_id];
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
