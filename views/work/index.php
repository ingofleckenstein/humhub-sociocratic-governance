<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\models\WorkItem;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$circle = \humhub\modules\sociocraticGovernance\models\Circle::findOne($space->id);
$toneClass = $circle ? $circle->colorClass() : 'sg-tone-teal';
$circleUrl = $space->createUrl($circle && $circle->isCompetenceCircle() ? '/space/space/home' : '/sociocratic-governance/circle/index');
?>
<div class="sg <?= Html::encode($toneClass) ?>">
<header class="sg-hero"><span class="sg-eyebrow">Vorhaben · <?= Html::encode($space->name) ?></span><h1>Board</h1>
<p>Was ist offen, in Bearbeitung oder wartet auf eine Abnahme?</p>
<?= Html::a($circle && $circle->isCompetenceCircle() ? '← Zurück zum Space' : '← Zurück zum Kreis', $circleUrl, ['class' => 'sg-hero-action']) ?></header>
<nav class="sg-subnav" aria-label="Vorhaben-Bereiche">
<?= Html::a('Board', $space->createUrl('/sociocratic-governance/work/index'), ['class' => 'is-active']) ?>
<?= Html::a('Neues Vorhaben', $space->createUrl('/sociocratic-governance/work/new')) ?>
<?= Html::a($circle && $circle->isCompetenceCircle() ? 'Zum Space' : 'Zum Projektkreis', $circleUrl) ?>
</nav>
<div class="sg-work-board" aria-label="Vorhaben nach Bearbeitungsstand">
<?php foreach (WorkItem::STATUSES as $status => $label): $column = array_filter($items, static fn($item) => $item->status === $status); ?>
<section class="sg-work-column" aria-labelledby="work-column-<?= Html::encode($status) ?>"><h2 id="work-column-<?= Html::encode($status) ?>"><?= Html::encode($label) ?> <span class="sg-muted">(<?= count($column) ?>)</span></h2>
<?php foreach ($column as $item): ?><article class="sg-work-card <?= Html::encode($item->circle ? $item->circle->colorClass() : $toneClass) ?>"><span class="sg-eyebrow">#<?= (int) $item->id ?> · <?= $item->kind === 'idea' ? 'Idee' : 'Aufgabe' ?></span>
<h3><?= Html::a(Html::encode($item->title), $space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id])) ?></h3>
<p><?= Html::encode($item->assignee ? 'Verantwortlich: ' . $item->assignee->displayName : 'Noch nicht übernommen') ?></p>
<?php if ($item->resources): ?><p class="sg-muted"><?= count($item->resources) ?> Ressourcenbedarf<?= count($item->resources) === 1 ? '' : 'e' ?></p><?php endif ?></article><?php endforeach ?>
<?php if (!$column): ?><p class="sg-muted">Keine Vorhaben.</p><?php endif ?></section><?php endforeach ?>
</div>
<p class="sg-note">Ein neues Vorhaben wird auf einer eigenen Seite angelegt. <?= Html::a('Neues Vorhaben öffnen', $space->createUrl('/sociocratic-governance/work/new')) ?></p>
</div>
