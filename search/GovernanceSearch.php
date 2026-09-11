<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\search;

use humhub\modules\sociocraticGovernance\models\{Circle, WorkItem, WorkResource};
use humhub\modules\sociocraticGovernance\services\{Access, WorkAccess};

/** Searches Governance-owned records and never bypasses the established visibility checks. */
final class GovernanceSearch
{
    public function search(?string $keyword, int $limit = 30): array
    {
        $keyword = is_string($keyword) ? trim($keyword) : '';
        if ($keyword === '') { return ['totalCount' => 0, 'results' => []]; }
        $results = [];

        foreach (Access::visibleCircles() as $circle) {
            $space = $circle->space;
            if (!$space || !$this->matches($keyword, $space->name, $circle->purpose, $circle->mandate_summary, $circle->mandate)) { continue; }
            $type = $circle->isCompetenceCircle() ? 'Kompetenzkreis' : 'Projektkreis';
            $results[] = new GovernanceSearchResult($type, $space->name, $this->summary($circle->mandate_summary ?: $circle->purpose),
                (string) $space->createUrl('/sociocratic-governance/circle/index'));
        }

        foreach (WorkItem::find()->where(['archived_at' => null])->with(['space', 'circle', 'topics', 'resources'])->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])->all() as $item) {
            if (!WorkAccess::read($item) || !$item->space) { continue; }
            $topics = implode(' ', array_map(static fn($topic) => $topic->name, $item->topics));
            if ($this->matches($keyword, $item->title, $item->description, $topics)) {
                $type = $item->kind === 'idea' ? 'Idee' : 'Aufgabe';
                $status = WorkItem::STATUSES[$item->status] ?? $item->status;
                $results[] = new GovernanceSearchResult($type, $item->title, $status . ' · ' . $item->space->name,
                    (string) $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id]));
            }
            foreach ($item->resources as $resource) {
                if (!$this->matches($keyword, $resource->label)) { continue; }
                $results[] = new GovernanceSearchResult('Ressource', $resource->label,
                    (WorkResource::TYPES[$resource->resource_type] ?? $resource->resource_type) . ' · für ' . $item->title,
                    (string) $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id, 'section' => 'resources']));
            }
        }

        return ['totalCount' => count($results), 'results' => array_slice($results, 0, max(1, $limit))];
    }

    private function matches(string $needle, string ...$values): bool
    {
        $needle = mb_strtolower($needle);
        foreach ($values as $value) {
            if (str_contains(mb_strtolower((string) $value), $needle)) { return true; }
        }
        return false;
    }

    private function summary(?string $text): string
    {
        $text = trim((string) $text);
        return mb_strimwidth($text === '' ? 'Noch nicht beschrieben.' : $text, 0, 150, '…');
    }
}
