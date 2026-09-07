<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\models\WorkItem;
use humhub\modules\sociocraticGovernance\services\Access;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$draftTopics = $draftTopics ?? '';
$toneClass = $space && ($circle = \humhub\modules\sociocraticGovernance\models\Circle::findOne($space->id)) ? $circle->colorClass() : 'sg-tone-teal';
?>
<div class="sg <?= Html::encode($toneClass) ?>">
<header class="sg-hero"><span class="sg-eyebrow">Vorhaben · <?= Html::encode($space->name) ?></span><h1>Ideen und Aufgaben</h1>
<p>Ideen einreichen, Verantwortung übernehmen und Ergebnisse gemeinsam abnehmen.</p>
<?= Html::a('Zum Kreisprofil', $space->createUrl('/sociocratic-governance/circle/index'), ['class' => 'sg-button']) ?></header>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= Html::encode($error) ?></p><?php endif ?>
<section class="sg-card"><h2>Neues Vorhaben</h2>
<?= Html::beginForm($space->createUrl('/sociocratic-governance/work/create'), 'post') ?>
<?= Html::activeLabel($draft, 'title') ?><?= Html::activeTextInput($draft, 'title', ['required' => true, 'maxlength' => 255]) ?>
<?= Html::activeLabel($draft, 'description') ?><?= Html::activeTextarea($draft, 'description', ['required' => true, 'maxlength' => 20000, 'rows' => 4]) ?>
<?= Html::label('Themen', 'work-topics') ?><?= Html::textInput('WorkItem[topics]', $draftTopics, ['id' => 'work-topics', 'maxlength' => 1000, 'placeholder' => 'z. B. Bildung, Infrastruktur, Fürsorge']) ?>
<p class="sg-muted">Kommagetrennte Themen machen das Vorhaben im Mitwirkungs-Dashboard auffindbar.</p>
<?php if (Access::write($space)): ?>
<?= Html::label('Art', 'work-kind') ?><?= Html::dropDownList('WorkItem[kind]', $draft->kind ?: 'idea', ['idea' => 'Idee zur Mandatsprüfung', 'task' => 'Aufgabe im bestehenden Mandat'], ['id' => 'work-kind']) ?>
<?php endif ?>
<p>Alle Communitymitglieder mit Lesezugang können Ideen einreichen. Die Annahme übernehmen Kreisleitung oder Delegierte*r.</p>
<?= Html::submitButton('Vorhaben erstellen', ['class' => 'sg-button']) ?><?= Html::endForm() ?></section>
<div class="sg-work-board" aria-label="Vorhaben nach Bearbeitungsstand">
<?php foreach (WorkItem::STATUSES as $status => $label): $column = array_filter($items, static fn($item) => $item->status === $status); ?>
<section class="sg-work-column" aria-labelledby="work-column-<?= Html::encode($status) ?>">
<h2 id="work-column-<?= Html::encode($status) ?>"><?= Html::encode($label) ?> <span class="sg-muted">(<?= count($column) ?>)</span></h2>
<?php foreach ($column as $item): ?>
<article class="sg-work-card <?= Html::encode($item->circle ? $item->circle->colorClass() : $toneClass) ?>"><span class="sg-eyebrow">#<?= (int) $item->id ?> · <?= $item->kind === 'idea' ? 'Idee' : 'Aufgabe' ?></span>
<h3><?= Html::a(Html::encode($item->title), $space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id])) ?></h3>
<p><?= Html::encode($item->assignee ? 'Verantwortlich: ' . $item->assignee->displayName : 'Noch nicht übernommen') ?></p>
<?php if ($item->topics): ?><p class="sg-tags"><?php foreach ($item->topics as $topic): ?><span><?= Html::encode($topic->name) ?></span><?php endforeach ?></p><?php endif ?>
<?php if ($item->resources): ?><p class="sg-muted"><?= count($item->resources) ?> Ressourcenbedarf<?= count($item->resources) === 1 ? '' : 'e' ?></p><?php endif ?>
<?php if ($status === 'review'): ?><p>Wartet auf die Abnahme durch Kreisleitung und Delegierte*n.</p><?php endif ?>
</article>
<?php endforeach ?>
<?php if (!$column): ?><p class="sg-muted">Keine Vorhaben.</p><?php endif ?>
</section>
<?php endforeach ?></div>
<p class="sg-note">Statuswechsel erfolgen in der Vorhabenkarte über beschriftete Schaltflächen. „Abgenommen“ bestätigt die Arbeit; es ersetzt keinen Konsentbeschluss. <?= Html::a('Wie kann ich mich einbringen?', ['/sociocratic-governance/dashboard/index']) ?></p>
</div>
