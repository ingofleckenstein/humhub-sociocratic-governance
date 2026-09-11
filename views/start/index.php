<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\helpers\Html;
use humhub\modules\sociocraticGovernance\models\WorkItem;
use humhub\modules\stream\widgets\StreamViewer;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
$circleUrl = static fn($circle) => $circle->space->createUrl('/sociocratic-governance/circle/index');
$workUrl = static fn($item, $section = 'overview') => $item->space->createUrl('/sociocratic-governance/work/view', ['id' => $item->id, 'section' => $section, 'from' => 'start']);
$notifications = $notifications ?? [];
$unseenNotificationCount = $unseenNotificationCount ?? 0;
$hasPersonalWork = $tasks || $contributions;
$hasPriority = $hasPersonalWork || $unseenNotificationCount;
$isReturning = $hasPriority || $circles || $spaces;
$name = Yii::$app->user->identity?->displayName;
?>
<div class="sg">
<header class="sg-hero"><span class="sg-eyebrow">Start</span><h1><?= $name ? ($isReturning ? 'Willkommen zurück, ' : 'Willkommen, ') . Html::encode($name) : 'Willkommen' ?></h1>
<?php if ($hasPriority): ?><p>Das braucht gerade deine Aufmerksamkeit. Danach kannst du schauen, wo du dich darüber hinaus einbringen möchtest.</p>
<?php elseif ($circles || $spaces): ?><p>Wähle, woran du heute mitwirken oder was du aus der Community mitnehmen möchtest.</p>
<?php else: ?><p>Finde einen ersten guten Anknüpfungspunkt: ein Thema, einen Kreis oder einen Space rund um ein Event.</p><?php endif ?></header>

<?php if ($hasPriority): ?><section class="sg-dashboard-section sg-start-priority"><h2>Für dich gerade wichtig</h2><div class="sg-grid">
<?php if ($unseenNotificationCount): ?><section class="sg-card"><h3>Neu für dich <span class="sg-count"><?= (int) $unseenNotificationCount ?></span></h3><ul class="sg-personal-list"><?php foreach ($notifications as $notification): ?><li><?= Html::a(Html::encode($notification['label']), $notification['url']) ?></li><?php endforeach ?></ul><?php if ($unseenNotificationCount > count($notifications)): ?><p class="sg-muted">Und <?= (int) ($unseenNotificationCount - count($notifications)) ?> weitere Hinweise.</p><?php endif ?><p><?= Html::a('Alle Benachrichtigungen öffnen', ['/notification/overview']) ?></p></section><?php endif ?>
<?php if ($tasks): ?><section class="sg-card"><h3>Meine Aufgaben <span class="sg-count"><?= count($tasks) ?></span></h3><ul class="sg-personal-list"><?php foreach ($tasks as $item): ?><li><?= Html::a(Html::encode($item->title), $workUrl($item)) ?><small><?= Html::encode(WorkItem::STATUSES[$item->status] ?? $item->status) ?> · <?= Html::encode($item->space->name) ?></small></li><?php endforeach ?></ul></section><?php endif ?>
<?php if ($contributions): ?><section class="sg-card"><h3>Meine Zusagen <span class="sg-count"><?= count($contributions) ?></span></h3><ul class="sg-personal-list"><?php foreach ($contributions as $contribution): $resource = $contribution->resource; $item = $resource->workItem; ?><li><?= Html::a(Html::encode($resource->label), $workUrl($item, 'resources')) ?><small>Für <?= Html::encode($item->title) ?> · <?= Html::encode($item->space->name) ?></small></li><?php endforeach ?></ul></section><?php endif ?>
</div></section><?php endif ?>

<section class="sg-dashboard-section sg-start-next"><h2><?= $hasPriority ? 'Was möchtest du außerdem tun?' : 'Dein nächster Schritt' ?></h2><div class="sg-start-actions">
<?= Html::a('Mitwirken und Möglichkeiten finden', ['/sociocratic-governance/dashboard/index'], ['class' => 'sg-button']) ?>
<?= Html::a('Kreise entdecken', ['/sociocratic-governance/directory/index'], ['class' => 'sg-button sg-button-secondary']) ?>
<?= Html::a('Spaces entdecken', ['/space/spaces'], ['class' => 'sg-button sg-button-secondary']) ?>
</div><p class="sg-muted">Aktuelles aus der Community findest du in der <?= Html::a('Aktivität', ['/dashboard/dashboard']) ?>.</p></section>

<details class="sg-card sg-collapsible sg-affiliations"><summary><h2>Meine Zugehörigkeiten</h2><span><?= count($circles) ?> Kreise · <?= count($spaces) ?> Spaces</span></summary>
<p class="sg-muted">Kreise sind Arbeits- und Kompetenzkreise. Spaces können auch sichere Gruppen rund um ein Event sein.</p><div class="sg-grid">
<section><h3>Meine Kreise</h3><?php if ($circles): ?><ul class="sg-personal-list"><?php foreach ($circles as $circle): ?><li><span class="<?= Html::encode($circle->colorClass()) ?> sg-personal-dot"></span><?= Html::a(Html::encode($circle->space->name), $circleUrl($circle)) ?><small><?= $circle->isCompetenceCircle() ? 'Kompetenzkreis' : 'Projektkreis' ?></small></li><?php endforeach ?></ul><?php else: ?><p class="sg-muted">Du bist derzeit keinem Kreis zugeordnet.</p><?php endif ?></section>
<section><h3>Meine Spaces</h3><?php if ($spaces): ?><ul class="sg-personal-list"><?php foreach ($spaces as $space): ?><li><?= Html::a(Html::encode($space->name), $space->getUrl()) ?></li><?php endforeach ?></ul><?php else: ?><p class="sg-muted">Du bist derzeit keinem allgemeinen Space zugeordnet.</p><?php endif ?></section>
</div></details>

<section class="sg-dashboard-section sg-start-activity"><h2>Letzte Aktivitäten</h2><p>Was in deinen sichtbaren Bereichen zuletzt passiert ist.</p>
<?= StreamViewer::widget([
    'options' => ['class' => 'dashboard-wall-stream sg-start-stream'],
    'streamAction' => '//dashboard/dashboard/stream',
    'showFilters' => false,
    'messageStreamEmpty' => 'Hier gibt es für dich noch keine sichtbaren Aktivitäten.',
]) ?>
</section>
</div>
