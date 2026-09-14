<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use yii\helpers\Json;
use humhub\modules\sociocraticGovernance\models\Role;

\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$activeView = ($activeView ?? 'table') === 'map' ? 'map' : 'table';
$query = $query ?? '';
$selectedType = $selectedType ?? 'all';
$onlyMine = $onlyMine ?? false;
$directoryUrl = static function (array $params = []) use ($query, $selectedType, $onlyMine): array {
    $base = [];
    if ($query !== '') { $base['query'] = $query; }
    if ($selectedType !== 'all') { $base['type'] = $selectedType; }
    if ($onlyMine) { $base['mine'] = 1; }
    return array_merge(['/sociocratic-governance/directory/index'], $base, $params);
};
$projectRows = $projectRows ?? array_values(array_filter($rows, static fn(array $row): bool => !$row['circle']->isCompetenceCircle()));
$competenceRows = $competenceRows ?? array_values(array_filter($rows, static fn(array $row): bool => $row['circle']->isCompetenceCircle()));

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
        if (($keys !== null && !in_array($role->role_key, $keys, true)) || !$role->user) { continue; }
        if ((int) $role->user->status !== \humhub\modules\user\models\User::STATUS_ENABLED || !$circle->space->isMember($role->user->id)) { continue; }
        $roles[] = $role;
    }
    return $roles;
};
$roleImages = static function ($circle) use ($avatar, $activeRoles): string {
    $images = [];
    foreach ($activeRoles($circle) as $role) {
        $images[] = Html::tag('span', $avatar($role->user), ['class' => 'sg-role-avatar sg-role-' . $role->role_key]);
    }
    return implode('', $images);
};
$otherMembers = static function ($circle): string {
    $roleIds = array_map(static fn($role): int => (int) $role->user_id, $circle->roles);
    $links = [];
    foreach (\humhub\modules\sociocraticGovernance\services\CircleDirectory::people($circle) as $member) {
        $user = $member['user'];
        if (in_array((int) $user->id, $roleIds, true)) { continue; }
        $links[] = Html::a(Html::encode($user->displayName), $user->getUrl());
    }
    return $links ? implode(', ', $links) : '<span class="sg-muted">Keine weiteren</span>';
};
$avatarUrl = static function ($user): ?string {
    try {
        if (method_exists($user, 'getProfileImage')) {
            $image = $user->getProfileImage();
            if ($image && method_exists($image, 'getUrl')) { return (string) $image->getUrl(); }
        }
    } catch (\Throwable) {
        // Initials are rendered by the client when an optional profile image is unavailable.
    }
    return null;
};
$person = static function ($user, string $roles, string $roleKey = '') use ($avatarUrl): array {
    return ['name' => (string) $user->displayName, 'roles' => $roles, 'roleKey' => $roleKey, 'url' => (string) $user->getUrl(), 'avatarUrl' => $avatarUrl($user)];
};
$circleUrl = static fn($circle): string => (string) $circle->space->createUrl('/sociocratic-governance/circle/index');
$membersUrl = static fn($circle): string => (string) $circle->space->createUrl('/space/membership/members-list');
$workUrl = static fn($circle): string => (string) $circle->space->createUrl('/sociocratic-governance/work/index');
$graph = ['nodes' => [], 'links' => []];
foreach ($nodes as $id => $node) {
    $circle = $node['circle'];
    $graph['nodes'][] = [
        'id' => (int) $id, 'name' => (string) $circle->space->name,
        'url' => $circleUrl($circle), 'membersUrl' => $membersUrl($circle), 'workUrl' => $workUrl($circle),
        'mandate' => $circle->mandateSummary(), 'memberCount' => count($node['people']), 'diameter' => (int) $node['diameter'],
        'x' => (float) $node['x'], 'depth' => (int) $node['depth'],
        'parentId' => (int) $node['parentId'], 'focus' => in_array($id, $focusSpaceIds, true),
        'color' => (string) $circle->color,
    ];
    if ($node['parentId'] && isset($nodes[$node['parentId']])) {
        $linkRoles = ['leader' => null, 'delegate' => null];
        foreach ($activeRoles($circle, ['leader', 'delegate']) as $role) { $linkRoles[$role->role_key] = $person($role->user, Role::LABELS[$role->role_key] ?? $role->role_key, $role->role_key); }
        $graph['links'][] = ['parentId' => (int) $node['parentId'], 'childId' => (int) $id, 'leader' => $linkRoles['leader'], 'delegate' => $linkRoles['delegate']];
    }
}
?>
<div class="container sg">
<header class="sg-hero"><span class="sg-eyebrow">Organisation</span><h1>Unsere Kreise</h1><p>Projektkreise organisieren konkrete Vorhaben. Kompetenzkreise sind Räume für Wissen und thematischen Austausch.</p><?= Html::a('← Zurück zu Start', ['/sociocratic-governance/start/index'], ['class' => 'sg-hero-action']) ?></header>
<?= Html::beginForm(['/sociocratic-governance/directory/index'], 'get', ['class' => 'sg-directory-filters', 'aria-label' => 'Kreise filtern']) ?>
<?= Html::hiddenInput('view', $activeView) ?>
<div><label for="sg-directory-query">Kreise suchen</label><?= Html::textInput('query', $query, ['id' => 'sg-directory-query', 'maxlength' => 100, 'placeholder' => 'Name, Mandat oder Thema']) ?></div>
<div><label for="sg-directory-type">Kreisart</label><?= Html::dropDownList('type', $selectedType, ['all' => 'Alle Kreisarten', 'project' => 'Projektkreise', 'competence' => 'Kompetenzkreise'], ['id' => 'sg-directory-type']) ?></div>
<label class="sg-directory-mine"><?= Html::checkbox('mine', $onlyMine, ['value' => 1]) ?> Nur meine Kreise</label>
<?= Html::submitButton('Anwenden', ['class' => 'sg-button']) ?>
<?php if ($query !== '' || $selectedType !== 'all' || $onlyMine): ?><?= Html::a('Filter zurücksetzen', ['/sociocratic-governance/directory/index', 'view' => $activeView], ['class' => 'sg-button sg-button-secondary']) ?><?php endif ?>
<?= Html::endForm() ?>
<?php if (!$hasConfiguredRoot && $rows): ?><div class="sg-note">Der Kernkreis ist noch nicht festgelegt. Die Übersicht zeigt deshalb die sichtbaren Wurzelkreise.</div><?php endif ?>
<?php if ($rows): ?>
<div class="sg-directory-switch" role="tablist" aria-label="Ansicht wählen"><?= Html::a('Tabelle', $directoryUrl(['view' => 'table']), ['class' => 'sg-button' . ($activeView === 'table' ? '' : ' sg-button-secondary'), 'data-sg-directory-tab' => 'table', 'aria-selected' => $activeView === 'table' ? 'true' : 'false']) ?><?= Html::a('Karte', $directoryUrl(['view' => 'map']), ['class' => 'sg-button' . ($activeView === 'map' ? '' : ' sg-button-secondary'), 'data-sg-directory-tab' => 'map', 'aria-selected' => $activeView === 'map' ? 'true' : 'false']) ?></div>
<section class="sg-card" data-sg-directory-panel="table"<?= $activeView === 'table' ? '' : ' hidden' ?>><div class="table-responsive"><table class="sg-directory-table"><thead><tr><th>Kreis</th><th>Typ</th><th>Mandat in Kürze</th><th>Rollen</th><th>Weitere Mitglieder</th></tr></thead><tbody>
<?php foreach ($projectRows as $row): $circle = $row['circle']; ?>
<tr class="<?= Html::encode($circle->colorClass()) ?>"><td><div class="sg-tree-name" style="padding-left:<?= (int) $row['depth'] * 28 ?>px"><span class="sg-circle-color-dot" aria-label="Kreisfarbe: <?= Html::encode(\humhub\modules\sociocraticGovernance\models\Circle::COLORS[$circle->color] ?? 'Seegrün') ?>"></span><?= Html::a(Html::encode($circle->space->name), $circleUrl($circle)) ?></div></td><td><?= Html::encode(\humhub\modules\sociocraticGovernance\models\Circle::TYPES[$circle->type] ?? \humhub\modules\sociocraticGovernance\models\Circle::TYPES['project']) ?></td><td><?= Html::encode($circle->mandateSummary() ?: 'Noch nicht beschrieben.') ?></td><td class="sg-role-avatars"><?= $roleImages($circle) ?></td><td><?= $otherMembers($circle) ?></td></tr>
<?php endforeach ?>
<?php if ($competenceRows): ?><tr class="sg-directory-section-divider"><th colspan="5" scope="colgroup"><span>Kompetenzkreise</span><small>Wissen, Erfahrung und thematischer Austausch</small></th></tr><?php endif ?>
<?php foreach ($competenceRows as $row): $circle = $row['circle']; ?>
<tr class="<?= Html::encode($circle->colorClass()) ?>"><td><div class="sg-tree-name" style="padding-left:<?= (int) $row['depth'] * 28 ?>px"><span class="sg-circle-color-dot" aria-label="Kreisfarbe: <?= Html::encode(\humhub\modules\sociocraticGovernance\models\Circle::COLORS[$circle->color] ?? 'Seegrün') ?>"></span><?= Html::a(Html::encode($circle->space->name), $circleUrl($circle)) ?></div></td><td><?= Html::encode(\humhub\modules\sociocraticGovernance\models\Circle::TYPES[$circle->type] ?? \humhub\modules\sociocraticGovernance\models\Circle::TYPES['project']) ?></td><td><?= Html::encode($circle->mandateSummary() ?: 'Noch nicht beschrieben.') ?></td><td class="sg-role-avatars"><?= $roleImages($circle) ?></td><td><?= $otherMembers($circle) ?></td></tr>
<?php endforeach ?></tbody></table></div><div class="sg-role-key" aria-label="Legende der Rollenfarben"><span class="sg-role-key-leader">Kreisleitung</span><span class="sg-role-key-delegate">Delegation</span><span class="sg-role-key-facilitator">Moderation</span><span class="sg-role-key-secretary">Dokumentation</span></div></section>
<section class="sg-card sg-directory-map-panel" data-sg-directory-panel="map"<?= $activeView === 'map' ? '' : ' hidden' ?>><div class="sg-map-intro"><p class="sg-muted">Kreise öffnen ihre Details und Mitglieder direkt in der Karte. Ziehen verschiebt die Ansicht; Mausrad, Zwei-Finger-Geste oder die Schaltflächen zoomen. „Ansicht zentrieren“ bewahrt dabei die gewählte Vergrößerung. Falls die interaktive Karte nicht verfügbar ist, bleibt die strukturierte Übersicht sichtbar.</p></div><div class="sg-circle-map sg-map-fallback-mode" data-sg-circle-map data-graph='<?= Json::htmlEncode($graph) ?>' aria-label="Kreis-Karte" tabindex="0"><div class="sg-map-fallback"><?php foreach ($rows as $row): $circle = $row['circle']; ?><article class="sg-map-fallback-node <?= Html::encode($circle->colorClass()) ?>" style="margin-left:<?= min(112, (int) $row['depth'] * 28) ?>px"><span class="sg-eyebrow"><?= Html::encode(\humhub\modules\sociocraticGovernance\models\Circle::TYPES[$circle->type] ?? \humhub\modules\sociocraticGovernance\models\Circle::TYPES['project']) ?><?= $row['depth'] ? ' · Ebene ' . ((int) $row['depth'] + 1) : '' ?></span><h3><?= Html::a(Html::encode($circle->space->name), $circleUrl($circle)) ?></h3><p><?= Html::encode($circle->mandateSummary() ?: 'Mandat noch nicht beschrieben.') ?></p><p class="sg-muted"><?= count(\humhub\modules\sociocraticGovernance\services\CircleDirectory::people($circle)) ?> sichtbare Mitglieder</p></article><?php endforeach ?></div></div></section>
<?php else: ?><section class="sg-card">Für diese Auswahl gibt es keine sichtbaren Kreise. Passe die Filter an oder setze sie zurück.</section><?php endif ?>
</div>
