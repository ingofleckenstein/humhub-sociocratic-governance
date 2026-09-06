<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance;

use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\space\models\Space;

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
    // Governance records deliberately survive disabling, including container settings.
    public function disableContentContainer(ContentContainerActiveRecord $container) {}
}
