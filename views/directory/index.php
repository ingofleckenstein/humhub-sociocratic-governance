<?php
use yii\helpers\Html;
use yii\helpers\Json;
use humhub\modules\sociocraticGovernance\models\Role;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);

$avatar = static function ($user): string {
    if (!$user) { return ''; }
    if (class_exists(\humhub\modules\user\widgets\Image::class)) {
        return \humhub\modules\user\widgets\Image::widget(['user' => $user, 'width' => 32, 'height' => 32, 'linkOptions' => ['class' => 'sg-avatar-link']]);
    }
    return Html::a(Html::tag('span', Html::encode(mb_substr($user->displayName, 0, 1)), ['class' => 'sg-avatar']), $user->getUrl());
};
$activeRoles = static function ($circle, ?array $keys = null): array {
    $roles = [];
    foreach ($circle->roles as $role) {
        if (($keys !== null && !in_array($role->role_key, $keys, true)) || !$role->user) {
            continue;
        }
        if ((int) $role->user->status !== \humhub\modules\user\models\User::STATUS_ENABLED || !$circle->space->isMember($role->user->id)) {
            continue;
        }
        $roles[] = $role;
    }
    return $roles;
};
$roleImages = static function ($circle) use ($avatar, $activeRoles): string {
    $images = [];
    foreach ($activeRoles($circle) as $role) {
        $images[] = Html::tag('span', $avatar($role->user), ['class' => 'sg-role-avatar', 'title' => Role::LABELS[$role->role_key] ?? $role->role_key]);
    }
    return implode('', $images);
};
$doubleLink = static function ($circle) use ($avatar, $activeRoles): string {
    $roles = [];
    foreach ($activeRoles($circle, ['leader', 'delegate']) as $role) {
        $roles[] = Html::tag('span',
            Html::tag('span', $avatar($role->user), ['class' => 'sg-map-role-avatar']) .
            Html::tag('span', Html::encode(Role::LABELS[$role->role_key]), ['class' => 'sg-map-role-label']),
            ['class' => 'sg-map-role', 'title' => $role->user->displayName . ': ' . Role::LABELS[$role->role_key]]
        );
    }
    return $roles ? Html::tag('span', implode('', $roles), ['class' => 'sg-map-roles']) : Html::tag('span', 'Doppelbindung noch nicht gepflegt', ['class' => 'sg-map-no-roles']);
};
$graph = [];
foreach ($nodes as $id => $node) {
    $graph[] = ['id' => $id, 'x' => $node['x'], 'depth' => $node['depth'], 'parentId' => $node['parentId'], 'focus' => in_array($id, $focusSpaceIds, true)];
}
?>
<div class="container sg">
<header class="sg-hero"><span class="sg-eyebrow">Organisation</span><h1>Unsere Arbeitskreise</h1><p>Welche Verantwortung liegt wo? Die Übersicht zeigt nur Kreise, die du sehen darfst.</p></header>
<?php if (!$hasConfiguredRoot && $rows): ?><div class="sg-note">Der Kernkreis ist noch nicht festgelegt. Die Übersicht zeigt deshalb die sichtbaren Wurzelkreise.</div><?php endif ?>
<?php if ($rows): ?>
<div class="sg-directory-switch" role="tablist" aria-label="Ansicht wählen"><button class="sg-button" type="button" data-sg-directory-tab="table" aria-selected="true">Tabelle</button><button class="sg-button sg-button-secondary" type="button" data-sg-directory-tab="map" aria-selected="false">Karte</button></div>
<section class="sg-card" data-sg-directory-panel="table"><div class="table-responsive"><table class="sg-directory-table"><thead><tr><th>Kreis</th><th>Mandat in Kürze</th><th>Rollen</th></tr></thead><tbody>
<?php foreach ($rows as $row): $circle = $row['circle']; ?>
<tr><td><div class="sg-tree-name" style="padding-left:<?= (int) $row['depth'] * 28 ?>px"><?= Html::a(Html::encode($circle->space->name), $circle->space->createUrl('/sociocratic-governance/circle/index')) ?></div></td><td><?= Html::encode($circle->mandateSummary() ?: 'Noch nicht beschrieben.') ?></td><td class="sg-role-avatars"><?= $roleImages($circle) ?></td></tr>
<?php endforeach ?></tbody></table></div></section>
<section class="sg-card sg-directory-map-panel" data-sg-directory-panel="map" hidden><p class="sg-muted">Mit Mausrad oder Pinch zoomen, mit Ziehen verschieben. Die Ansicht startet bei deinen Kreisrollen.</p><div class="sg-circle-map" data-sg-circle-map data-graph='<?= Json::htmlEncode($graph) ?>'><svg class="sg-map-links" aria-hidden="true"></svg><div class="sg-map-world">
<?php foreach ($nodes as $id => $node): $circle = $node['circle']; ?>
<a class="sg-map-bubble<?= in_array($id, $focusSpaceIds, true) ? ' is-focus' : '' ?>" data-sg-node-id="<?= (int) $id ?>" href="<?= Html::encode($circle->space->createUrl('/sociocratic-governance/circle/index')) ?>"><span class="sg-map-role-area"><?= $doubleLink($circle) ?></span><strong><?= Html::encode($circle->space->name) ?></strong><span class="sg-map-mandate"><?= Html::encode($circle->mandateSummary() ?: 'Mandat noch nicht beschrieben.') ?></span></a>
<?php endforeach ?></div></div></section>
<?php else: ?><section class="sg-card">Noch keine sichtbaren Kreisprofile. Aktiviere das Modul in einem Space und pflege dort das Kreisprofil.</section><?php endif ?>
</div>
