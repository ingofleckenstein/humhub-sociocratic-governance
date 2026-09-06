<?php
use yii\helpers\Html;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$byId = [];
foreach ($circles as $circle) { $byId[(int) $circle->space_id] = $circle; }
?>
<div class="container sg">
<header class="sg-hero"><span class="sg-eyebrow">Organisation</span><h1>Unsere Arbeitskreise</h1><p>Welche Verantwortung liegt wo? Die Übersicht zeigt die für dich sichtbaren, eingerichteten Kreise.</p></header>
<div class="sg-grid">
<?php foreach ($circles as $circle): ?>
<section class="sg-card"><span class="sg-eyebrow">Arbeitskreis<?= $circle->space->isArchived() ? ' · archiviert' : '' ?></span>
<h2><?= Html::a(Html::encode($circle->space->name), $circle->space->createUrl('/sociocratic-governance/circle/index')) ?></h2>
<p class="sg-text"><?= Html::encode($circle->purpose ?: 'Zweck noch nicht beschrieben.') ?></p>
<p class="sg-muted">Oberkreis: <?= Html::encode($circle->parent_space_id ? (isset($byId[$circle->parent_space_id]) ? $byId[$circle->parent_space_id]->space->name : 'Nicht sichtbar oder nicht aktiv') : 'Nicht zugeordnet') ?></p>
</section>
<?php endforeach ?></div>
<?php if (!$circles): ?><section class="sg-card">Noch keine sichtbaren Kreisprofile. Aktiviere das Modul in einem Space und pflege dort das Kreisprofil.</section><?php endif ?>
</div>
