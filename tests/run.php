<?php
// SPDX-License-Identifier: AGPL-3.0-only
require __DIR__ . '/bootstrap.php';
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, Configuration, Role};
use humhub\modules\sociocraticGovernance\services\{Access, CircleDirectory, CircleService, VCardData};
use humhub\modules\space\models\Space;

$count = 0;
function check($condition, string $message): void {
    global $count;
    if (!$condition) { throw new RuntimeException($message); }
    $count++; echo "PASS: $message\n";
}
$service = new CircleService();
$space = Space::findOne(1);
$configuration = Configuration::findOne(1);
$configuration->company_user_id = 3;
check($configuration->save() && (int) Configuration::findOne(1)->company_user_id === 3, 'Optional company account persists for stream announcements');
$configuration->company_user_id = null;
$configuration->save(false);
$form = new CircleForm(['purpose' => 'Gemeinschaft stärken', 'mandate' => 'Gesamtmandat', 'leader' => 1, 'delegate' => 2]);
check($service->save($space, $form), 'First circle and roles persist through migration with table prefix');
check(Role::find()->count() == 2, 'Both linking roles stored');
check(!Circle::findOne(1)->is_published, 'A newly created circle starts as a private draft');
$initialCircle = Circle::findOne(1);
$initialCircle->is_published = 1;
$initialCircle->save(false);
check(Circle::findOne(1)->reelection_interval === 'Alle 6 Monate', 'New circles default to six-month reelection');
$competence = CircleForm::forCircle(Circle::findOne(1));
$competence->type = 'competence';
check($service->save($space, $competence) && Circle::findOne(1)->isCompetenceCircle(), 'Competence circle type persists');
$project = CircleForm::forCircle(Circle::findOne(1));
$project->type = 'project';
check($service->save($space, $project) && !Circle::findOne(1)->isCompetenceCircle(), 'Project circle remains the default type');
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
check(Circle::findOne(1)->color !== Circle::findOne(2)->color, 'New circles automatically receive distinct colors');
$draftChild = Circle::findOne(2);
$draftChild->mandate_summary = 'Digitale Zusammenarbeit verlässlich ermöglichen.';
$draftChild->save(false);
Yii::$app->user->id = 3;
check(!Access::read(Space::findOne(2)), 'A draft circle is hidden from non-admins');
Yii::$app->user->id = 1;
$configuration->company_user_id = 3;
$configuration->save(false);
$service->publish(Space::findOne(2));
$publishedChild = Circle::findOne(2);
check((bool) $publishedChild->is_published && (int) Space::findOne(2)->visibility === Space::VISIBILITY_REGISTERED_ONLY,
    'Publishing makes a drafted circle visible to registered users');
$announcement = \humhub\modules\post\models\Post::find()->orderBy(['id' => SORT_DESC])->one();
check(str_contains($announcement->message, 'Digitale Zusammenarbeit verlässlich ermöglichen.')
    && str_contains($announcement->message, 'Kreis ansehen') && str_contains($announcement->message, 'jederzeit beitreten'),
    'Publishing creates the linked mandate announcement');
try { $service->publish(Space::findOne(2)); check(false, 'A circle can only be published once'); }
catch (\DomainException $e) { check(true, 'A circle can only be published once'); }
$configuration->company_user_id = null;
$configuration->save(false);
$duplicateColor = CircleForm::forCircle(Circle::findOne(2));
$duplicateColor->color = Circle::findOne(1)->color;
check(!$service->save(Space::findOne(2), $duplicateColor), 'A circle cannot take a color already assigned to another circle');
$configuration->root_space_id = 1;
$configuration->save(false);
Space::$members[3][] = 1;
$competenceForm = new CircleForm(['type' => 'competence']);
check($service->save(Space::findOne(3), $competenceForm), 'Visible competence circle can be created for directory grouping');
$directory = (new CircleDirectory())->data();
check(array_map(static fn(array $row): int => (int) $row['circle']->space_id, $directory['projectRows']) === [1, 2]
    && array_map(static fn(array $row): int => (int) $row['circle']->space_id, $directory['competenceRows']) === [3]
    && $directory['projectRows'][0]['depth'] === 0 && $directory['projectRows'][1]['depth'] === 1,
    'Directory starts with the configured core hierarchy and separates competence circles');
$people = CircleDirectory::people(Circle::findOne(1));
check(count($people) === 2 && $people[0]['label'] === 'Kreisleitung', 'Map includes the active circle roles');
Space::$members[1][] = 3;
$extraRole = new Role(['space_id' => 1, 'user_id' => 2, 'role_key' => 'facilitator']);
$extraRole->save(false);
$people = CircleDirectory::people(Circle::findOne(1));
check(count($people) === 3 && $people[2]['label'] === 'Kreismitglied', 'Map includes members without roles');
check(str_contains($people[1]['label'], 'Moderation') && str_contains($people[1]['label'], 'Delegierte'), 'Multiple roles share one member image');
Yii::$app->db->createCommand()->update('{{%user}}', ['status' => 0], ['id' => 3])->execute();
check(count(CircleDirectory::people(Circle::findOne(1))) === 2, 'Inactive members are excluded');
Yii::$app->db->createCommand()->update('{{%user}}', ['status' => 1], ['id' => 3])->execute();
Space::$members[1] = [1, 2];
$extraRole->delete();
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
check(!Access::read($space) && CircleDirectory::people(Circle::findOne(1)) === [], 'Archived circle and members are hidden');
Space::$archived = []; Space::$disabled = [1];
check(!Access::read($space), 'Disabled module hides circle data');
Space::$disabled = []; Space::$blocked = [1];
check(!Access::read($space), 'Blocked viewer cannot access circle');
Space::$blocked = [];
Yii::$app->db->createCommand()->update('{{%sg_config}}', ['root_space_id' => 1], ['id' => 1])->execute();
$vCardRoles = VCardData::roles(\humhub\modules\user\models\User::findOne(1));
check($vCardRoles === 'Kern:Kreisleitung', 'VCard role value is ordered from the configured core circle');
check(VCardData::purpose($space) === 'Neuer Stand', 'VCard purpose value respects circle visibility');
check(VCardData::mandate($space) === 'Gesamtmandat', 'VCard mandate value uses the short mandate');
$cardClass = \humhub\modules\sociocraticGovernance\widgets\VCardGovernance::class;
$cardClass::$renderedDescription = 'Kern:Kreisleitung';
check((new $cardClass(['container' => \humhub\modules\user\models\User::findOne(1)]))->run() === '', 'User role data already rendered by template is not duplicated');
$cardClass::$renderedDescription = 'Neuer Stand';
$card = (new $cardClass(['container' => $space]))->run();
check(!str_contains($card, '<strong>Zweck') && str_contains($card, '<strong>Mandat'), 'Only the already-rendered space field is suppressed');
$cardClass::$renderedDescription = 'Neuer Stand Gesamtmandat';
check((new $cardClass(['container' => $space]))->run() === '', 'Fully rendered space data adds no duplicate addon or separator');
$cardClass::$renderedDescription = null;
check(str_contains((new $cardClass(['container' => $space]))->run(), '<strong>Zweck'), 'Cards without template fields retain the addon fallback');

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
class RequiredModuleDouble extends \yii\base\Module {
    public $version = '3.0.0';
    public $enabled = true;
    public function getVersion() { return $this->version; }
    public function getIsEnabled() { return $this->enabled; }
}
$required = \humhub\modules\sociocraticGovernance\services\RequiredModules::class;
check(count($required::enable($space)) === 3, 'Missing required modules are reported');
foreach ($required::MODULES as $id => [$label, $version]) {
    Yii::$app->setModule($id, new RequiredModuleDouble($id, Yii::$app, ['version' => $version]));
}
Space::$activationFailures = ['wiki'];
check(count($required::enable($space)) === 1, 'Failed module activation is reported');
Space::$activationFailures = [];
check($required::enable($space) === [] && count(Space::$requiredEnabled[1]) === 3, 'Available required modules can be activated after a failure');
check($required::enable($space) === [] && count(Space::$requiredEnabled[1]) === 3, 'Repeated setup does not reactivate modules');
Yii::$app->getModule('wiki')->version = '1.0.0';
check(count($required::enable($space)) === 1, 'Outdated required module is reported even when enabled');
Yii::$app->getModule('wiki')->version = '2.5.12';
Yii::$app->getModule('wiki')->enabled = false;
check(count($required::enable($space)) === 1, 'Globally disabled required module is reported');
echo "$count checks passed.\n";
