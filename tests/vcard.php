<?php
// SPDX-License-Identifier: AGPL-3.0-only
// Uses real Twig/Sandbox and HTMLPurifier; no database or remote instance.
$vendor = getenv('TEST_VENDOR');
$yii = getenv('YII_FRAMEWORK');
if (!$vendor || !$yii) { fwrite(STDERR, "Set TEST_VENDOR and YII_FRAMEWORK.\n"); exit(2); }
require $vendor . '/autoload.php';
require $yii . '/Yii.php';
Yii::setAlias('@humhub/modules/sociocraticGovernance', dirname(__DIR__));
new yii\console\Application(['id' => 'vcard-test', 'basePath' => __DIR__, 'runtimePath' => sys_get_temp_dir() . '/governance-vcard-test']);
yii\helpers\FileHelper::createDirectory(Yii::getAlias('@runtime'));
use humhub\modules\sociocraticGovernance\services\VCardTemplate;
function verify($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
    echo "PASS: $message\n";
}
$data = ['user' => ['rolls' => 'Technik:Moderation'], 'profile' => ['about' => 'Hallo'],
    'space' => ['purpose' => 'Zweck', 'mandate' => 'Mandat']];
verify(VCardTemplate::render('{{ user.rolls }}', $data) === 'Technik:Moderation', 'Requested user role field renders');
verify(VCardTemplate::render('{space.purpose} {space.mandate}', $data) === 'Zweck Mandat', 'Legacy single-brace shortcodes render');
verify(VCardTemplate::render('{% if user.rolls %}{{ user.rolls|e }}{% endif %}', $data) === 'Technik:Moderation', 'Conditional role templates remain supported');
verify(VCardTemplate::render('{% if profile.about %}{{ profile.about }}{% endif %}', $data) === 'Hallo', 'Existing profile templates render');
$data['space']['purpose'] = '<script>alert(1)</script>{{ user.rolls }}';
$out = VCardTemplate::render('{{ space.purpose }}', $data);
verify(!str_contains($out, '<script>') && str_contains($out, '{{ user.rolls }}'), 'Values are escaped and never executed as Twig');
verify(!str_contains(VCardTemplate::render('<img src=x onerror=alert(1)>', $data), 'onerror'), 'Template HTML is sanitized');
verify(str_contains(VCardTemplate::render('{{ user.rolls|raw }}', $data), 'ungültige Vorlage'), 'Forbidden filters fail without breaking the card');
verify(str_contains(VCardTemplate::render('{% broken %}', $data), 'ungültige Vorlage'), 'Invalid template syntax keeps the card available');

$humhub = getenv('HUMHUB_SOURCE');
$vcard = getenv('VCARD_SOURCE');
if ($humhub && $vcard) {
    Yii::setAlias('@humhub', $humhub . '/protected/humhub');
    Yii::setAlias('@humhub/modules/popovervcard', $vcard);
    Yii::$app->set('user', new class extends yii\base\Component { public $isGuest = false; });
    $module = new class('popover-vcard', Yii::$app) extends yii\base\Module {
        public $version = '1.2.1';
        public function getVersion() { return $this->version; }
        public function getConfiguration() { return new stdClass(); }
    };
    Yii::$app->setModule('popover-vcard', $module);
    $config = require dirname(__DIR__) . '/config.php';
    foreach ($config['events'] as $event) {
        if (str_contains($event['class'], 'popovervcard')) {
            yii\base\Event::on($event['class'], $event['event'], $event['callback']);
        }
    }
    verify(humhub\modules\popovervcard\widgets\VCardUser::widget([]) === '', 'Real VCard user creation reaches Governance adapter');
    verify(humhub\modules\popovervcard\widgets\VCardSpace::widget([]) === '', 'Real VCard space creation reaches Governance adapter');
    $module->version = '1.3.0';
    $widgetConfig = ['class' => humhub\modules\popovervcard\widgets\VCardUser::class];
    humhub\modules\sociocraticGovernance\Events::vCardCreate(new humhub\libs\WidgetCreateEvent($widgetConfig));
    verify($widgetConfig['class'] === humhub\modules\popovervcard\widgets\VCardUser::class, 'Unverified VCard API version is not replaced');
}
