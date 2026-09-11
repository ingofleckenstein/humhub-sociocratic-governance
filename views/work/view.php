<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\{Html, Json};
use humhub\modules\sociocraticGovernance\models\{Circle, WorkEvent, WorkItem, WorkResource};
use humhub\modules\sociocraticGovernance\services\{Access, WorkAccess};
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);

$submitted = $submitted ?? [];
$draftInput = static fn($action, $field, $fallback = '') => ($submitted['action'] ?? null) === $action && is_string($submitted[$field] ?? null) ? $submitted[$field] : $fallback;
$member = Access::write($space);
$reviewer = WorkAccess::reviewer($space);
$actor = (int) Yii::$app->user->id;
$reviewerIds = $item->status === 'review' ? WorkAccess::reviewers($space) : [];
$canApprove = $item->status === 'review' && (
    ((int) ($reviewerIds['leader'] ?? 0) === $actor && (int) $item->leader_approval_id !== $actor)
    || ((int) ($reviewerIds['delegate'] ?? 0) === $actor && (int) $item->delegate_approval_id !== $actor)
);
$canContribute = !$item->archived_at && WorkAccess::activeUser() && $item->kind === 'task' && !in_array($item->status, ['done', 'rejected'], true);
$resourceCount = count($item->resources);
$coveredResourceCount = count(array_filter($item->resources, static fn(WorkResource $resource): bool => $resource->required_amount !== null && $resource->totalCommittedAmount + 0.00001 >= (float) $resource->required_amount));
$resourceStatusPercent = $resourceCount ? (int) round(100 * $coveredResourceCount / $resourceCount) : 0;
$visibleEvents = array_values(array_filter($item->events, static fn(WorkEvent $event): bool => WorkAccess::canReadEvent($event)));
$canReadFullHistory = WorkAccess::canReadFullHistory($item);
$itemTypeLabel = $item->kind === 'idea' ? 'Vorschlag' : 'Aufgabe';
$statusLabel = match ($item->status) {
    'idea' => 'In Prüfung',
    'open' => 'Offen',
    'working' => 'In Bearbeitung',
    'review' => 'Zur Abnahme',
    'done' => 'Abgeschlossen',
    'rejected' => 'Nicht weiterverfolgt',
    default => WorkItem::STATUSES[$item->status] ?? $item->status,
};
$rich = static fn($text) => class_exists(\humhub\modules\content\widgets\richtext\RichText::class) ? \humhub\modules\content\widgets\richtext\RichText::output((string) $text) : nl2br(Html::encode((string) $text));
$returnParams = $returnParams ?? [];
$viewUrl = static fn(string $name) => $space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id, 'section' => $name] + $returnParams);
$sections = ['overview' => 'Worum geht es?', 'collaboration' => 'Mitmachen & umsetzen', 'history' => 'Bisheriger Weg'];
$personLink = static function ($person): string {
    return $person ? Html::a(Html::encode($person->displayName), $person->getUrl()) : 'Nicht verfügbar';
};
$historyValue = static function (string $field, $value): string {
    if ($value === null || $value === '') { return '–'; }
    if ($field === 'space_id') { return Space::findOne((int) $value)?->name ?? 'Nicht verfügbar'; }
    if (in_array($field, ['author_id', 'assignee_id', 'leader_approval_id', 'delegate_approval_id'], true)) {
        return User::findOne((int) $value)?->displayName ?? 'Nicht verfügbar';
    }
    if ($field === 'status') { return WorkItem::STATUSES[$value] ?? (string) $value; }
    if ($field === 'kind') { return $value === 'idea' ? 'Idee' : 'Aufgabe'; }
    return (string) $value;
};
$canReadSpace = static function (int $spaceId): bool {
    $candidate = Space::findOne($spaceId);
    return $candidate !== null && Access::read($candidate);
};
$formStart = static function ($action) use ($space, $item, $section) {
    return Html::beginForm($space->createUrl('/sociocratic-governance/work/change', ['id' => $item->id]), 'post')
        . Html::hiddenInput('revision', $item->revision) . Html::hiddenInput('section', $section)
        . ($action === '' ? '' : Html::hiddenInput('action', $action));
};
$button = static function ($action, $label) use ($formStart) {
    return $formStart($action) . Html::submitButton($label, ['class' => 'sg-button']) . Html::endForm();
};
?>
<div class="sg">
<header class="sg-hero"><span class="sg-eyebrow"><?= Html::encode($itemTypeLabel) ?> #<?= (int) $item->id ?> · <?= Html::encode($statusLabel) ?><?= $item->archived_at ? ' · Archiviert' : '' ?></span>
<h1><?= Html::encode($item->title) ?></h1><p><?= $item->kind === 'idea' ? 'Ein Vorschlag zur gemeinsamen Prüfung.' : 'Eine konkrete Aufgabe im Projektkreis.' ?></p>
<?= Html::a($returnLabel ?? '← Zurück zum Board', $returnUrl ?? $space->createUrl('/sociocratic-governance/work/index'), ['class' => 'sg-hero-action']) ?></header>
<nav class="sg-subnav" aria-label="Bereiche dieses Vorhabens"><?php foreach ($sections as $key => $label): ?><?= Html::a($label, $viewUrl($key), ['class' => $section === $key ? 'is-active' : '']) ?><?php endforeach ?></nav>
<?php if ($error): ?><p class="alert alert-danger" role="alert"><?= Html::encode($error) ?></p><?php endif ?>

<?php if ($section === 'overview'): ?>
<details class="sg-card sg-collapsible" open><summary><h2>Worum geht es?</h2></summary><div class="sg-text"><?= $rich($item->description) ?></div>
<?php if ($item->topics): ?><p class="sg-tags"><?php foreach ($item->topics as $topic): ?><span><?= Html::encode($topic->name) ?></span><?php endforeach ?></p><?php endif ?>
<?php if ($item->kind === 'task'): ?><div class="sg-resource-summary"><h3>Ressourcen im Überblick <span><?= $resourceStatusPercent ?> % gedeckt</span></h3><?php if ($resourceCount): ?><p class="sg-muted"><?= $coveredResourceCount ?> von <?= $resourceCount ?> Ressourcen sind vollständig gedeckt. <?= Html::a('Mitmachen & umsetzen', $viewUrl('collaboration')) ?></p><ul><?php foreach ($item->resources as $resource): ?><li><strong><?= Html::encode($resource->label) ?></strong><?php if ($resource->required_amount === null): ?><span>Bedarf noch offen</span><?php else: ?><span><?= Html::encode((string) $resource->totalCommittedAmount) ?> von <?= Html::encode((string) $resource->required_amount) ?> <?= Html::encode($resource->unit) ?> · <?= Html::encode((string) $resource->progress) ?> %</span><?php endif ?></li><?php endforeach ?></ul><?php else: ?><p class="sg-muted">Noch kein Ressourcenbedarf erfasst. <?= Html::a('Bedarf ergänzen', $viewUrl('collaboration')) ?></p><?php endif ?></div><?php endif ?>
 </details>
<details class="sg-card sg-collapsible"><summary><h2>Wer ist beteiligt?</h2></summary><dl class="sg-work-facts"><div><dt>Eingereicht von</dt><dd><?= $personLink($item->author) ?></dd></div><div><dt>Aktuell im Kreis</dt><dd><?= Html::a(Html::encode($space->name), $space->createUrl('/sociocratic-governance/circle/index')) ?></dd></div><div><dt>Zuständige Person</dt><dd><?= $item->assignee ? $personLink($item->assignee) : 'Noch nicht übernommen' ?></dd></div></dl></details>
<details class="sg-card sg-collapsible" open><summary><h2>Wo steht es gerade?</h2></summary><p><strong><?= Html::encode($statusLabel) ?></strong></p>
<?php if ($item->status === 'review'): ?><p>Das Ergebnis liegt zur Abnahme vor.</p><ul><?php foreach (['leader' => 'Kreisleitung', 'delegate' => 'Delegierte*r'] as $key => $label): ?><li><?= Html::encode($label) ?>: <?= !isset($reviewerIds[$key]) ? 'Rolle nicht aktiv besetzt' : ((int) $item->{$key . '_approval_id'} === $reviewerIds[$key] ? 'Bestätigt' : 'Bestätigung offen') ?></li><?php endforeach ?></ul><?php endif ?>
<div class="sg-actions"><?php if (!$item->archived_at && $member && !$item->assignee_id && in_array($item->status, ['open', 'working'], true)): ?><?= $button('claim', 'Mir zuweisen') ?><?php endif ?>
<?php if (!$item->archived_at && $member && (int) $item->assignee_id === $actor && $item->status === 'open'): ?><?= $button('start', 'In Bearbeitung verschieben') ?><?php endif ?>
<?php if (!$item->archived_at && $canApprove): ?><?= $button('approve', 'Abnahme bestätigen') ?><?php endif ?>
<?php if ($member && !$item->archived_at && $item->kind === 'task' && in_array($item->status, ['done', 'rejected'], true)): ?><?= $button('archive', 'Archivieren') ?><?php endif ?></div></details>
<?php endif ?>

<?php if ($section === 'collaboration' && $item->kind === 'task'): ?>
<details class="sg-card sg-collapsible" open><summary><h2>Was wird gebraucht – und wer möchte beitragen?</h2></summary><p>Hier wird sichtbar, was noch gebraucht wird und was bereits gedeckt ist.</p>
<?php foreach ($item->resources as $resource): ?><article class="sg-resource"><h3><?= Html::encode($resource->label) ?> <span class="sg-muted">· <?= Html::encode(WorkResource::TYPES[$resource->resource_type]) ?></span></h3>
<?php if (in_array($resource->resource_type, ['time', 'expertise'], true) && $resource->time_mode): ?><p class="sg-muted"><strong><?= Html::encode(WorkResource::TIME_MODES[$resource->time_mode]) ?>:</strong> <?= Html::encode($resource->time_pattern) ?></p><?php endif ?>
<?php if ($resource->required_amount !== null): ?><p><strong><?= Html::encode((string) $resource->totalCommittedAmount) ?></strong> von <?= Html::encode((string) $resource->required_amount) ?> <?= Html::encode($resource->unit) ?> zugesagt</p><div class="sg-progress" role="progressbar" aria-label="Ressourcenstand" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= Html::encode((string) $resource->progress) ?>"><span style="width:<?= Html::encode((string) $resource->progress) ?>%"></span></div><?php endif ?>
<?php if ($resource->details): ?><div class="sg-text"><?= $rich($resource->details) ?></div><?php endif ?>
<?php if (!$item->archived_at && $member && $item->status !== 'rejected'): ?><details><summary>Ressourcenbedarf bearbeiten</summary><?= $formStart('resource') ?><?= Html::hiddenInput('resource_id', $resource->id) ?>
<?= Html::label('Art', 'resource-type-' . $resource->id) ?><?= Html::dropDownList('resource_type', $resource->resource_type, WorkResource::TYPES, ['id' => 'resource-type-' . $resource->id, 'data-sg-resource-type' => true]) ?><?= Html::label('Was wird gebraucht?', 'resource-label-' . $resource->id) ?><?= Html::textInput('label', $resource->label, ['id' => 'resource-label-' . $resource->id, 'required' => true, 'maxlength' => 255]) ?><?= Html::label('Geschätzte Menge', 'resource-required-' . $resource->id) ?><?= Html::textInput('required_amount', $resource->required_amount, ['id' => 'resource-required-' . $resource->id, 'inputmode' => 'decimal']) ?><?= Html::label('Einheit', 'resource-unit-' . $resource->id) ?><?= Html::textInput('unit', $resource->unit, ['id' => 'resource-unit-' . $resource->id, 'required' => true, 'maxlength' => 32, 'placeholder' => 'z. B. Stunden oder o. E.']) ?><p class="sg-muted">Falls keine Einheit passt: „o. E.“ eintragen.</p>
<div data-sg-resource-participation><?= Html::label('Form der Mitwirkung', 'resource-time-mode-' . $resource->id, ['data-sg-resource-mode-label' => true]) ?><?= Html::dropDownList('time_mode', $resource->time_mode, ['' => 'Bitte wählen'] + WorkResource::TIME_MODES, ['id' => 'resource-time-mode-' . $resource->id, 'data-sg-resource-mode' => true]) ?><?= Html::label('Zeitlicher Rahmen oder Termin', 'resource-time-pattern-' . $resource->id, ['data-sg-resource-pattern-label' => true]) ?><?= Html::textInput('time_pattern', $resource->time_pattern, ['id' => 'resource-time-pattern-' . $resource->id, 'maxlength' => 255, 'data-sg-resource-pattern' => true]) ?></div><?= Html::label('Bereits vorhandene, nicht personenbezogene Menge', 'resource-available-' . $resource->id) ?><?= Html::textInput('available_amount', $resource->committed_amount, ['id' => 'resource-available-' . $resource->id, 'inputmode' => 'decimal']) ?><?= Html::label('Hinweise', 'resource-details-' . $resource->id) ?><?= Html::textarea('details', $resource->details, ['id' => 'resource-details-' . $resource->id, 'rows' => 3, 'maxlength' => 20000]) ?><?= Html::submitButton('Bedarf speichern', ['class' => 'sg-button']) ?><?= Html::endForm() ?></details><?php endif ?>
<?php if ($canContribute): $ownContribution = null; foreach ($resource->contributions as $contribution) { if ((int) $contribution->user_id === $actor) { $ownContribution = $contribution; break; } } ?><details><summary><?= $ownContribution && (float) $ownContribution->amount > 0 ? 'Meine Zusage ändern' : 'Ich möchte beitragen' ?></summary><?= $formStart('contribute') ?><?= Html::hiddenInput('resource_id', $resource->id) ?><?= Html::label('Ich gebe', 'contribution-amount-' . $resource->id) ?><?= Html::textInput('amount', $ownContribution?->amount ?? '', ['id' => 'contribution-amount-' . $resource->id, 'inputmode' => 'decimal', 'required' => true]) ?> <?= Html::encode($resource->unit) ?><p class="sg-muted">Die Art und Menge deines Beitrags werden nicht angezeigt. Mit 0 nimmst du deine Zusage zurück.</p><?= Html::submitButton('Meine Zusage speichern', ['class' => 'sg-button']) ?><?= Html::endForm() ?></details><?php endif ?></article><?php endforeach ?>
<?php if (!$item->resources): ?><p class="sg-muted">Bisher ist kein Ressourcenbedarf eingetragen.</p><?php endif ?>
<?php if (!$item->archived_at && $member && $item->status !== 'rejected'): ?><details><summary>Ressource hinzufügen</summary><?= $formStart('resource') ?><?= Html::label('Art', 'resource-type') ?><?= Html::dropDownList('resource_type', 'time', WorkResource::TYPES, ['id' => 'resource-type', 'data-sg-resource-type' => true]) ?><?= Html::label('Was wird gebraucht?', 'resource-label') ?><?= Html::textInput('label', '', ['id' => 'resource-label', 'required' => true, 'maxlength' => 255]) ?><?= Html::label('Geschätzte Menge', 'resource-required') ?><?= Html::textInput('required_amount', '', ['id' => 'resource-required', 'inputmode' => 'decimal']) ?><?= Html::label('Einheit', 'resource-unit') ?><?= Html::textInput('unit', '', ['id' => 'resource-unit', 'required' => true, 'maxlength' => 32, 'placeholder' => 'z. B. Stunden oder o. E.']) ?><p class="sg-muted">Falls keine Einheit passt: „o. E.“ eintragen.</p><div data-sg-resource-participation><?= Html::label('Form der Mitwirkung', 'resource-time-mode', ['data-sg-resource-mode-label' => true]) ?><?= Html::dropDownList('time_mode', '', ['' => 'Bitte wählen'] + WorkResource::TIME_MODES, ['id' => 'resource-time-mode', 'data-sg-resource-mode' => true]) ?><?= Html::label('Zeitlicher Rahmen oder Termin', 'resource-time-pattern', ['data-sg-resource-pattern-label' => true]) ?><?= Html::textInput('time_pattern', '', ['id' => 'resource-time-pattern', 'maxlength' => 255, 'data-sg-resource-pattern' => true]) ?></div><?= Html::label('Bereits vorhandene, nicht personenbezogene Menge', 'resource-available-new') ?><?= Html::textInput('available_amount', '0', ['id' => 'resource-available-new', 'inputmode' => 'decimal']) ?><?= Html::label('Hinweise', 'resource-details') ?><?= Html::textarea('details', '', ['id' => 'resource-details', 'rows' => 3, 'maxlength' => 20000]) ?><?= Html::submitButton('Bedarf erfassen', ['class' => 'sg-button']) ?><?= Html::endForm() ?></details><?php endif ?></details>
<?php endif ?>

<?php if ($section === 'collaboration'): ?>
<?php
$canChooseNextStep = $reviewer;
$targetOptions = [];
if ($canChooseNextStep && in_array($item->status, ['idea', 'open', 'working'], true)) {
    $targets = WorkAccess::neighbours($space);
    $circle = Circle::findOne($space->id);
    $parentId = (int) $circle?->parent_space_id;
    if (isset($targets[$parentId])) {
        $targetOptions['↑ Einen Kreis höher'] = [$parentId => $targets[$parentId]];
        unset($targets[$parentId]);
    }
    if ($targets) { $targetOptions['↓ Einen Kreis niedriger'] = $targets; }
}
$actions = [];
if ($reviewer && $item->status === 'idea') { $actions['accept'] = 'Im Mandat: als Aufgabe annehmen'; }
if ($targetOptions) { $actions['delegate'] = 'An einen anderen Kreis delegieren'; }
if ($member && (int) $item->assignee_id === $actor && $item->status === 'working') { $actions['submit'] = 'Fertig: zur Abnahme vorlegen'; }
if ($reviewer && $item->status === 'review') { $actions['return'] = 'Zur Nacharbeit zurückgeben'; }
if ($member && $item->kind === 'task' && in_array($item->status, ['open', 'working'], true)) { $actions['reject'] = 'Aufgabe begründet ablehnen'; }
if ($reviewer && $item->status === 'idea' && !Circle::findOne($space->id)->parent_space_id) { $actions['reject'] = 'Außerhalb des Gesamtmandats abschließen'; }
?>
<?php if ($actions): $selectedAction = $submitted['action'] ?? array_key_first($actions); ?><details class="sg-card sg-collapsible"<?= $item->status === 'open' ? '' : ' open' ?>><summary><h2>Nächster Schritt</h2></summary><p class="sg-muted">Prüfe, ob das Vorhaben in diesem Mandat angenommen wird oder ob ein direkt benachbarter Kreis zuständig ist.</p><?= $formStart('') ?><?= Html::label('Was soll als Nächstes passieren?', 'work-action') ?><?= Html::dropDownList('action', $selectedAction, $actions, ['id' => 'work-action', 'data-sg-work-action' => 'work-delegation-target', 'aria-controls' => 'work-delegation-target']) ?><?php if ($targetOptions): ?><div id="work-delegation-target" data-sg-delegation-target<?= $selectedAction === 'delegate' ? '' : ' hidden' ?>><?= Html::label('Welcher direkt benachbarte Kreis übernimmt?', 'work-target') ?><?= Html::dropDownList('target_space_id', $submitted['target_space_id'] ?? null, $targetOptions, ['id' => 'work-target']) ?><p class="sg-muted">Die Übergabe überträgt den aktuellen Stand und startet im Zielkreis eine neue gemeinsame Diskussion. Geschützte frühere Fassungen bleiben dort verborgen.</p></div><?php endif ?><?= Html::label('Mandatsbezug, Übergabe, Ergebnis oder Begründung', 'work-note') ?><?= Html::textarea('note', $draftInput($selectedAction, 'note'), ['id' => 'work-note', 'rows' => 4, 'required' => true, 'maxlength' => 20000]) ?><?= Html::submitButton('Schritt dokumentieren', ['class' => 'sg-button']) ?><?= Html::endForm() ?></details><?php elseif (in_array($item->status, ['idea', 'open', 'working'], true) && !$reviewer): ?><section class="sg-card"><h2>Nächster Schritt</h2><?php if ($item->kind === 'idea'): ?><p>Bring deine Sichtweise in die gemeinsame Diskussion ein<?= ($member || (int) $item->author_id === $actor) ? ' oder präzisiere den Vorschlag' : '' ?>. Die Entscheidung über Annahme oder Delegation treffen die Kreisleitung oder die delegierte Person dieses Kreises.</p><?php else: ?><p>Wenn du die Aufgabe übernehmen möchtest, wähle im Überblick „Mir zuweisen“. Über eine Delegation entscheidet die Kreisleitung oder die delegierte Person dieses Kreises.</p><?php endif ?><p class="sg-muted">Sobald eine dieser Rollen den Vorschlag geprüft oder die Aufgabe weitergegeben hat, wird der nächste dokumentierte Schritt hier sichtbar.</p></section><?php endif ?>
<?php if (in_array($item->status, ['idea', 'open', 'working'], true) && ($member || ($item->kind === 'idea' && (int) $item->author_id === $actor))): ?><details class="sg-card sg-collapsible"><summary><h2><?= $item->kind === 'task' ? 'Aufgabe präzisieren' : 'Vorschlag präzisieren' ?></h2></summary><?= $formStart('edit') ?><?= Html::label('Titel', 'work-edit-title') ?><?= Html::textInput('title', $draftInput('edit', 'title', $item->title), ['id' => 'work-edit-title', 'required' => true, 'maxlength' => 255]) ?><?= Html::label('Beschreibung', 'work-edit-description') ?><?= Html::textarea('description', $draftInput('edit', 'description', $item->description), ['id' => 'work-edit-description', 'required' => true, 'maxlength' => 20000, 'rows' => 5]) ?><?= Html::label('Themen', 'work-edit-topics') ?><?= Html::textInput('topics', $draftInput('edit', 'topics', implode(', ', array_map(static fn($topic) => $topic->name, $item->topics))), ['id' => 'work-edit-topics', 'maxlength' => 1000]) ?><?= Html::submitButton('Änderung speichern', ['class' => 'sg-button']) ?><?= Html::endForm() ?></details><?php endif ?>
<?php if ($item->streamPost && class_exists(\humhub\modules\comment\widgets\Comments::class)): ?><section class="sg-card sg-work-discussion"><h2>Gemeinsame Diskussion</h2><p class="sg-muted">Diese Kommentare sind dieselben wie direkt am zugehörigen Streambeitrag. Antworten, Markdown und Erwähnungen stehen hier ebenfalls zur Verfügung.</p><?= \humhub\modules\comment\widgets\CommentLink::widget(['object' => $item->streamPost]) ?><?= \humhub\modules\comment\widgets\Comments::widget(['object' => $item->streamPost, 'viewMode' => \humhub\modules\comment\widgets\Comments::VIEW_MODE_FULL]) ?></section><?php elseif ($item->stream_post_id === null): ?><section class="sg-card"><h2>Gemeinsame Diskussion</h2><p class="sg-muted">Für dieses bereits bestehende Vorhaben wurde noch kein gemeinsamer Streambeitrag angelegt.</p><?php if ($member || (int) $item->author_id === $actor): ?><?= Html::beginForm($space->createUrl('/sociocratic-governance/work/discussion', ['id' => $item->id]), 'post') ?><?= Html::submitButton('Gemeinsame Diskussion im Stream anlegen', ['class' => 'sg-button']) ?><?= Html::endForm() ?><?php endif ?></section><?php endif ?>
<?php endif ?>

<?php if ($section === 'history'): ?>
<?php if ($item->proposalRevisions && $canReadFullHistory): ?><section class="sg-card"><h2>Wie hat sich der Vorschlag entwickelt?</h2><p class="sg-muted">Jede gespeicherte Fassung bleibt erhalten. Die aktuelle Version steht zuerst.</p><ol class="sg-work-history sg-proposal-versions"><?php foreach ($item->proposalRevisions as $proposalRevision): $topics = Json::decode($proposalRevision->topics_json) ?: []; ?><li><p><strong>Version <?= (int) $proposalRevision->version ?></strong> · am <?= Html::encode(date('d.m.Y', $proposalRevision->created_at)) ?> um <?= Html::encode(date('H:i', $proposalRevision->created_at)) ?> Uhr · präzisiert durch <?= Html::encode($proposalRevision->author?->displayName ?? 'Nicht verfügbar') ?></p><h3><?= Html::encode($proposalRevision->title) ?></h3><div class="sg-text"><?= $rich($proposalRevision->description) ?></div><?php if ($topics): ?><p class="sg-tags"><?php foreach ($topics as $topic): ?><span><?= Html::encode((string) $topic) ?></span><?php endforeach ?></p><?php endif ?></li><?php endforeach ?></ol></section><?php elseif ($item->proposalRevisions): ?><section class="sg-card"><h2>Wie hat sich der Vorschlag entwickelt?</h2><p class="sg-muted">Frühere Fassungen stammen aus einem geschützten Kreis und sind hier nicht sichtbar.</p></section><?php endif ?>
<section class="sg-card"><h2>Was ist bisher passiert?</h2><p class="sg-muted">Hier erzählt das Vorhaben seinen Weg: wichtige Schritte, Rückmeldungen und Entscheidungen.</p><?php if (count($visibleEvents) !== count($item->events)): ?><p class="sg-muted">Ein Teil des Verlaufs gehört zu einem geschützten früheren Kreis und wird hier nicht angezeigt.</p><?php endif ?><ol class="sg-work-history"><?php foreach ($visibleEvents as $event): ?><li><p><strong><?= Html::encode(WorkEvent::LABELS[$event->action] ?? $event->action) ?></strong> · am <?= Html::encode(date('d.m.Y', $event->created_at)) ?> um <?= Html::encode(date('H:i', $event->created_at)) ?> Uhr · durch <?= Html::encode($event->actor?->displayName ?? 'Nicht verfügbar') ?> · im Kreis <?= Html::encode($event->space->name) ?></p><?php if ($event->note): ?><div class="sg-text"><?= $rich($event->note) ?></div><?php endif ?><?php $before = Json::decode($event->before_json); $after = Json::decode($event->after_json); ?><details><summary>Was hat sich konkret geändert?</summary><dl><?php foreach ($after as $field => $value): if (in_array($field, ['revision', 'updated_at', 'created_at', 'id'], true) || ($before[$field] ?? null) === $value || ($event->action === 'delegate' && $field === 'space_id' && !$canReadSpace((int) ($before[$field] ?? 0)))) { continue; } ?><dt><?= Html::encode(['space_id' => 'Zuständiger Kreis', 'author_id' => 'Eingereicht von', 'assignee_id' => 'Zuständige Person', 'title' => 'Titel', 'description' => 'Beschreibung', 'kind' => 'Art', 'status' => 'Bearbeitungsstand', 'leader_approval_id' => 'Abnahme durch Kreisleitung', 'delegate_approval_id' => 'Abnahme durch Delegierte*r'][$field] ?? $field) ?></dt><dd><span class="sg-work-before"><?= Html::encode($historyValue($field, $before[$field] ?? null)) ?></span> → <?= Html::encode($historyValue($field, $value)) ?></dd><?php endforeach ?></dl></details></li><?php endforeach ?></ol></section>
<?php endif ?>
</div>
