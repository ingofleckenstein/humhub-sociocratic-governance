<?php
$root = $argv[1] ?? getenv('HUMHUB_ROOT');
if (!$root || !is_file($root . '/protected/yii')) { throw new RuntimeException('Provide a test HumHub root.'); }
$protected = $root . '/protected';
$loader = require $protected . '/vendor/autoload.php';
Dotenv\Dotenv::createMutable($root, '.env')->safeLoad();
$loader->addClassMap(['humhub\\services\\BootstrapService' => $protected . '/humhub/services/BootstrapService.php']);
define('YII_DEBUG', false); define('YII_ENV', 'prod');
require $protected . '/vendor/yiisoft/yii2/Yii.php';
Yii::setAlias('@humhub', $protected . '/humhub');
$_SERVER['REQUEST_URI'] = '/';
$config = (new humhub\services\BootstrapService())->getConfig('web');
$config['components']['request']['scriptFile'] = $root . '/index.php';
$config['components']['request']['scriptUrl'] = '/index.php';
$config['components']['request']['hostInfo'] = 'http://localhost';
new humhub\components\Application($config);
Yii::$app->view->theme->register();

$users = humhub\modules\user\models\User::find()->where(['status' => 1])->orderBy(['id' => SORT_ASC])->limit(3)->all();
foreach ($users as $user) {
    Yii::$app->user->setIdentity($user);
    $data = (new humhub\modules\sociocraticGovernance\services\PersonalDashboard())->data();
    foreach ($data['navigationSpaces'] as $item) {
        if (!$item['space']->isMember($user->id)) { throw new RuntimeException('Non-member space in sidebar'); }
    }
    $controller = new humhub\modules\sociocraticGovernance\controllers\StartController('start', Yii::$app->getModule('sociocratic-governance'));
    Yii::$app->controller = $controller;
    foreach ($data['chatGroups'] as $group) {
        foreach ($group['conversations'] as $chat) {
            if (!$chat->content->canView($user) || $chat->content->archived) { throw new RuntimeException('Unreadable or archived chat in navigation'); }
        }
    }
    $start = $controller->actionIndex();

    if (substr_count($start, 'class="sg-start-sidebar"') !== 1 || !str_contains($start, 'Meine Spaces')) { throw new RuntimeException('Sidebar missing'); }
    $controller = new humhub\modules\conversations\controllers\OverviewController('overview', Yii::$app->getModule('conversations'));
    Yii::$app->controller = $controller;
    $overview = $controller->actionIndex();
    if (substr_count($overview, 'class="conversation-overview__head"') !== 1 || substr_count($overview, 'role="table"') !== 1) { throw new RuntimeException('Overview must contain one table and heading'); }


    echo 'PASS user ' . $user->id . ': permission-aware sidebar, dashboard rendering, single chat table; spaces=' . count($data['navigationSpaces']) . PHP_EOL;
}


Yii::$app->user->setIdentity(null);
$controller = new humhub\modules\sociocraticGovernance\controllers\StartController('start', Yii::$app->getModule('sociocratic-governance'));
Yii::$app->controller = $controller;
try {
    $action = new yii\base\Action('index', $controller);
    $controller->action = $action;
    if ($controller->beforeAction($action)) { throw new RuntimeException('Guest dashboard access allowed'); }
} catch (yii\web\ForbiddenHttpException $e) {}
echo "PASS guest dashboard access denied\n";
