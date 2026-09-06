<?php
// SPDX-License-Identifier: AGPL-3.0-only
require __DIR__ . '/bootstrap.php';
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, Configuration, PermanentMembership};
use humhub\modules\sociocraticGovernance\services\{Access, CircleService};
use humhub\modules\space\models\Space;
$out = getenv('PREVIEW_DIR');
if (!$out || !is_dir($out)) { throw new RuntimeException('Set PREVIEW_DIR to an existing output directory.'); }
if (!is_dir($out . '/assets')) { mkdir($out . '/assets'); }
Yii::$app->set('request', ['class' => \yii\web\Request::class, 'cookieValidationKey' => 'local-render-test', 'scriptUrl' => '/index.php', 'hostInfo' => 'http://localhost', 'url' => '/preview']);
Yii::$app->set('urlManager', ['class' => \yii\web\UrlManager::class, 'scriptUrl' => '/index.php', 'baseUrl' => '']);
Yii::$app->set('assetManager', ['class' => \yii\web\AssetManager::class, 'basePath' => $out . '/assets', 'baseUrl' => '/assets']);
$_SERVER['REQUEST_METHOD'] = 'GET';
Yii::$app->set('response', ['class' => \yii\web\Response::class]);
$space = Space::findOne(1);
$service = new CircleService();
$service->save($space, new CircleForm([
    'purpose' => 'Menschen ermöglichen, gemeinsam Verantwortung zu übernehmen.',
    'mandate' => "Gemeinsame Ausrichtung und Rahmenbedingungen.\nEntscheidungsbereiche an Arbeitskreise übertragen.\nWiederwahl: alle sechs Monate.",
    'leader' => 1, 'delegate' => 2, 'facilitator' => 2,
]));
$service->save(Space::findOne(2), new CircleForm(['purpose' => 'Digitale Zusammenarbeit zuverlässig ermöglichen.', 'parent_space_id' => 1]));
$circle = Circle::findOne(1);
$circles = Access::visibleCircles();
$pages = [
    'circle' => ['circle/index', compact('space', 'circle', 'circles') + ['canWrite' => true]],
    'guide' => ['circle/guide', compact('space')],
    'edit' => ['circle/edit', ['space' => $space, 'form' => CircleForm::forCircle($circle), 'parents' => [2 => 'Technik'], 'members' => [1 => 'Alex', 2 => 'Robin']]],
    'directory' => ['directory/index', compact('circles')],
    'admin' => ['admin/index', ['config' => Configuration::findOne(1), 'permanent' => new PermanentMembership(), 'spaces' => [1 => 'Kern', 2 => 'Technik'], 'users' => [1 => 'Alex', 2 => 'Robin'], 'declarations' => []]],
];
foreach ($pages as $name => [$template, $params]) {
    $view = new \yii\web\View();
    ob_start();
    $view->beginPage();
    echo '<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Governance · Vorschau</title>';
    $view->head();
    echo '<style>body{font-family:Arial,sans-serif;background:#f3f5f6;margin:0;padding:32px;line-height:1.5}*{box-sizing:border-box}main{max-width:1040px;margin:auto}a{color:#176a64}.container{max-width:1040px;margin:auto}.alert-danger{color:#a00}li{margin-bottom:8px}</style></head><body>';
    $view->beginBody();
    echo '<main>' . $view->renderFile(dirname(__DIR__) . '/views/' . $template . '.php', $params) . '</main>';
    $view->endBody();
    echo '</body></html>';
    $view->endPage();
    $html = ob_get_clean();
    if (in_array($name, ['edit', 'admin'], true) && !str_contains($html, 'name="_csrf"')) {
        throw new RuntimeException('Missing CSRF field: ' . $name);
    }
    file_put_contents($out . '/' . $name . '.html', $html);
    echo "Rendered $name\n";
}
