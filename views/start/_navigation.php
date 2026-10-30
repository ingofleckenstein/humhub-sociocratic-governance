<?php
// SPDX-License-Identifier: AGPL-3.0-only
use humhub\helpers\Html;
use humhub\modules\space\widgets\Image;
?>
<aside class="sg-start-sidebar" aria-label="Meine Chats und Spaces">
<details class="sg-start-navigation" open>
<summary>Chats &amp; Spaces <span class="sg-start-navigation__chevron" aria-hidden="true">⌄</span></summary>
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
<ul>
<?php foreach ($navigationSpaces as $item): $space = $item['space']; ?>
<li><a href="<?= Html::encode($space->getUrl()) ?>">
<?= Image::widget(['space' => $space, 'width' => 28, 'link' => false, 'showTooltip' => false]) ?>
<span class="sg-start-navigation__label"><?= Html::encode($space->displayName) ?></span>
<?php if ($item['unread']): ?><span class="sg-unread-dot" role="img" aria-label="Neue Beiträge seit deinem letzten Besuch" title="Neue Beiträge seit deinem letzten Besuch"></span><?php endif ?>
</a></li>
<?php endforeach ?>
<?php if (!$navigationSpaces): ?><li class="sg-muted">Noch keine Mitgliedschaften</li><?php endif ?>
</ul>
</nav>
</details>
</aside>
