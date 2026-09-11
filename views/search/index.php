<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
?>
<div class="sg"><header class="sg-hero"><span class="sg-eyebrow">Suche</span><h1>Kreise und Mitwirken durchsuchen</h1><?= Html::a('← Zurück zu Start', ['/sociocratic-governance/start/index'], ['class' => 'sg-hero-action']) ?>
<?= Html::beginForm(['/sociocratic-governance/search/index'], 'get', ['class' => 'sg-search-form']) ?><?= Html::textInput('keyword', $keyword, ['aria-label' => 'Kreise, Vorhaben oder Ressourcen suchen', 'maxlength' => 120, 'placeholder' => 'Kreis, Vorhaben oder Ressource']) ?><?= Html::submitButton('Suchen', ['class' => 'sg-button']) ?><?= Html::endForm() ?></header>
<?php if ($keyword === ''): ?><p class="sg-note">Gib einen Suchbegriff ein.</p><?php elseif (!$results): ?><p class="sg-note">Zu „<?= Html::encode($keyword) ?>“ gibt es keine sichtbaren Governance-Ergebnisse.</p><?php else: ?><p class="sg-muted"><?= (int) $totalCount ?> Ergebnis<?= $totalCount === 1 ? '' : 'se' ?> zu „<?= Html::encode($keyword) ?>“</p><div class="sg-dashboard-cards"><?php foreach ($results as $result): ?><article class="sg-work-card"><span class="sg-eyebrow"><?= Html::encode($result->getType()) ?></span><h2><?= Html::a(Html::encode($result->getTitle()), $result->getUrl()) ?></h2><p><?= Html::encode(substr($result->getDescription(), strlen($result->getType() . ' · '))) ?></p></article><?php endforeach ?></div><?php endif ?></div>
