<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance;
use humhub\modules\sociocraticGovernance\services\Access;
use humhub\modules\sociocraticGovernance\widgets\{CircleBadge, ProfileRoles};
use humhub\modules\ui\menu\MenuLink;
class Events
{
    public static function spaceMenu($event)
    {
        $space = $event->sender->space;
        if (!Access::read($space)) { return; }
        $event->sender->addEntry(new MenuLink([
            'label' => 'Arbeitskreis', 'icon' => 'users', 'sortOrder' => 210,
            'url' => $space->createUrl('/sociocratic-governance/circle/index'),
            'isActive' => \Yii::$app->controller && \Yii::$app->controller->module->id === 'sociocratic-governance',
        ]));
    }
    public static function spaceSidebar($event)
    {
        if (Access::read($event->sender->space)) {
            $event->sender->addWidget(CircleBadge::class, ['space' => $event->sender->space], ['sortOrder' => 20]);
        }
    }
    public static function profileSidebar($event)
    {
        if (!\Yii::$app->user->isGuest) {
            $event->sender->addWidget(ProfileRoles::class, ['user' => $event->sender->user], ['sortOrder' => 25]);
        }
    }
}
