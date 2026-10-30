<?php
// SPDX-License-Identifier: AGPL-3.0-only
use humhub\helpers\Html;
use humhub\modules\space\widgets\Image;
?>
<aside class="sg-start-sidebar" aria-label="Meine Chats, Spaces und Kreise">
<details class="sg-start-navigation" open>
<summary>Chats, Spaces &amp; Kreise <span class="sg-start-navigation__chevron" aria-hidden="true">⌄</span></summary>
<nav aria-label="Persönliche Übersicht">
<h2><?= Html::a('Chats', ['/conversations/overview/index']) ?></h2>
<ul>
<?php $hasChats = false; foreach ($chatGroups as $group): ?>
<?php foreach ($group['conversations'] as $chat): $hasChats = true; $unread = !$group['isMuted'] && ($group['unreadCounts'][(int) $chat->id] ?? 0) > 0; ?>
<li><a href="<?= Html::encode($chat->url) ?>" title="<?= Html::encode($group['space']->displayName) ?>">
<?= Image::widget(['space' => $group['space'], 'width' => 28, 'link' => false, 'showTooltip' => false]) ?>
<span class="sg-start-navigation__label"><?= Html::encode($chat->title) ?></span>
<?php if ($unread): ?><span class="sg-unread-dot" role="img" aria-label="Ungelesene Nachrichten" title="Ungelesene Nachrichten"></span><?php endif ?>
</a></li>
<?php endforeach; endforeach ?>
<?php if (!$hasChats): ?><li class="sg-muted">Noch keine Chats</li><?php endif ?>
</ul>
<hr>
<h2>Meine Spaces</h2>
<?= $this->render('_navigation-items', ['items' => $navigationSpaces, 'circles' => false]) ?>
<hr>
<h2>Meine Kreise</h2>
<h3>Projektkreise</h3>
<?= $this->render('_navigation-items', ['items' => $navigationProjectCircles ?? [], 'circles' => true]) ?>
<h3>Kompetenzkreise</h3>
<?= $this->render('_navigation-items', ['items' => $navigationCompetenceCircles ?? [], 'circles' => true]) ?>
</nav>
</details>
</aside>
