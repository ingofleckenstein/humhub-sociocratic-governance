<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\models\Role;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);

$renderRich = static function ($text): string {
    return class_exists(\humhub\modules\content\widgets\richtext\RichText::class)
        ? \humhub\modules\content\widgets\richtext\RichText::output((string) $text)
        : nl2br(Html::encode((string) $text));
};
$toneClass = $circle ? $circle->colorClass() : 'sg-tone-teal';
$circleUrl = static fn(string $name) => $space->createUrl('/sociocratic-governance/circle/index', ['section' => $name]);
$sections = ['overview' => 'Überblick', 'mandate' => 'Mandat', 'roles' => 'Rollen', 'connections' => 'Verbindungen'];
$byId = [];
foreach ($circles as $item) { $byId[(int) $item->space_id] = $item; }
$canPublish = $canPublish ?? false;
$permanentMemberships = $permanentMemberships ?? [];
?>
<div class="sg <?= Html::encode($toneClass) ?>">
<header class="sg-hero"><span class="sg-eyebrow">Projektkreis</span><h1><?= Html::encode($space->name) ?></h1>
<p>Gemeinsam Verantwortung übernehmen – im vereinbarten Mandat selbstständig handeln.</p>
<div class="sg-actions">
<?= Html::a('Vorhaben', $space->createUrl('/sociocratic-governance/work/index'), ['class' => 'sg-button']) ?>
<?= Html::a('So arbeiten wir', $space->createUrl('/sociocratic-governance/circle/guide'), ['class' => 'sg-button sg-button-secondary']) ?>
<?php if ($canWrite): ?><?= Html::a('Kreisprofil pflegen', $space->createUrl('/sociocratic-governance/circle/edit'), ['class' => 'sg-button sg-button-secondary']) ?><?php endif ?>
<?php if ($canPublish): ?><?= Html::beginForm($space->createUrl('/sociocratic-governance/circle/publish'), 'post', ['class' => 'sg-inline-form']) ?>
<?= Html::submitButton('Space veröffentlichen', ['class' => 'sg-button']) ?>
<?= Html::endForm() ?><?php endif ?>
</div></header>
<nav class="sg-subnav" aria-label="Projektkreis-Bereiche">
<?php foreach ($sections as $key => $label): ?><?= Html::a($label, $circleUrl($key), ['class' => $section === $key ? 'is-active' : '']) ?><?php endforeach ?>
</nav>

<?php if ($section === 'overview'): ?>
<?php if (!$circle): ?><div class="sg-note">Dieser Space ist als Projektkreis aktiviert. Ein Kreismitglied kann nun Zweck und Mandat ergänzen.</div><?php endif ?>
<section class="sg-card"><h2>Wofür sind wir da?</h2>
<div class="sg-text sg-markdown"><?= $renderRich($circle && $circle->purpose !== '' ? $circle->purpose : 'Noch nicht beschrieben.') ?></div>
<?php if ($circle && $circle->mandate_summary): ?><h3>Unser Auftrag in Kürze</h3><p class="sg-summary"><?= Html::encode($circle->mandate_summary) ?></p><?php endif ?>
<p class="sg-muted">Details zu Verantwortung, Befugnissen und Grenzen stehen im Bereich <?= Html::a('Mandat', $circleUrl('mandate')) ?>.</p></section>
<?php endif ?>

<?php if ($section === 'mandate'): ?>
<section class="sg-card"><h2>Mandat</h2>
<?php if ($circle && $circle->mandate_summary): ?><p class="sg-summary"><?= Html::encode($circle->mandate_summary) ?></p><?php endif ?>
<?php foreach (['responsibility' => 'Verantwortung', 'authority' => 'Befugnisse', 'boundaries' => 'Grenzen', 'budget' => 'Budget / Ressourcen', 'reelection_interval' => 'Wiederwahl', 'review' => 'Review'] as $attribute => $label): ?>
<?php if ($circle && trim((string) $circle->$attribute) !== ''): ?><h3><?= Html::encode($label) ?></h3><div class="sg-text sg-markdown"><?= $renderRich($circle->$attribute) ?></div><?php endif ?>
<?php endforeach ?>
<?php if ($circle && trim((string) $circle->mandate) !== ''): ?><h3>Weitere Beschreibung</h3><div class="sg-text sg-markdown"><?= $renderRich($circle->mandate) ?></div><?php endif ?>
<?php if (!$circle || (!$circle->mandate_summary && !$circle->responsibility && !$circle->authority && !$circle->boundaries && !$circle->mandate)): ?><p class="sg-muted">Das Mandat ist noch nicht beschrieben.</p><?php endif ?>
<p class="sg-muted">Mandatsänderungen brauchen den vorgesehenen Beschluss im Oberkreis. Diese Ansicht dokumentiert den aktuellen Stand.</p></section>
<?php endif ?>

<?php if ($section === 'roles'): ?>
<section class="sg-card"><h2>Rollen</h2>
<?php $assigned = []; if ($circle) { foreach ($circle->roles as $role) { $assigned[$role->role_key] = $role; } } ?>
<?php $permanentByUser = []; foreach ($permanentMemberships as $membership): $member = $membership->user; if ($member && $space->isMember($member->id) && (int) $member->status === \humhub\modules\user\models\User::STATUS_ENABLED) { $permanentByUser[(int) $member->id][] = $membership; } endforeach ?>
<?php $roleUserIds = []; ?>
<?php foreach (Role::LABELS as $key => $label): $role = $assigned[$key] ?? null; $user = $role ? $role->user : null; ?>
<div class="sg-role"><strong><?= Html::encode($label) ?></strong><br>
<?php if ($user && $space->isMember($user->id) && (int) $user->status === \humhub\modules\user\models\User::STATUS_ENABLED): $roleUserIds[(int) $user->id] = true; ?><?= Html::a(Html::encode($user->displayName), $user->getUrl()) ?>
<?php foreach ($permanentByUser[(int) $user->id] ?? [] as $membership): ?><br><span class="sg-muted">Dauerhafte Mitgliedschaft: <?= Html::encode($membership->reason) ?></span><?php endforeach ?>
<?php else: ?><span class="sg-muted">Nicht besetzt<?= $role ? ' – Zuordnung prüfen' : '' ?></span><?php endif ?></div>
<?php endforeach ?>
<?php $unassignedPermanent = array_diff_key($permanentByUser, $roleUserIds); if ($unassignedPermanent): ?><h3>Dauerhafte Kreismitglieder</h3>
<?php foreach ($unassignedPermanent as $memberships): foreach ($memberships as $membership): $member = $membership->user; ?><div class="sg-role"><strong>Dauerhaftes Kreismitglied</strong><br>
<?= Html::a(Html::encode($member->displayName), $member->getUrl()) ?><br><span class="sg-muted"><?= Html::encode($membership->reason) ?></span></div><?php endforeach; endforeach ?>
<?php endif ?>
<p class="sg-muted">Besetzungen werden hier dokumentiert. Wahlen und Amtszeiten werden noch nicht automatisch verwaltet.</p></section>
<?php endif ?>

<?php if ($section === 'connections'): ?>
<section class="sg-card"><h2>Verbindungen</h2><p><strong>Oberkreis:</strong>
<?php if ($circle && $circle->parent_space_id && isset($byId[$circle->parent_space_id])): $parent = $byId[$circle->parent_space_id]->space; ?>
<?= Html::a(Html::encode($parent->name), $parent->createUrl('/sociocratic-governance/circle/index')) ?>
<?php elseif ($circle && $circle->parent_space_id): ?>Nicht sichtbar oder nicht aktiv<?php else: ?>Nicht zugeordnet<?php endif ?></p>
<h3>Unterkreise</h3><ul><?php $hasChildren = false; foreach ($circles as $child): if ((int) $child->parent_space_id !== (int) $space->id) { continue; } $hasChildren = true; ?>
<li><?= Html::a(Html::encode($child->space->name), $child->space->createUrl('/sociocratic-governance/circle/index')) ?></li><?php endforeach ?></ul>
<?php if (!$hasChildren): ?><p class="sg-muted">Keine sichtbaren Unterkreise zugeordnet.</p><?php endif ?></section>
<?php endif ?>
</div>
