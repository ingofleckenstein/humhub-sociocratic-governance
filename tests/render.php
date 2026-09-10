<?php
// SPDX-License-Identifier: AGPL-3.0-only
require __DIR__ . '/bootstrap.php';
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, Configuration, PermanentMembership};
use humhub\modules\sociocraticGovernance\services\{Access, CircleDirectory, CircleService, ParticipationDashboard};
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
$permanentMembership = new PermanentMembership(['space_id' => 1, 'user_id' => 1, 'reason' => 'dauerhafte Kreisleitung']);
if (!$permanentMembership->save()) { throw new RuntimeException('Could not prepare permanent membership preview.'); }
$circles = Access::visibleCircles();
Space::$members[1][] = 3;
$directoryData = (new CircleDirectory())->data();
$workService = new \humhub\modules\sociocraticGovernance\services\WorkService();
$workIdea = $workService->create($space, '<script>Idee</script>', 'Ein Vorschlag mit <img src=x onerror=alert(1)>', 'idea', 'Commons, Sicherheit');
$workTask = $workService->create($space, 'Ein konkreter Arbeitsauftrag', 'Beschreibung', 'task', 'Technik');
$workTask = $workService->change($workTask->id, 0, 'resource', ['resource_type' => 'time', 'label' => 'Moderation', 'required_amount' => '8', 'available_amount' => '0', 'unit' => 'Stunden', 'time_mode' => 'scheduled', 'time_pattern' => 'Alle zwei Wochen donnerstags, 3 Stunden', 'details' => 'Für die erste Runde']);
Yii::$app->user->id = 3;
$workTask = $workService->change($workTask->id, 1, 'contribute', ['resource_id' => $workTask->resources[0]->id, 'amount' => '8']);
Yii::$app->user->id = 1;
$workTask = $workService->change($workTask->id, 2, 'claim');
$workTask = $workService->change($workTask->id, 3, 'start');
$workTask = $workService->change($workTask->id, 4, 'submit', ['note' => 'Das Ergebnis liegt vor.']);
$dashboardData = (new ParticipationDashboard())->data();
$pages = [
    'work-board' => ['work/index', ['space' => $space, 'items' => [$workIdea, $workTask], 'error' => '', 'draft' => new \humhub\modules\sociocraticGovernance\models\WorkItem(), 'draftTopics' => '']],
    'work-new' => ['work/new', ['space' => $space, 'error' => '', 'draft' => new \humhub\modules\sociocraticGovernance\models\WorkItem(), 'draftTopics' => '']],
    'work-idea' => ['work/view', ['space' => $space, 'item' => $workIdea, 'error' => '', 'section' => 'overview']],
    'work-review' => ['work/view', ['space' => $space, 'item' => $workTask, 'error' => '', 'section' => 'overview']],
    'work-resources' => ['work/view', ['space' => $space, 'item' => $workTask, 'error' => '', 'section' => 'resources']],
    'circle' => ['circle/index', compact('space', 'circle', 'circles') + ['canWrite' => true]],
    'circle-roles' => ['circle/index', compact('space', 'circle', 'circles') + [
        'canWrite' => true, 'canPublish' => true, 'permanentMemberships' => [$permanentMembership], 'section' => 'roles',
    ]],
    'guide' => ['circle/guide', compact('space')],
    'edit' => ['circle/edit', ['space' => $space, 'form' => CircleForm::forCircle($circle), 'parents' => [2 => 'Technik'], 'members' => [1 => 'Alex', 2 => 'Robin']]],
    'directory' => ['directory/index', $directoryData + compact('circles')],
    'dashboard' => ['dashboard/index', $dashboardData + ['focus' => 'topics']],
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
    if (in_array($name, ['edit', 'admin', 'work-new', 'work-idea', 'work-review', 'work-resources'], true) && !str_contains($html, 'name="_csrf"')) {
        throw new RuntimeException('Missing CSRF field: ' . $name);
    }
    if (str_starts_with($name, 'work-')) {
        if (str_contains($html, '<script>Idee') || str_contains($html, '<img src=x onerror')) {
            throw new RuntimeException('Unsafe work text output');
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//form') as $form) {
            if (strtolower($form->getAttribute('method')) !== 'post'
                || $xpath->query('.//input[@name="_csrf"]', $form)->length !== 1) {
                throw new RuntimeException('Work mutation form without POST/CSRF');
            }
            if (str_contains($form->getAttribute('action'), 'work%2Fchange')
                && $xpath->query('.//input[@name="revision"]', $form)->length !== 1) {
                throw new RuntimeException('Work action missing optimistic revision');
            }
        }
    }
    if ($name === 'directory') {
        // Browsers repair nested links by moving the content outside the bubble.
        preg_match_all('/<\/?a\b[^>]*>/i', $html, $anchors);
        $insideLink = false;
        foreach ($anchors[0] as $anchor) {
            if (str_starts_with(strtolower($anchor), '</a')) {
                $insideLink = false;
            } else {
                if ($insideLink) { throw new RuntimeException('Nested directory links'); }
                $insideLink = true;
            }
        }
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $map = $xpath->query('//div[@data-sg-circle-map]')->item(0);
        if (!$map || !$map->hasAttribute('data-graph')) { throw new RuntimeException('Missing map application data'); }
        $graph = json_decode(html_entity_decode($map->getAttribute('data-graph'), ENT_QUOTES | ENT_HTML5), true);
        if (!is_array($graph) || count($graph['nodes'] ?? []) !== count($directoryData['nodes'])) {
            throw new RuntimeException('Map application data omits visible circles');
        }
        if (array_key_exists('circle', $graph['nodes'][0] ?? []) || array_key_exists('user', $graph['nodes'][0] ?? [])) {
            throw new RuntimeException('Map application serializes server objects');
        }
        foreach ($graph['nodes'] as $node) {
            if (!isset($node['name'], $node['url'], $node['members'], $node['roles'], $node['mandate'])) {
                throw new RuntimeException('Map application node is incomplete');
            }
        }
    }
    if ($name === 'circle-roles' && (!str_contains($html, 'Dauerhafte Mitgliedschaft: dauerhafte Kreisleitung')
        || !str_contains($html, 'Space veröffentlichen') || str_contains($html, 'data-confirm'))) {
        throw new RuntimeException('Circle roles omit permanent membership or publication action.');
    }
    file_put_contents($out . '/' . $name . '.html', $html);
    echo "Rendered $name\n";
}
if (!str_contains(file_get_contents(dirname(__DIR__) . '/views/circle/index.php'), 'sgPublicationError')) {
    throw new RuntimeException('Circle view does not render publication failures.');
}
