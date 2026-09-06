<?php
// SPDX-License-Identifier: AGPL-3.0-only
// Load against real framework/core source without booting a full installation.
$yii = getenv('YII_FRAMEWORK');
$humhub = getenv('HUMHUB_SOURCE');
if (!$yii || !$humhub) { fwrite(STDERR, "Set YII_FRAMEWORK and HUMHUB_SOURCE (HumHub repository root).\n"); exit(2); }
require $yii . '/Yii.php';
Yii::setAlias('@humhub', $humhub . '/protected/humhub');
Yii::setAlias('@humhub/modules/sociocraticGovernance', dirname(__DIR__));
foreach ([
    'Module', 'Events', 'controllers\CircleController', 'controllers\DirectoryController',
    'controllers\AdminController', 'widgets\CircleBadge', 'widgets\ProfileRoles', 'assets\GovernanceAsset',
] as $class) {
    if (!class_exists('humhub\modules\sociocraticGovernance\\' . $class)) { throw new RuntimeException($class); }
    echo "Loaded against HumHub core: $class\n";
}
$config = require dirname(__DIR__) . '/config.php';
foreach ($config['events'] as $event) {
    if (!class_exists($event['class']) || !is_callable($event['callback'])) { throw new RuntimeException('Invalid event registration'); }
}
echo "All event classes and callbacks exist.\n";
