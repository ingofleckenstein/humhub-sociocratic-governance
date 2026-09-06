<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance;
use humhub\modules\sociocraticGovernance\services\Access;
use humhub\modules\sociocraticGovernance\widgets\{CircleBadge, ProfileRoles};
use humhub\modules\ui\menu\MenuLink;
use humhub\helpers\ControllerHelper;
use humhub\modules\content\models\ContentContainerModuleState;
use humhub\modules\space\components\SpaceDirectoryQuery;
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
    public static function topMenu($event)
    {
        if (\Yii::$app->user->isGuest) { return; }
        $event->sender->addEntry(new MenuLink([
            'id' => 'sociocratic-governance-directory', 'label' => 'Arbeitskreise', 'icon' => 'sitemap',
            'url' => ['/sociocratic-governance/directory/index'], 'sortOrder' => 245,
            'isActive' => ControllerHelper::isActivePath('sociocratic-governance', 'directory'),
        ]));
    }
    public static function filterSpaceDirectory($event)
    {
        if (!$event->sender instanceof SpaceDirectoryQuery) { return; }
        $enabledCircles = ContentContainerModuleState::find()->select('contentcontainer_id')->where([
            'module_id' => 'sociocratic-governance',
            'module_state' => [ContentContainerModuleState::STATE_ENABLED, ContentContainerModuleState::STATE_FORCE_ENABLED],
        ]);
        $event->query->andWhere(['not in', 'space.contentcontainer_id', $enabledCircles]);
    }
}
