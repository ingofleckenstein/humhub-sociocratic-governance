<?php
// SPDX-License-Identifier: AGPL-3.0-only
use humhub\helpers\Html;
use humhub\modules\space\widgets\Image;
?>
<ul>
<?php foreach ($items as $item): $space = $item['space']; ?>
<li><a href="<?= Html::encode($circles ? $space->createUrl('/sociocratic-governance/circle/index') : $space->getUrl()) ?>">
<?= Image::widget(['space' => $space, 'width' => 28, 'link' => false, 'showTooltip' => false]) ?>
<span class="sg-start-navigation__label"><?= Html::encode($space->displayName) ?></span>
<?php if ($item['unread']): ?><span class="sg-unread-dot" role="img" aria-label="Neue Beiträge seit deinem letzten Besuch" title="Neue Beiträge seit deinem letzten Besuch"></span><?php endif ?>
</a></li>
<?php endforeach ?>
<?php if (!$items): ?><li class="sg-muted">Noch keine Mitgliedschaften in diesem Bereich</li><?php endif ?>
</ul>
