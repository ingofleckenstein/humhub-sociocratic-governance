<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance;

use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\content\permissions\CreatePublicContent;
use humhub\modules\space\models\Space;
use humhub\libs\BasePermission;

class Module extends ContentContainerModule
{
    public function getContentContainerTypes() { return [Space::class]; }
    public function getContentContainerName(ContentContainerActiveRecord $container) { return 'Arbeitskreis'; }
    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        return 'Kennzeichnet diesen Space als Arbeitskreis: Mandat, Rollen und methodische Orientierung.';
    }
    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        return $container->createUrl('/sociocratic-governance/circle/index');
    }
    public function getConfigUrl() { return \yii\helpers\Url::to(['/sociocratic-governance/admin/index']); }
    public function enableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::enableContentContainer($container);
        if (!$container instanceof Space) { return; }
        $container->visibility = Space::VISIBILITY_REGISTERED_ONLY;
        $container->join_policy = Space::JOIN_POLICY_APPLICATION;
        $container->default_content_visibility = Content::VISIBILITY_PUBLIC;
        if (!$container->save(false, ['visibility', 'join_policy', 'default_content_visibility'])) {
            throw new \RuntimeException('Die Arbeitskreis-Voreinstellungen konnten nicht gespeichert werden.');
        }
        $container->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePublicContent::class, BasePermission::STATE_ALLOW);
        $container->permissionManager->setGroupState(Space::USERGROUP_USER, CreatePublicContent::class, BasePermission::STATE_DENY);
    }
    // Governance records deliberately survive disabling, including container settings.
    public function disableContentContainer(ContentContainerActiveRecord $container) {}
}
