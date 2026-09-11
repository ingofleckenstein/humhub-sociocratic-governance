<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use humhub\modules\sociocraticGovernance\models\WorkItem;
use humhub\modules\sociocraticGovernance\models\WorkResource;

/** Builds the global, visibility-filtered answer to “How can I contribute?”. */
final class ParticipationDashboard
{
    public function data(?string $topic = null, ?string $resourceType = null, ?int $circleId = null): array
    {
        $items = [];
        $topicCounts = [];
        $circleCounts = [];
        $resources = [];
        $resourceTypeCounts = array_fill_keys(array_keys(WorkResource::TYPES), 0);
        $topic = is_string($topic) ? trim($topic) : null;
        $resourceType = is_string($resourceType) && array_key_exists($resourceType, WorkResource::TYPES) ? $resourceType : null;
        foreach (WorkItem::find()->where(['archived_at' => null])->with(['events', 'space', 'circle', 'assignee', 'topics', 'resources.contributions.user'])->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])->all() as $item) {
            if (!WorkAccess::read($item)) { continue; }
            if ($item->circle) {
                $key = (int) $item->space_id;
                $circleCounts[$key] = [
                    'id' => $key,
                    'name' => $item->space->name,
                    'type' => $item->circle->isCompetenceCircle() ? 'Kompetenzkreis' : 'Projektkreis',
                    'count' => ($circleCounts[$key]['count'] ?? 0) + 1,
                ];
            }
            if ($circleId !== null && (int) $item->space_id !== $circleId) { continue; }
            foreach ($item->topics as $workTopic) {
                $key = mb_strtolower($workTopic->name);
                $topicCounts[$key] = ['name' => $workTopic->name, 'count' => ($topicCounts[$key]['count'] ?? 0) + 1];
            }
            if ($topic !== null && $topic !== '' && !array_filter($item->topics, static fn($workTopic) => mb_strtolower($workTopic->name) === mb_strtolower($topic))) {
                continue;
            }
            $items[] = $item;
            foreach ($item->resources as $resource) {
                $resourceTypeCounts[$resource->resource_type]++;
                if ($resourceType === null || $resource->resource_type === $resourceType) {
                    $resources[] = ['item' => $item, 'resource' => $resource];
                }
            }
        }
        uasort($topicCounts, static fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
        uasort($circleCounts, static fn($a, $b) => strnatcasecmp($a['name'], $b['name']));
        return ['items' => $items, 'topics' => array_values($topicCounts), 'resources' => $resources, 'selectedTopic' => $topic,
            'selectedResourceType' => $resourceType, 'resourceTypeCounts' => $resourceTypeCounts,
            'circles' => array_values($circleCounts), 'selectedCircleId' => $circleId];
    }
}
