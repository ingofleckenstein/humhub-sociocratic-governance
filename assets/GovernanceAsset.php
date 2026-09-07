<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\assets;
class GovernanceAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__ . '/../resources';
    public $css = ['governance.css?v=20260907.18'];
    public $js = ['governance-directory.js?v=20260907.18'];
}
