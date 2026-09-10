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
    public function getContentContainerName(ContentContainerActiveRecord $container) { return 'Kreis'; }
    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        return 'Kennzeichnet diesen Space als Projekt- oder Kompetenzkreis: Mandat, Rollen und methodische Orientierung.';
    }
    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        return $container->createUrl('/sociocratic-governance/circle/edit');
    }
    public function getConfigUrl() { return \yii\helpers\Url::to(['/sociocratic-governance/admin/index']); }
    public function enableContentContainer(ContentContainerActiveRecord $container)
    {
        parent::enableContentContainer($container);
        if (!$container instanceof Space) { return; }
        // A new circle is prepared as a private draft. Publication is an
        // explicit action once its mandate is ready to share.
        $container->visibility = Space::VISIBILITY_NONE;
        $container->join_policy = Space::JOIN_POLICY_NONE;
        $container->default_content_visibility = Content::VISIBILITY_PRIVATE;
        if (!$container->save(false, ['visibility', 'join_policy', 'default_content_visibility'])) {
            throw new \RuntimeException('Die Kreis-Voreinstellungen konnten nicht gespeichert werden.');
        }
        $container->permissionManager->setGroupState(Space::USERGROUP_MEMBER, CreatePublicContent::class, BasePermission::STATE_ALLOW);
        $container->permissionManager->setGroupState(Space::USERGROUP_USER, CreatePublicContent::class, BasePermission::STATE_DENY);
        $messages = \humhub\modules\sociocraticGovernance\services\RequiredModules::enable($container);
        if ($messages && \Yii::$app->has('session')) {
            \Yii::$app->session->setFlash('warning', implode(' ', $messages));
        }

    }
    // Governance records deliberately survive disabling, including container settings.
    public function disableContentContainer(ContentContainerActiveRecord $container) {}
}
