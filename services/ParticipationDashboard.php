<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use humhub\modules\sociocraticGovernance\models\WorkItem;

/** Builds the global, visibility-filtered answer to “How can I contribute?”. */
final class ParticipationDashboard
{
    public function data(?string $topic = null): array
    {
        $items = [];
        $topicCounts = [];
        $resources = [];
        $topic = is_string($topic) ? trim($topic) : null;
        foreach (WorkItem::find()->where(['archived_at' => null])->with(['events', 'space', 'circle', 'assignee', 'topics', 'resources.contributions.user'])->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])->all() as $item) {
            if (!WorkAccess::read($item)) { continue; }
            foreach ($item->topics as $workTopic) {
                $key = mb_strtolower($workTopic->name);
                $topicCounts[$key] = ['name' => $workTopic->name, 'count' => ($topicCounts[$key]['count'] ?? 0) + 1];
            }
            if ($topic !== null && $topic !== '' && !array_filter($item->topics, static fn($workTopic) => mb_strtolower($workTopic->name) === mb_strtolower($topic))) {
                continue;
            }
            $items[] = $item;
            foreach ($item->resources as $resource) { $resources[] = ['item' => $item, 'resource' => $resource]; }
        }
        uasort($topicCounts, static fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
        return ['items' => $items, 'topics' => array_values($topicCounts), 'resources' => $resources, 'selectedTopic' => $topic];
    }
}
