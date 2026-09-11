<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\search;

use humhub\interfaces\MetaSearchResultInterface;

final class GovernanceSearchResult implements MetaSearchResultInterface
{
    public function __construct(
        private string $type,
        private string $title,
        private string $description,
        private string $url,
    ) {}

    public function getImage(): string { return ''; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): string { return $this->type . ' · ' . $this->description; }
    public function getUrl(): string { return $this->url; }
    public function getType(): string { return $this->type; }
}
