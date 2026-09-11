<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\search;

use Yii;
use humhub\interfaces\MetaSearchProviderInterface;
use humhub\services\MetaSearchService;

final class GovernanceSearchProvider implements MetaSearchProviderInterface
{
    private ?MetaSearchService $service = null;
    public ?string $keyword = null;
    public string|array|null $route = '/sociocratic-governance/search/index';

    public function getName(): string { return 'Kreise & Mitwirken'; }
    public function getSortOrder(): int { return 350; }
    public function getRoute(): string|array { return $this->route; }
    public function getAllResultsText(): string { return $this->getService()->hasResults() ? 'Alle Governance-Ergebnisse' : 'Kreise und Vorhaben durchsuchen'; }
    public function getIsHiddenWhenEmpty(): bool { return true; }
    public function getResults(int $maxResults): array { return (new GovernanceSearch())->search($this->getKeyword(), $maxResults); }
    public function getService(): MetaSearchService { return $this->service ??= new MetaSearchService($this); }
    public function getKeyword(): ?string { return $this->keyword; }
}
