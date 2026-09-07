<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\models\WorkItem;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$url = static fn(array $params = []) => array_merge(['/sociocratic-governance/dashboard/index'], $params);
?>
<div class="sg">
<header class="sg-hero"><span class="sg-eyebrow">Mitwirken</span><h1>Wie kann ich mich einbringen?</h1>
<p>Hier findest du alle für dich sichtbaren Ideen und Aufgaben – nach Themen oder nach dem, was gerade gebraucht wird.</p></header>
<nav class="sg-dashboard-switch" aria-label="Ansicht wählen">
<?= Html::a('Nach Themen', $url(['focus' => 'topics', 'topic' => $selectedTopic]), ['class' => 'sg-button' . ($focus === 'topics' ? '' : ' sg-button-secondary')]) ?>
<?= Html::a('Nach Ressourcen', $url(['focus' => 'resources', 'topic' => $selectedTopic]), ['class' => 'sg-button' . ($focus === 'resources' ? '' : ' sg-button-secondary')]) ?>
<?= $selectedTopic ? Html::a('Themenfilter aufheben', $url(['focus' => $focus]), ['class' => 'sg-button sg-button-secondary']) : '' ?>
</nav>
<?php if ($focus === 'topics'): ?>
<section class="sg-card"><h2>Themen</h2>
<?php if ($topics): ?><div class="sg-topic-overview"><?php foreach ($topics as $topic): ?>
<?= Html::a(Html::encode($topic['name']) . ' <span>' . (int) $topic['count'] . '</span>', $url(['focus' => 'topics', 'topic' => $topic['name']]), ['class' => 'sg-topic-card' . (mb_strtolower((string) $selectedTopic) === mb_strtolower($topic['name']) ? ' is-selected' : '')]) ?>
<?php endforeach ?></div><?php else: ?><p class="sg-muted">Noch sind keine Themen vergeben. Arbeitskreise können sie beim Erstellen oder Bearbeiten eines Vorhabens ergänzen.</p><?php endif ?>
</section>
<section><h2><?= $selectedTopic ? 'Vorhaben zum Thema „' . Html::encode($selectedTopic) . '“' : 'Alle Ideen und Aufgaben' ?></h2>
<div class="sg-dashboard-cards"><?php foreach ($items as $item): ?>
<article class="sg-work-card <?= Html::encode($item->circle ? $item->circle->colorClass() : 'sg-tone-teal') ?>"><?= Html::a('', $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id]), ['class' => 'sg-card-hit', 'aria-label' => 'Vorhaben „' . $item->title . '“ öffnen']) ?><span class="sg-eyebrow"><?= $item->kind === 'idea' ? 'Idee eingereicht' : 'Aufgabe · ' . Html::encode(WorkItem::STATUSES[$item->status]) ?></span>
<h3><?= Html::a(Html::encode($item->title), $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id])) ?></h3>
<p class="sg-work-circle">Arbeitskreis: <?= Html::a(Html::encode($item->space->name), $item->space->createUrl('/sociocratic-governance/circle/index'), ['aria-label' => 'Arbeitskreis ' . $item->space->name . ' öffnen']) ?></p>
<p><?= Html::encode($item->assignee ? 'Verantwortlich: ' . $item->assignee->displayName : 'Noch nicht übernommen') ?></p>
<?php if ($item->topics): ?><p class="sg-tags"><?php foreach ($item->topics as $workTopic): ?><span><?= Html::encode($workTopic->name) ?></span><?php endforeach ?></p><?php endif ?>
</article><?php endforeach ?></div>
<?php if (!$items): ?><p class="sg-note">Für diesen Filter sind keine sichtbaren Vorhaben vorhanden.</p><?php endif ?></section>
<?php else: ?>
<section><h2>Ressourcen, die gerade gebraucht werden</h2>
<p>Arbeitskreise schätzen den Bedarf. Mitglieder sagen Beiträge zu; sichtbar bleibt nur der gemeinsame Stand.</p>
<div class="sg-dashboard-cards"><?php foreach ($resources as $entry): $resource = $entry['resource']; $item = $entry['item']; ?>
<article class="sg-resource <?= Html::encode($item->circle ? $item->circle->colorClass() : 'sg-tone-teal') ?>"><?= Html::a('', $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id]), ['class' => 'sg-card-hit', 'aria-label' => 'Vorhaben „' . $item->title . '“ öffnen']) ?><span class="sg-eyebrow"><?= Html::encode(\humhub\modules\sociocraticGovernance\models\WorkResource::TYPES[$resource->resource_type]) ?> · <?= Html::encode($item->space->name) ?></span>
<h3><?= Html::a(Html::encode($resource->label), $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id])) ?></h3>
<p>Für: <?= Html::a(Html::encode($item->title), $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id])) ?></p>
<p>Arbeitskreis: <?= Html::a(Html::encode($item->space->name), $item->space->createUrl('/sociocratic-governance/circle/index'), ['aria-label' => 'Arbeitskreis ' . $item->space->name . ' öffnen']) ?></p>
<?php if ($resource->resource_type === 'time' && $resource->time_mode): ?><p class="sg-muted"><strong><?= Html::encode(\humhub\modules\sociocraticGovernance\models\WorkResource::TIME_MODES[$resource->time_mode]) ?>:</strong> <?= Html::encode($resource->time_pattern) ?></p><?php endif ?>
<?php if ($resource->required_amount !== null): ?><p><strong><?= Html::encode((string) $resource->totalCommittedAmount) ?></strong> von <?= Html::encode((string) $resource->required_amount) ?> <?= Html::encode($resource->unit) ?> zugesagt</p>
<div class="sg-progress" role="progressbar" aria-label="Ressourcenstand" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= Html::encode((string) $resource->progress) ?>"><span style="width:<?= Html::encode((string) $resource->progress) ?>%"></span></div><?php endif ?>
<?php if ($resource->details): ?><p><?= Html::encode($resource->details) ?></p><?php endif ?>
</article><?php endforeach ?></div>
<?php if (!$resources): ?><p class="sg-note">Es sind derzeit keine Ressourcenbedarfe eingetragen.</p><?php endif ?></section>
<?php endif ?></div>
