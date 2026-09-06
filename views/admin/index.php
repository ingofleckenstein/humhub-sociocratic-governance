<?php
use yii\helpers\Html;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
?>
<div class="container sg">
<header class="sg-hero"><span class="sg-eyebrow">Backend · Governance</span><h1>Organisation einrichten</h1><p>Universelle Einstellungen ohne fest eingebaute Personen oder Organisationen.</p></header>
<section class="sg-card">
<?= Html::beginForm('', 'post') ?><?= Html::errorSummary($config, ['class' => 'alert alert-danger']) ?>
<?php foreach (['root_space_id' => $spaces, 'authority_user_id' => $users] as $key => $options): ?>
<?= Html::activeLabel($config, $key) ?><?= Html::activeDropDownList($config, $key, $options, ['prompt' => 'Nicht festgelegt']) ?>
<?php endforeach ?>
<?= Html::activeLabel($config, 'organisation') ?><?= Html::activeTextInput($config, 'organisation', ['maxlength' => 255]) ?>
<p class="sg-note">Nach Benennung der Admin-Sonderrolle darf nur diese Person diese Seite bearbeiten. Ohne Benennung dürfen Systemadministrator*innen die Ersteinrichtung vornehmen. Diese Zuordnung verleiht keine technischen HumHub-Adminrechte.</p>
<?= Html::submitButton('Einstellungen speichern', ['class' => 'sg-button']) ?><?= Html::endForm() ?>
</section>
<section class="sg-card"><h2>Dauerhafte Mitgliedschaft dokumentieren</h2>
<p>Nur bereits bestehende aktive Kreismitglieder können eingetragen werden. Stufe 1 dokumentiert die Vereinbarung und zeigt sie im Profil; sie verhindert noch keinen Austritt und fügt niemanden automatisch einem Space hinzu.</p>
<?= Html::beginForm('', 'post') ?><?= Html::errorSummary($permanent, ['class' => 'alert alert-danger']) ?>
<?= Html::activeLabel($permanent, 'space_id') ?><?= Html::activeDropDownList($permanent, 'space_id', $spaces, ['prompt' => 'Kreis wählen']) ?>
<?= Html::activeLabel($permanent, 'user_id') ?><?= Html::activeDropDownList($permanent, 'user_id', $users, ['prompt' => 'Person wählen']) ?>
<?= Html::activeLabel($permanent, 'reason') ?><?= Html::activeTextInput($permanent, 'reason', ['maxlength' => 255, 'placeholder' => 'Zum Beispiel: dauerhafte Kreisleitung']) ?>
<div class="sg-actions"><?= Html::submitButton('Vereinbarung dokumentieren', ['class' => 'sg-button']) ?></div><?= Html::endForm() ?>
<?php foreach ($declarations as $item): ?>
<div class="sg-role"><?= Html::encode(($item->space ? $item->space->name : 'Entfernter Space') . ' – ' . ($item->user ? $item->user->displayName : 'Entfernte Person') . ' – ' . $item->reason) ?>
<?= Html::beginForm(['remove', 'id' => $item->id], 'post') ?><?= Html::submitButton('Dokumentierte Zuordnung entfernen', ['class' => 'btn btn-default btn-sm']) ?><?= Html::endForm() ?></div>
<?php endforeach ?></section></div>
