<?php
// SPDX-License-Identifier: AGPL-3.0-only
use humhub\modules\sociocraticGovernance\Events;
return [
    'id' => 'sociocratic-governance',
    'class' => \humhub\modules\sociocraticGovernance\Module::class,
    'namespace' => 'humhub\modules\sociocraticGovernance',
    'events' => [
        ['class' => \humhub\modules\space\widgets\Menu::class, 'event' => \humhub\modules\space\widgets\Menu::EVENT_INIT, 'callback' => [Events::class, 'spaceMenu']],
        ['class' => \humhub\modules\space\widgets\Sidebar::class, 'event' => \humhub\modules\space\widgets\Sidebar::EVENT_INIT, 'callback' => [Events::class, 'spaceSidebar']],
        ['class' => \humhub\modules\user\widgets\ProfileSidebar::class, 'event' => \humhub\modules\user\widgets\ProfileSidebar::EVENT_INIT, 'callback' => [Events::class, 'profileSidebar']],
    ],
];
