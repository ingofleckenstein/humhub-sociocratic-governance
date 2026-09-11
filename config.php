<?php
// SPDX-License-Identifier: AGPL-3.0-only
use humhub\modules\sociocraticGovernance\Events;

$events = [
    ['class' => \humhub\modules\space\widgets\Menu::class, 'event' => \humhub\modules\space\widgets\Menu::EVENT_INIT, 'callback' => [Events::class, 'spaceMenu']],
    ['class' => \humhub\modules\space\widgets\Sidebar::class, 'event' => \humhub\modules\space\widgets\Sidebar::EVENT_INIT, 'callback' => [Events::class, 'spaceSidebar']],
    ['class' => \humhub\modules\user\widgets\ProfileSidebar::class, 'event' => \humhub\modules\user\widgets\ProfileSidebar::EVENT_INIT, 'callback' => [Events::class, 'profileSidebar']],
    ['class' => \humhub\modules\user\models\fieldtype\BaseType::class, 'event' => \humhub\modules\user\models\fieldtype\BaseType::EVENT_INIT, 'callback' => [Events::class, 'profileFieldTypes']],
    ['class' => \humhub\widgets\TopMenu::class, 'event' => \humhub\widgets\TopMenu::EVENT_INIT, 'callback' => [Events::class, 'topMenu']],
    ['class' => \humhub\widgets\TopMenu::class, 'event' => \humhub\widgets\TopMenu::EVENT_RUN, 'callback' => [Events::class, 'topMenuRun']],
    ['class' => \humhub\widgets\MetaSearchWidget::class, 'event' => \humhub\widgets\MetaSearchWidget::EVENT_INIT, 'callback' => [Events::class, 'metaSearch']],
    ['class' => \humhub\modules\space\components\ActiveQuerySpace::class, 'event' => \humhub\modules\space\components\ActiveQuerySpace::EVENT_CHECK_VISIBILITY, 'callback' => [Events::class, 'filterSpaceDirectory']],
    ['class' => \humhub\commands\CronController::class, 'event' => \humhub\commands\CronController::EVENT_ON_DAILY_RUN, 'callback' => [Events::class, 'archiveDueWork']],
];

// Register optional class names without autoloading them during module discovery.
// The callbacks check the installed version; absent modules never trigger events.
$events[] = ['class' => 'humhub\\modules\\popovervcard\\widgets\\VCardAddons', 'event' => 'run', 'callback' => [Events::class, 'vCardAddons']];
foreach (['VCardUser', 'VCardSpace'] as $widget) {
    $events[] = ['class' => 'humhub\\modules\\popovervcard\\widgets\\' . $widget, 'event' => 'create', 'callback' => [Events::class, 'vCardCreate']];
}

return [
    'id' => 'sociocratic-governance',
    'class' => \humhub\modules\sociocraticGovernance\Module::class,
    'namespace' => 'humhub\modules\sociocraticGovernance',
    'events' => $events,
];
