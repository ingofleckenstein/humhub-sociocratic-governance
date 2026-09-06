<?php
// SPDX-License-Identifier: AGPL-3.0-only
use humhub\modules\sociocraticGovernance\Events;

$events = [
    ['class' => \humhub\modules\space\widgets\Menu::class, 'event' => \humhub\modules\space\widgets\Menu::EVENT_INIT, 'callback' => [Events::class, 'spaceMenu']],
    ['class' => \humhub\modules\space\widgets\Sidebar::class, 'event' => \humhub\modules\space\widgets\Sidebar::EVENT_INIT, 'callback' => [Events::class, 'spaceSidebar']],
    ['class' => \humhub\modules\user\widgets\ProfileSidebar::class, 'event' => \humhub\modules\user\widgets\ProfileSidebar::EVENT_INIT, 'callback' => [Events::class, 'profileSidebar']],
    ['class' => \humhub\widgets\TopMenu::class, 'event' => \humhub\widgets\TopMenu::EVENT_INIT, 'callback' => [Events::class, 'topMenu']],
    ['class' => \humhub\modules\space\components\ActiveQuerySpace::class, 'event' => \humhub\modules\space\components\ActiveQuerySpace::EVENT_CHECK_VISIBILITY, 'callback' => [Events::class, 'filterSpaceDirectory']],
];

// Popover VCard is deliberately optional. The runtime callback verifies the
// supported 1.2.1+ release before it adds Governance content to a card.
if (class_exists(\humhub\modules\popovervcard\widgets\VCardAddons::class)) {
    $events[] = ['class' => \humhub\modules\popovervcard\widgets\VCardAddons::class, 'event' => \humhub\modules\popovervcard\widgets\VCardAddons::EVENT_RUN, 'callback' => [Events::class, 'vCardAddons']];
}

return [
    'id' => 'sociocratic-governance',
    'class' => \humhub\modules\sociocraticGovernance\Module::class,
    'namespace' => 'humhub\modules\sociocraticGovernance',
    'events' => $events,
];
