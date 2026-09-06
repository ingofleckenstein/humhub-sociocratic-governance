<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\assets;
class GovernanceAsset extends \yii\web\AssetBundle
{
    public $sourcePath = __DIR__ . '/../resources';
    public $css = ['governance.css'];
    public $js = ['governance-directory.js'];
}
