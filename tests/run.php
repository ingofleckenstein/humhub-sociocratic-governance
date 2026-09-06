<?php
// SPDX-License-Identifier: AGPL-3.0-only
require __DIR__ . '/bootstrap.php';
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, Role};
use humhub\modules\sociocraticGovernance\services\{Access, CircleDirectory, CircleService};
use humhub\modules\space\models\Space;

$count = 0;
function check($condition, string $message): void {
    global $count;
    if (!$condition) { throw new RuntimeException($message); }
    $count++; echo "PASS: $message\n";
}
$service = new CircleService();
$space = Space::findOne(1);
$form = new CircleForm(['purpose' => 'Gemeinschaft stärken', 'mandate' => 'Gesamtmandat', 'leader' => 1, 'delegate' => 2]);
check($service->save($space, $form), 'First circle and roles persist through migration with table prefix');
check(Role::find()->count() == 2, 'Both linking roles stored');
check(Circle::findOne(1)->reelection_interval === 'Alle 6 Monate', 'New circles default to six-month reelection');
$stale = CircleForm::forCircle(Circle::findOne(1));
$fresh = CircleForm::forCircle(Circle::findOne(1));
$fresh->purpose = 'Neuer Stand';
check($service->save($space, $fresh), 'Update persists');
check(!$service->save($space, $stale), 'Stale form cannot overwrite newer changes');
$invalid = CircleForm::forCircle(Circle::findOne(1));
$invalid->delegate = 1;
check(!$service->save($space, $invalid), 'Leader and delegate cannot be the same person');
check(Role::findOne(['space_id' => 1, 'role_key' => 'delegate'])->user_id == 2, 'Failed update preserves original roles');
$invalid = CircleForm::forCircle(Circle::findOne(1));
$invalid->leader = 3;
check(!$service->save($space, $invalid), 'Nonmember cannot receive a role');
$child = new CircleForm(['parent_space_id' => 1]);
check($service->save(Space::findOne(2), $child), 'Child circle can reference parent');
$directory = (new CircleDirectory())->data();
check(count($directory['rows']) === 2 && $directory['rows'][0]['depth'] === 0 && $directory['rows'][1]['depth'] === 1, 'Directory returns visible circles as an indented hierarchy');
$cycle = CircleForm::forCircle(Circle::findOne(1));
$cycle->parent_space_id = 2;
check(!$service->save($space, $cycle), 'Indirect circle cycle rejected');
$self = CircleForm::forCircle(Circle::findOne(1));
$self->parent_space_id = 1;
check(!$service->save($space, $self), 'Self-parent rejected');
Yii::$app->user->id = 3;
check(Access::read($space), 'Registered nonmember can read visible circle');
check(!Access::write($space), 'Registered nonmember cannot write');
try { $service->save($space, new CircleForm()); check(false, 'Write guard'); }
catch (\yii\web\ForbiddenHttpException $e) { check(true, 'Service independently rejects unauthorized writes'); }
check(!Access::read(Space::findOne(3)), 'Private space hidden from nonmember');
Yii::$app->user->isGuest = true;
check(!Access::read($space), 'Guest cannot read even visible circle');
Yii::$app->user->isGuest = false; Yii::$app->user->id = 1;
Space::$archived = [1];
check(!Access::write($space), 'Archived space cannot be edited');
Space::$archived = []; Space::$disabled = [1];
check(!Access::read($space), 'Disabled module hides circle data');
Space::$disabled = []; Space::$blocked = [1];
check(!Access::read($space), 'Blocked viewer cannot access circle');
Space::$blocked = [];
Yii::$app->db->createCommand()->update('{{%sg_config}}', ['root_space_id' => 1], ['id' => 1])->execute();
$root = CircleForm::forCircle(Circle::findOne(1)); $root->parent_space_id = 2;
check(!$service->save($space, $root), 'Root cannot acquire parent');
$user = \humhub\modules\user\models\User::findOne(1);
$html = (new \humhub\modules\sociocraticGovernance\widgets\ProfileRoles(['user' => $user]))->run();
check(str_contains($html, 'Kreisleitung'), 'Profile displays active role');
Space::$members[1] = [2];
check((new \humhub\modules\sociocraticGovernance\widgets\ProfileRoles(['user' => $user]))->run() === '', 'Former member role hidden from profile');
Space::$members[1] = [1,2];
$name = '<script>alert(1)</script>';
Yii::$app->db->createCommand()->update('{{%space}}', ['name' => $name], ['id' => 1])->execute();
$html = (new \humhub\modules\sociocraticGovernance\widgets\ProfileRoles(['user' => $user]))->run();
check(!str_contains($html, '<script>') && str_contains($html, '&lt;script&gt;'), 'Profile escapes malicious names');
$transfer = CircleForm::forCircle(Circle::findOne(1));
$transfer->leader = 2; $transfer->delegate = 1;
check($service->save($space, $transfer), 'Space owner can assign a new circle leader');
check(Space::$owners[1] === 2, 'Circle leader becomes space owner');
Yii::$app->db->createCommand()->delete('{{%user}}', ['id' => 2])->execute();
check(!Role::find()->where(['user_id' => 2])->exists(), 'User deletion cascades role references');
echo "$count checks passed.\n";
