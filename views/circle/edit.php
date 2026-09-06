<?php
use yii\helpers\Html;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
?>
<div class="sg">
<header class="sg-hero"><span class="sg-eyebrow">Arbeitskreis pflegen</span><h1><?= Html::encode($space->name) ?></h1>
<p>Dokumentiere die gemeinsam vereinbarten Angaben. Das Speichern ersetzt keinen Konsentbeschluss.</p></header>
<section class="sg-card">
<?= Html::beginForm('', 'post') ?>
<?= Html::errorSummary($form, ['class' => 'alert alert-danger', 'header' => 'Bitte prüfen:']) ?>
<?= Html::activeHiddenInput($form, 'revision') ?>
<?= Html::activeLabel($form, 'purpose') ?>
<?= Html::activeTextarea($form, 'purpose', ['rows' => 3, 'maxlength' => 20000]) ?>
<?= Html::activeLabel($form, 'mandate') ?>
<?= Html::activeTextarea($form, 'mandate', ['rows' => 8, 'maxlength' => 20000, 'placeholder' => "Verantwortung:\nBefugnisse:\nGrenzen / Budget:\nWiederwahl: standardmäßig alle 6 Monate\nReview:"]) ?>
<?= Html::activeLabel($form, 'parent_space_id') ?>
<?= Html::activeDropDownList($form, 'parent_space_id', $parents, ['prompt' => 'Kein Oberkreis']) ?>
<h2 style="margin-top:24px">Rollen zuordnen</h2>
<p>Kreisleitung und Delegierte*r sind immer verschiedene Personen. Nur vorhandene aktive Kreismitglieder können zugeordnet werden. Beim Rollenwechsel entsteht noch keine automatische Mitgliedschaft im Oberkreis.</p>
<div class="sg-grid">
<?php foreach (\humhub\modules\sociocraticGovernance\models\Role::LABELS as $key => $label): ?>
<div><?= Html::activeLabel($form, $key) ?><?= Html::activeDropDownList($form, $key, $members, ['prompt' => 'Nicht besetzt']) ?></div>
<?php endforeach ?>
</div>
<div class="sg-actions"><?= Html::submitButton('Angaben speichern', ['class' => 'sg-button']) ?>
<?= Html::a('Abbrechen', $space->createUrl('/sociocratic-governance/circle/index')) ?></div>
<?= Html::endForm() ?>
</section></div>
