<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\models\WorkItem;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$selectedCircleId = $selectedCircleId ?? null;
$circles = $circles ?? [];
$url = static fn(array $params = []) => array_merge(['/sociocratic-governance/dashboard/index'], $selectedCircleId ? ['circle' => $selectedCircleId] : [], $params);
$circleType = static fn($item): string => $item->circle && $item->circle->isCompetenceCircle() ? 'Kompetenzkreis' : 'Projektkreis';
$circleUrl = static fn($item): string => (string) $item->space->createUrl($item->circle && $item->circle->isCompetenceCircle() ? '/space/space/home' : '/sociocratic-governance/circle/index');
$workContext = ['from' => 'participation', 'focus' => $focus];
if ($selectedCircleId) { $workContext['circle'] = $selectedCircleId; }
$workUrl = static fn($item, array $params = []) => $item->space->createUrl('/sociocratic-governance/work/view', array_merge(['id' => $item->id], $workContext, $params));
$selectedCircle = null;
foreach ($circles as $circleFilter) {
    if ((int) $circleFilter['id'] === (int) $selectedCircleId) { $selectedCircle = $circleFilter; break; }
}
?>
<div class="sg">
<header class="sg-hero"><span class="sg-eyebrow">Mitwirken</span><h1>Wie kann ich mich einbringen?</h1>
<p>Hier findest du alle für dich sichtbaren Ideen und Aufgaben – nach Themen oder nach dem, was gerade gebraucht wird.</p>
<?= Html::a('← Zurück zu Start', ['/sociocratic-governance/start/index'], ['class' => 'sg-hero-action']) ?></header>
<nav class="sg-dashboard-switch" aria-label="Ansicht wählen">
<?= Html::a('Nach Themen', $url(['focus' => 'topics', 'topic' => $selectedTopic]), ['class' => 'sg-button' . ($focus === 'topics' ? '' : ' sg-button-secondary')]) ?>
<?= Html::a('Nach Ressourcen', $url(['focus' => 'resources', 'topic' => $selectedTopic, 'resourceType' => $selectedResourceType]), ['class' => 'sg-button' . ($focus === 'resources' ? '' : ' sg-button-secondary')]) ?>
<?= ($selectedTopic || $selectedCircleId) ? Html::a('Filter aufheben', $url(['focus' => $focus, 'topic' => null, 'circle' => null]), ['class' => 'sg-button sg-button-secondary']) : '' ?>
</nav>
<details class="sg-topic-picker"><summary><span>Kreis filtern</span><small><?= $selectedCircle ? Html::encode($selectedCircle['name']) : count($circles) . ' Kreise' ?></small></summary>
<?php if ($circles): ?><div class="sg-topic-overview sg-topic-picker-options"><?= Html::a('Alle Kreise', $url(['circle' => null]), ['class' => 'sg-topic-card' . ($selectedCircleId ? '' : ' is-selected')]) ?><?php foreach ($circles as $circleFilter): ?>
<?= Html::a(Html::encode($circleFilter['name']) . ' <span>' . (int) $circleFilter['count'] . '</span>', $url(['circle' => $circleFilter['id']]), ['class' => 'sg-topic-card' . ((int) $selectedCircleId === (int) $circleFilter['id'] ? ' is-selected' : '')]) ?>
<?php endforeach ?></div><?php else: ?><p class="sg-muted">Für die sichtbaren Kreise gibt es gerade keine offenen Ideen oder Aufgaben.</p><?php endif ?></details>
<?php if ($focus === 'topics'): ?>
<details class="sg-topic-picker"><summary><span>Themen filtern</span><small><?= $selectedTopic ? 'Thema: ' . Html::encode($selectedTopic) : count($topics) . ' Themen' ?></small></summary>
<?php if ($topics): ?><div class="sg-topic-overview sg-topic-picker-options"><?= Html::a('Alle Themen', $url(['focus' => 'topics']), ['class' => 'sg-topic-card' . ($selectedTopic ? '' : ' is-selected')]) ?><?php foreach ($topics as $topic): ?>
<?= Html::a(Html::encode($topic['name']) . ' <span>' . (int) $topic['count'] . '</span>', $url(['focus' => 'topics', 'topic' => $topic['name']]), ['class' => 'sg-topic-card' . (mb_strtolower((string) $selectedTopic) === mb_strtolower($topic['name']) ? ' is-selected' : '')]) ?>
<?php endforeach ?></div><?php else: ?><p class="sg-muted">Noch sind keine Themen vergeben. Kreise können sie beim Erstellen oder Bearbeiten eines Vorhabens ergänzen.</p><?php endif ?></details>
<section class="sg-dashboard-section"><h2><?= $selectedTopic ? 'Vorhaben zum Thema „' . Html::encode($selectedTopic) . '“' : ($selectedCircle ? 'Ideen und Aufgaben im Kreis „' . Html::encode($selectedCircle['name']) . '“' : 'Alle Ideen und Aufgaben') ?></h2>
<div class="sg-dashboard-cards"><?php foreach ($items as $item): ?>
<article class="sg-work-card <?= Html::encode($item->circle ? $item->circle->colorClass() : 'sg-tone-teal') ?>"><?= Html::a('', $workUrl($item, $selectedTopic ? ['topic' => $selectedTopic] : []), ['class' => 'sg-card-hit', 'aria-label' => 'Vorhaben „' . $item->title . '“ öffnen']) ?><span class="sg-eyebrow"><?= $item->kind === 'idea' ? 'Idee eingereicht' : 'Aufgabe · ' . Html::encode(WorkItem::STATUSES[$item->status]) ?></span>
<h3><?= Html::a(Html::encode($item->title), $workUrl($item, $selectedTopic ? ['topic' => $selectedTopic] : [])) ?></h3>
<p class="sg-work-circle"><?= $circleType($item) ?>: <?= Html::a(Html::encode($item->space->name), $circleUrl($item), ['aria-label' => $circleType($item) . ' ' . $item->space->name . ' öffnen']) ?></p>
<p><?= Html::encode($item->assignee ? 'Verantwortlich: ' . $item->assignee->displayName : 'Noch nicht übernommen') ?></p>
<?php if ($item->topics): ?><p class="sg-tags"><?php foreach ($item->topics as $workTopic): ?><span><?= Html::encode($workTopic->name) ?></span><?php endforeach ?></p><?php endif ?>
</article><?php endforeach ?></div>
<?php if (!$items): ?><p class="sg-note">Für diesen Filter sind keine sichtbaren Vorhaben vorhanden.</p><?php endif ?></section>
<?php else: ?>
<section class="sg-dashboard-section"><h2>Ressourcen, die gerade gebraucht werden</h2>
<p>Kreise schätzen den Bedarf. Mitglieder sagen Beiträge zu; sichtbar bleibt nur der gemeinsame Stand.</p>
<nav class="sg-resource-filter" aria-label="Ressourcen nach eigenem Beitrag filtern"><span>Ich kann beitragen:</span>
<?= Html::a('Alles <span>' . array_sum($resourceTypeCounts) . '</span>', $url(['focus' => 'resources', 'topic' => $selectedTopic]), ['class' => 'sg-resource-filter-button' . ($selectedResourceType === null ? ' is-selected' : '')]) ?>
<?php foreach (\humhub\modules\sociocraticGovernance\models\WorkResource::TYPES as $type => $label): $filterLabel = ['money' => 'Geld', 'time' => 'Zeit', 'expertise' => 'Erfahrung & Wissen', 'material' => 'Material', 'other' => 'Sonstiges'][$type]; ?>
<?= Html::a(Html::encode($filterLabel) . ' <span>' . (int) $resourceTypeCounts[$type] . '</span>', $url(['focus' => 'resources', 'topic' => $selectedTopic, 'resourceType' => $type]), ['class' => 'sg-resource-filter-button' . ($selectedResourceType === $type ? ' is-selected' : '')]) ?>
<?php endforeach ?></nav>
<?php if ($selectedResourceType): ?><p class="sg-muted">Du siehst nur Bedarfe für <strong><?= Html::encode(['money' => 'Geld', 'time' => 'Zeit', 'expertise' => 'Erfahrung und Wissen', 'material' => 'Material', 'other' => 'Sonstiges'][$selectedResourceType]) ?></strong>.</p><?php endif ?>
<div class="sg-dashboard-cards"><?php foreach ($resources as $entry): $resource = $entry['resource']; $item = $entry['item']; ?>
<article class="sg-resource <?= Html::encode($item->circle ? $item->circle->colorClass() : 'sg-tone-teal') ?>"><?= Html::a('', $workUrl($item, ['section' => 'resources', 'resourceType' => $selectedResourceType]), ['class' => 'sg-card-hit', 'aria-label' => 'Ressourcen von „' . $item->title . '“ öffnen']) ?><span class="sg-eyebrow"><?= Html::encode(\humhub\modules\sociocraticGovernance\models\WorkResource::TYPES[$resource->resource_type]) ?> · <?= Html::encode($item->space->name) ?></span>
<h3><?= Html::a(Html::encode($resource->label), $workUrl($item, ['section' => 'resources', 'resourceType' => $selectedResourceType])) ?></h3>
<p>Für: <?= Html::a(Html::encode($item->title), $workUrl($item, ['section' => 'resources', 'resourceType' => $selectedResourceType])) ?></p>
<p><?= $circleType($item) ?>: <?= Html::a(Html::encode($item->space->name), $circleUrl($item), ['aria-label' => $circleType($item) . ' ' . $item->space->name . ' öffnen']) ?></p>
<?php if ($resource->resource_type === 'time' && $resource->time_mode): ?><p class="sg-muted"><strong><?= Html::encode(\humhub\modules\sociocraticGovernance\models\WorkResource::TIME_MODES[$resource->time_mode]) ?>:</strong> <?= Html::encode($resource->time_pattern) ?></p><?php endif ?>
<?php if ($resource->required_amount !== null): ?><p><strong><?= Html::encode((string) $resource->totalCommittedAmount) ?></strong> von <?= Html::encode((string) $resource->required_amount) ?> <?= Html::encode($resource->unit) ?> zugesagt</p>
<div class="sg-progress" role="progressbar" aria-label="Ressourcenstand" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= Html::encode((string) $resource->progress) ?>"><span style="width:<?= Html::encode((string) $resource->progress) ?>%"></span></div><?php endif ?>
<?php if ($resource->details): ?><p><?= Html::encode($resource->details) ?></p><?php endif ?>
</article><?php endforeach ?></div>
<?php if (!$resources): ?><p class="sg-note"><?= $selectedResourceType ? 'Für diesen Beitrag gibt es gerade keinen sichtbaren Bedarf.' : 'Es sind derzeit keine Ressourcenbedarfe eingetragen.' ?></p><?php endif ?></section>
<?php endif ?></div>
