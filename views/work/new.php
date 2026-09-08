<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\services\Access;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$circle = \humhub\modules\sociocraticGovernance\models\Circle::findOne($space->id);
$toneClass = $circle ? $circle->colorClass() : 'sg-tone-teal';
?>
<div class="sg <?= Html::encode($toneClass) ?>">
<header class="sg-hero"><span class="sg-eyebrow">Vorhaben · <?= Html::encode($space->name) ?></span><h1>Neues Vorhaben</h1>
<p>Eine Idee zur gemeinsamen Prüfung oder eine konkrete Aufgabe anlegen.</p></header>
<nav class="sg-subnav" aria-label="Vorhaben-Bereiche">
<?= Html::a('Board', $space->createUrl('/sociocratic-governance/work/index')) ?>
<?= Html::a('Neues Vorhaben', $space->createUrl('/sociocratic-governance/work/new'), ['class' => 'is-active']) ?>
</nav>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= Html::encode($error) ?></p><?php endif ?>
<section class="sg-card"><h2>Worum geht es?</h2><?= Html::beginForm($space->createUrl('/sociocratic-governance/work/create'), 'post') ?>
<?= Html::activeLabel($draft, 'title') ?><?= Html::activeTextInput($draft, 'title', ['required' => true, 'maxlength' => 255]) ?>
<?= Html::activeLabel($draft, 'description') ?><?= Html::activeTextarea($draft, 'description', ['required' => true, 'maxlength' => 20000, 'rows' => 5]) ?>
<?= Html::label('Themen', 'work-topics') ?><?= Html::textInput('WorkItem[topics]', $draftTopics, ['id' => 'work-topics', 'maxlength' => 1000, 'placeholder' => 'z. B. Auszeit, Teilhabe, Raumhaltung']) ?>
<p class="sg-muted">Kommagetrennte Themen machen das Vorhaben im Mitwirkungs-Dashboard auffindbar.</p>
<?php if (Access::write($space)): ?><?= Html::label('Art', 'work-kind') ?><?= Html::dropDownList('WorkItem[kind]', $draft->kind ?: 'idea', ['idea' => 'Neue Idee oder Vorschlag – zuerst gemeinsam prüfen', 'task' => 'Konkrete Aufgabe – der Kreis ist bereits zuständig'], ['id' => 'work-kind', 'aria-describedby' => 'work-kind-help']) ?><?php endif ?>
<div id="work-kind-help" class="sg-note"><p><strong>Idee:</strong> Der Kreis prüft zuerst gemeinsam, ob sie zu seinem Auftrag passt.</p><p><strong>Aufgabe:</strong> Der Kreis ist bereits zuständig; sie kann danach übernommen werden.</p></div>
<?= Html::submitButton('Vorhaben erstellen', ['class' => 'sg-button']) ?><?= Html::endForm() ?></section>
</div>
