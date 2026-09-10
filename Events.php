<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance;
use humhub\modules\sociocraticGovernance\services\Access;
use humhub\modules\sociocraticGovernance\services\WorkArchiver;
use humhub\modules\sociocraticGovernance\widgets\{CircleBadge, ProfileRoles, VCardGovernance};
use humhub\modules\ui\menu\MenuLink;
use humhub\helpers\ControllerHelper;
use humhub\modules\content\models\ContentContainerModuleState;
use humhub\modules\space\components\SpaceDirectoryQuery;
use humhub\modules\sociocraticGovernance\models\Circle;
use humhub\modules\sociocraticGovernance\models\fieldtype\CircleResponsibilities;
class Events
{
    public static function profileFieldTypes($event)
    {
        $event->sender->addFieldType(CircleResponsibilities::class, 'Kreise und Rollen');
    }
    public static function spaceMenu($event)
    {
        $space = $event->sender->space;
        if (!Access::read($space)) { return; }
        $event->sender->addEntry(new MenuLink([
            'label' => 'Projektkreis', 'icon' => 'users', 'sortOrder' => 210,
            'url' => $space->createUrl('/sociocratic-governance/circle/index'),
            'isActive' => \Yii::$app->controller && \Yii::$app->controller->module->id === 'sociocratic-governance',
        ]));
        $event->sender->addEntry(new MenuLink([
            'label' => 'Vorhaben', 'icon' => 'columns', 'sortOrder' => 211,
            'url' => $space->createUrl('/sociocratic-governance/work/index'),
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
            'id' => 'sociocratic-governance-directory', 'label' => 'Kreise', 'icon' => 'sitemap',
            'url' => ['/sociocratic-governance/directory/index'], 'sortOrder' => 245,
            'isActive' => ControllerHelper::isActivePath('sociocratic-governance', 'directory'),
        ]));
        $event->sender->addEntry(new MenuLink([
            'id' => 'sociocratic-governance-dashboard', 'label' => 'Mitwirken', 'icon' => 'hand-paper-o',
            'url' => ['/sociocratic-governance/dashboard/index'], 'sortOrder' => 246,
            'isActive' => ControllerHelper::isActivePath('sociocratic-governance', 'dashboard'),
        ]));
    }
    public static function filterSpaceDirectory($event)
    {
        if (!$event->sender instanceof SpaceDirectoryQuery) { return; }
        $enabledCircleIds = ContentContainerModuleState::find()->select('contentcontainer_id')->where([
            'module_id' => 'sociocratic-governance',
            'module_state' => [ContentContainerModuleState::STATE_ENABLED, ContentContainerModuleState::STATE_FORCE_ENABLED],
        ])->column();
        $competenceCircleIds = Circle::find()->select('space_id')->where(['type' => 'competence'])->column();
        $projectCircleIds = array_values(array_diff(array_map('intval', $enabledCircleIds), array_map('intval', $competenceCircleIds)));
        if ($projectCircleIds) {
            $event->query->andWhere(['not in', 'space.contentcontainer_id', $projectCircleIds]);
        }
    }
    public static function archiveDueWork($event)
    {
        (new WorkArchiver())->archiveDue();
    }
    public static function vCardCreate($event)
    {
        $module = \Yii::$app->getModule('popover-vcard');
        if (!$module || version_compare($module->getVersion(), '1.2.1', '<')
            || version_compare($module->getVersion(), '1.3.0', '>=')) { return; }
        if (!in_array($event->config['class'] ?? '', [\humhub\modules\popovervcard\widgets\VCardUser::class, \humhub\modules\popovervcard\widgets\VCardSpace::class], true)) { return; }
        $event->config['class'] = \humhub\modules\sociocraticGovernance\widgets\GovernanceVCard::class;
    }

    public static function vCardAddons($event)
    {
        $vCardModule = \Yii::$app->getModule('popover-vcard');
        if (!$vCardModule || version_compare($vCardModule->getVersion(), '1.2.1', '<')) {
            return;
        }
        $container = $event->sender->container ?? null;
        if ($container instanceof \humhub\modules\user\models\User || $container instanceof \humhub\modules\space\models\Space) {
            $event->sender->addWidget(VCardGovernance::class, ['container' => $container], ['sortOrder' => 100]);
        }
    }
}
