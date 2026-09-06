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
<?php if (class_exists(\humhub\modules\content\widgets\richtext\RichTextField::class)): ?>
<?= \humhub\modules\content\widgets\richtext\RichTextField::widget(['model' => $form, 'attribute' => 'purpose', 'preset' => 'markdown', 'exclude' => ['upload', 'oembed', 'mention'], 'placeholder' => 'Wofür gibt es diesen Kreis?']) ?>
<?php else: ?><?= Html::activeTextarea($form, 'purpose', ['rows' => 3, 'maxlength' => 20000]) ?><?php endif ?>
<h2 style="margin-top:24px">Mandat</h2>
<p class="sg-muted">Markdown und Emojis werden unterstützt. Die Kurzform erscheint in der Kreisübersicht.</p>
<?= Html::activeLabel($form, 'mandate_summary') ?>
<?= Html::activeTextInput($form, 'mandate_summary', ['maxlength' => 255, 'placeholder' => 'Maximal 255 Zeichen']) ?>
<?php foreach (['responsibility' => 4, 'authority' => 4, 'boundaries' => 4, 'budget' => 3, 'review' => 3] as $attribute => $rows): ?>
<?= Html::activeLabel($form, $attribute) ?>
<?php if (class_exists(\humhub\modules\content\widgets\richtext\RichTextField::class)): ?>
<?= \humhub\modules\content\widgets\richtext\RichTextField::widget(['model' => $form, 'attribute' => $attribute, 'preset' => 'markdown', 'exclude' => ['upload', 'oembed', 'mention']]) ?>
<?php else: ?><?= Html::activeTextarea($form, $attribute, ['rows' => $rows, 'maxlength' => 20000]) ?><?php endif ?>
<?php endforeach ?>
<?= Html::activeLabel($form, 'reelection_interval') ?>
<?= Html::activeTextInput($form, 'reelection_interval', ['maxlength' => 255]) ?>
<?= Html::activeLabel($form, 'mandate') ?>
<?php if (class_exists(\humhub\modules\content\widgets\richtext\RichTextField::class)): ?>
<?= \humhub\modules\content\widgets\richtext\RichTextField::widget(['model' => $form, 'attribute' => 'mandate', 'preset' => 'markdown', 'exclude' => ['upload', 'oembed', 'mention'], 'placeholder' => 'Bestehende oder ergänzende Beschreibung']) ?>
<?php else: ?><?= Html::activeTextarea($form, 'mandate', ['rows' => 5, 'maxlength' => 20000]) ?><?php endif ?>
<?= Html::activeLabel($form, 'parent_space_id') ?>
<?= Html::activeDropDownList($form, 'parent_space_id', $parents, ['prompt' => 'Kein Oberkreis']) ?>
<h2 style="margin-top:24px">Rollen zuordnen</h2>
<p>Kreisleitung und Delegierte*r sind immer verschiedene Personen. Nur vorhandene aktive Kreismitglieder können zugeordnet werden. Die Kreisleitung wird Space-Besitzer*in; diese Änderung darf nur durch Besitzer*innen oder Administrator*innen erfolgen. Beim Rollenwechsel entsteht noch keine automatische Mitgliedschaft im Oberkreis.</p>
<div class="sg-grid">
<?php foreach (\humhub\modules\sociocraticGovernance\models\Role::LABELS as $key => $label): ?>
<div><?= Html::activeLabel($form, $key) ?><?= Html::activeDropDownList($form, $key, $members, ['prompt' => 'Nicht besetzt']) ?></div>
<?php endforeach ?>
</div>
<div class="sg-actions"><?= Html::submitButton('Angaben speichern', ['class' => 'sg-button']) ?>
<?= Html::a('Abbrechen', $space->createUrl('/sociocratic-governance/circle/index')) ?></div>
<?= Html::endForm() ?>
</section></div>
