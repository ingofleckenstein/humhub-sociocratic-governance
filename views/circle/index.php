<?php
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\models\Role;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$renderRich = static function ($text): string {
    if (class_exists(\humhub\modules\content\widgets\richtext\RichText::class)) {
        return \humhub\modules\content\widgets\richtext\RichText::output((string) $text);
    }
    return nl2br(Html::encode((string) $text));
};
$byId = [];
foreach ($circles as $item) { $byId[(int) $item->space_id] = $item; }
?>
<div class="sg">
<header class="sg-hero">
<span class="sg-eyebrow">Arbeitskreis · Orientierung</span><h1><?= Html::encode($space->name) ?></h1>
<p>Gemeinsam Verantwortung übernehmen. Im vereinbarten Mandat selbstständig handeln.</p>
<div class="sg-actions">
<?= Html::a('So arbeiten wir', $space->createUrl('/sociocratic-governance/circle/guide'), ['class' => 'sg-button']) ?>
<?= Html::a('Kreisübersicht', ['/sociocratic-governance/directory/index'], ['class' => 'sg-button']) ?>
<?php if ($canWrite): ?><?= Html::a('Mandat & Rollen pflegen', $space->createUrl('/sociocratic-governance/circle/edit'), ['class' => 'sg-button']) ?><?php endif ?>
</div></header>
<?php if (!$circle): ?><div class="sg-note">Dieser Space ist als Arbeitskreis aktiviert. Ein Kreismitglied kann jetzt Zweck, Mandat und Rollen eintragen.</div><?php endif ?>
<div class="sg-grid">
<section class="sg-card"><h2>Unser Zweck</h2>
<div class="sg-text sg-markdown"><?= $renderRich($circle && $circle->purpose !== '' ? $circle->purpose : 'Noch nicht beschrieben.') ?></div>
<h3>Unser Mandat</h3>
<?php if ($circle && $circle->mandate_summary): ?><p class="sg-summary"><?= Html::encode($circle->mandate_summary) ?></p><?php endif ?>
<?php foreach (['responsibility' => 'Verantwortung', 'authority' => 'Befugnisse', 'boundaries' => 'Grenzen', 'budget' => 'Budget / Ressourcen', 'reelection_interval' => 'Wiederwahl', 'review' => 'Review'] as $attribute => $label): ?>
<?php if ($circle && trim((string) $circle->$attribute) !== ''): ?><h4><?= Html::encode($label) ?></h4><div class="sg-text sg-markdown"><?= $renderRich($circle->$attribute) ?></div><?php endif ?>
<?php endforeach ?>
<?php if ($circle && trim((string) $circle->mandate) !== ''): ?><h4>Weitere Beschreibung</h4><div class="sg-text sg-markdown"><?= $renderRich($circle->mandate) ?></div><?php endif ?>
<?php if (!$circle || (!$circle->mandate_summary && !$circle->responsibility && !$circle->authority && !$circle->boundaries && !$circle->mandate)): ?><p class="sg-muted">Welche Verantwortung, Befugnisse und Grenzen gelten?</p><?php endif ?>
<p class="sg-muted">Mandatsänderungen brauchen den vorgesehenen Beschluss im Oberkreis. Diese erste Version dokumentiert Angaben, sie führt noch kein Konsentverfahren aus.</p></section>
<section class="sg-card"><h2>Unsere Rollen</h2>
<?php
$assigned = [];
if ($circle) { foreach ($circle->roles as $role) { $assigned[$role->role_key] = $role; } }
foreach (Role::LABELS as $key => $label):
$role = $assigned[$key] ?? null; $user = $role ? $role->user : null;
?>
<div class="sg-role"><strong><?= Html::encode($label) ?></strong><br>
<?php if ($user && $space->isMember($user->id) && (int) $user->status === \humhub\modules\user\models\User::STATUS_ENABLED): ?>
<?= Html::a(Html::encode($user->displayName), $user->getUrl()) ?>
<?php else: ?><span class="sg-muted">Nicht besetzt<?= $role ? ' – Zuordnung prüfen' : '' ?></span><?php endif ?>
</div>
<?php endforeach ?>
<p class="sg-muted">Manuell dokumentierte Besetzungen. Wahlen und Amtszeiten werden noch nicht automatisch verwaltet.</p></section></div>
<section class="sg-card"><h2>Unsere Verbindungen</h2><p><strong>Oberkreis:</strong>
<?php if ($circle && $circle->parent_space_id && isset($byId[$circle->parent_space_id])):
$parent = $byId[$circle->parent_space_id]->space; ?>
<?= Html::a(Html::encode($parent->name), $parent->createUrl('/sociocratic-governance/circle/index')) ?>
<?php elseif ($circle && $circle->parent_space_id): ?>Nicht sichtbar oder nicht aktiv
<?php else: ?>Nicht zugeordnet<?php endif ?></p>
<h3>Unterkreise</h3><ul>
<?php $hasChildren = false; foreach ($circles as $child):
if ((int) $child->parent_space_id !== (int) $space->id) { continue; } $hasChildren = true; ?>
<li><?= Html::a(Html::encode($child->space->name), $child->space->createUrl('/sociocratic-governance/circle/index')) ?></li>
<?php endforeach ?></ul>
<?php if (!$hasChildren): ?><p class="sg-muted">Keine sichtbaren Unterkreise zugeordnet.</p><?php endif ?>
</section></div>
