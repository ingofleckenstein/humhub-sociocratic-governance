<?php
// SPDX-License-Identifier: AGPL-3.0-only
require __DIR__ . '/bootstrap.php';
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, Configuration, WorkItem, WorkEvent, WorkResource, WorkResourceContribution, Role};
use humhub\modules\sociocraticGovernance\services\{CircleService, WorkService, WorkAccess, WorkArchiver, WorkNotificationRecipients};
use humhub\modules\space\models\Space;
use yii\web\{ForbiddenHttpException, NotFoundHttpException, ConflictHttpException};

$count = 0;
function checkWork(bool $condition, string $message): void {
    global $count;
    if (!$condition) { throw new RuntimeException($message); }
    $count++; echo "PASS: $message\n";
}
function deniedWork(callable $call, string $class, string $message): void {
    try { $call(); } catch (Throwable $e) {
        if ($e instanceof $class) { checkWork(true, $message); return; }
        throw $e;
    }
    throw new RuntimeException('Expected rejection: ' . $message);
}
$circles = new CircleService();
$circles->save(Space::findOne(1), new CircleForm(['leader' => 1, 'delegate' => 2]));
$circles->save(Space::findOne(2), new CircleForm(['parent_space_id' => 1, 'leader' => 1, 'delegate' => 2]));
Yii::$app->user->id = 2;
$circles->save(Space::findOne(3), new CircleForm(['parent_space_id' => 1, 'leader' => 2]));
$service = new WorkService();
Yii::$app->user->id = 3;
$item = $service->create(Space::findOne(1), 'Idee aus der Community', 'Ein konkreter Vorschlag.', 'idea', 'Bildung, Commons, Bildung');
checkWork($item->status === 'idea' && (int) $item->author_id === 3, 'Nonmember can submit an idea to a readable circle');
checkWork(count($item->topics) === 2, 'Topics are normalized and tracked with an idea');
checkWork(count($item->events) === 1, 'Creation records original text, actor and circle');
checkWork(WorkNotificationRecipients::ids($item, 'created_idea') === [1, 2], 'New ideas target both active linking roles');
checkWork(WorkNotificationRecipients::ids($item, 'commented') === [3, 1, 2], 'Comments target author and both linking roles without broad space alerts');
deniedWork(fn() => $service->create(Space::findOne(1), 'Aufgabe', 'Text', 'task'), ForbiddenHttpException::class, 'Nonmember cannot create a task directly');
deniedWork(fn() => $service->create(Space::findOne(3), 'Geheim', 'Text'), ForbiddenHttpException::class, 'Private circle cannot be addressed without access');
deniedWork(fn() => $service->change($item->id, 0, 'accept', ['note' => 'Mandat']), ForbiddenHttpException::class, 'Nonmember cannot accept an idea');
$item = $service->change($item->id, 0, 'comment', ['note' => 'Mein Kommentar']);
checkWork($item->revision === 1 && WorkEvent::find()->where(['work_item_id' => $item->id])->count() == 2, 'Comment is recorded in the same history');
deniedWork(fn() => $service->change($item->id, 0, 'comment', ['note' => 'Veraltet']), ConflictHttpException::class, 'Stale requests cannot overwrite comments or state');
Space::$members[1][] = 3;
deniedWork(fn() => $service->change($item->id, 1, 'accept', ['note' => 'Mandat']), ForbiddenHttpException::class, 'Membership alone cannot approve an idea');
Yii::$app->user->admin = true;
deniedWork(fn() => $service->change($item->id, 1, 'accept', ['note' => 'Mandat']), ForbiddenHttpException::class, 'Technical admin gets no idea approval authority');
Yii::$app->user->admin = false;
Yii::$app->user->id = 1;
deniedWork(fn() => $service->change($item->id, 1, 'accept'), DomainException::class, 'Acceptance requires a documented mandate reference');
$item = $service->change($item->id, 1, 'accept', ['note' => 'Innerhalb unseres Bildungsmandats.']);
checkWork($item->kind === 'task' && $item->status === 'open' && (int) $item->author_id === 3, 'Acceptance retains identity and author and creates an open task');
deniedWork(fn() => $service->change($item->id, 1, 'accept', ['note' => 'Nochmals']), ConflictHttpException::class, 'Repeated acceptance cannot duplicate a task');
Yii::$app->user->id = 3;
$item = $service->change($item->id, 2, 'claim');
checkWork((int) $item->assignee_id === 3, 'Circle member can assign an unclaimed task to themselves');
Yii::$app->user->id = 2;
deniedWork(fn() => $service->change($item->id, 3, 'claim'), DomainException::class, 'Another member cannot steal a claimed task');
deniedWork(fn() => $service->change($item->id, 3, 'submit', ['note' => 'Fertig']), ForbiddenHttpException::class, 'Another member cannot submit someone else’s work');
Yii::$app->user->id = 3;
$item = $service->change($item->id, 3, 'start');
$item = $service->change($item->id, 4, 'submit', ['note' => 'Ergebnis erstellt und geprüft.']);
checkWork($item->status === 'review', 'Finished work goes to review, not directly to done');
deniedWork(fn() => $service->change($item->id, 5, 'approve'), ForbiddenHttpException::class, 'Assignee without a reviewer role cannot approve');
Yii::$app->user->id = 1;
$item = $service->change($item->id, 5, 'approve');
checkWork($item->status === 'review', 'First approval does not close the task');
deniedWork(fn() => $service->change($item->id, 6, 'approve'), DomainException::class, 'Same reviewer cannot approve twice');
Yii::$app->user->id = 2;
$item = $service->change($item->id, 6, 'return', ['note' => 'Bitte Ergebnis nachbessern.']);
checkWork($item->status === 'working' && !$item->leader_approval_id, 'Return clears approvals for a new result');
Yii::$app->user->id = 3;
$item = $service->change($item->id, 7, 'submit', ['note' => 'Verbessertes Ergebnis.']);
Yii::$app->user->id = 1;
$item = $service->change($item->id, 8, 'approve');
Yii::$app->user->id = 2;
$item = $service->change($item->id, 9, 'approve');
checkWork($item->status === 'done', 'Both independent reviewer confirmations close the task');
deniedWork(fn() => $service->change($item->id, 10, 'edit', ['title' => 'Anders', 'description' => 'Neu']), DomainException::class, 'Accepted result cannot be silently edited');

$task = $service->create(Space::findOne(2), 'Delegation', 'Arbeitsauftrag', 'task');
$task = $service->change($task->id, 0, 'resource', ['resource_type' => 'time', 'label' => 'Moderation', 'required_amount' => '12', 'available_amount' => '0', 'unit' => 'Stunden', 'time_mode' => 'async', 'time_pattern' => 'Vorbereitung und Nachbereitung im eigenen Rhythmus', 'details' => 'Für die erste Runde']);
checkWork(WorkResource::find()->where(['work_item_id' => $task->id])->count() === 1 && $task->resources[0]->progress === 0.0, 'Circle members estimate a differentiated time resource without faking a pledge');
Yii::$app->user->id = 3;
$task = $service->change($task->id, 1, 'contribute', ['resource_id' => $task->resources[0]->id, 'amount' => '3.5']);
checkWork(WorkResourceContribution::find()->where(['resource_id' => $task->resources[0]->id, 'user_id' => 3])->count() === 1 && $task->resources[0]->progress === 29.2, 'Readable task accepts a personal contribution and exposes only the aggregate');
deniedWork(fn() => $service->change($task->id, 2, 'resource', ['resource_type' => 'time']), ForbiddenHttpException::class, 'Nonmember cannot estimate or alter a circle resource need');
Yii::$app->user->id = 2;
deniedWork(fn() => $service->change($task->id, 2, 'delegate', ['target_space_id' => 3, 'note' => 'Quer']), DomainException::class, 'Task cannot be delegated to a sibling');
$task = $service->change($task->id, 2, 'delegate', ['target_space_id' => 1, 'note' => 'Mandat im Oberkreis']);
checkWork((int) $task->space_id === 1 && $task->status === 'open' && !$task->assignee_id, 'Task can move to direct parent and loses previous assignment');
$task = $service->change($task->id, 3, 'delegate', ['target_space_id' => 3, 'note' => 'Auftrag an direkten Unterkreis']);
checkWork((int) $task->space_id === 3, 'Task can move to direct child');
Yii::$app->user->id = 1;
checkWork(!WorkAccess::read(WorkItem::findOne($task->id)), 'Transfer to private circle removes access for nonmembers');
Yii::$app->user->id = 2;
$task = $service->change($task->id, 4, 'delegate', ['target_space_id' => 1, 'note' => 'Zurück an Oberkreis']);
Yii::$app->user->id = 1;
checkWork(!WorkAccess::read(WorkItem::findOne($task->id)), 'Private history is not disclosed by transfer into a public circle');
deniedWork(fn() => $service->change($task->id, 5, 'claim'), NotFoundHttpException::class, 'Direct mutation cannot bypass private history access');

$idea = $service->create(Space::findOne(2), 'Mandatsprüfung', 'Idee');
deniedWork(fn() => $service->change($idea->id, 0, 'reject', ['note' => 'Mag ich nicht']), ForbiddenHttpException::class, 'Child circle cannot reject an idea instead of checking mandate');
$idea = $service->change($idea->id, 0, 'delegate', ['target_space_id' => 1, 'note' => 'Außerhalb des Unterkreismandats']);
checkWork($idea->kind === 'idea' && $idea->status === 'idea', 'Escalated idea awaits mandate check in parent');
$idea = $service->change($idea->id, 1, 'reject', ['note' => 'Liegt außerhalb des Gesamtzwecks']);
checkWork($idea->status === 'rejected' && str_contains(WorkEvent::find()->where(['work_item_id' => $idea->id])->orderBy(['id' => SORT_DESC])->one()->note, 'Gesamtmandats'), 'Top circle documents out-of-total-mandate closure');


$reviewTask = $service->create(Space::findOne(1), 'Rollenwechsel', 'Prüfauftrag', 'task');
$reviewTask = $service->change($reviewTask->id, 0, 'claim');
$reviewTask = $service->change($reviewTask->id, 1, 'start');
$reviewTask = $service->change($reviewTask->id, 2, 'submit', ['note' => 'Ergebnis']);
$reviewTask = $service->change($reviewTask->id, 3, 'approve');
Yii::$app->db->createCommand()->update('{{%sg_role}}', ['user_id' => 3], ['space_id' => 1, 'role_key' => 'leader'])->execute();
Yii::$app->user->id = 2;
$reviewTask = $service->change($reviewTask->id, 4, 'approve');
checkWork($reviewTask->status === 'review' && !$reviewTask->leader_approval_id, 'Role changes invalidate the former role holder’s pending approval');
Yii::$app->user->id = 3;
$reviewTask = $service->change($reviewTask->id, 5, 'approve');
checkWork($reviewTask->status === 'done', 'New role holder can complete the current review');
Yii::$app->db->createCommand()->update('{{%sg_role}}', ['user_id' => 1], ['space_id' => 1, 'role_key' => 'leader'])->execute();
Yii::$app->user->id = 1;
$reviewTask = $service->change($reviewTask->id, 6, 'archive');
checkWork((int) $reviewTask->archived_by === 1 && $reviewTask->archived_at && $reviewTask->status === 'done', 'Circle member archives a completed task without deleting it');
deniedWork(fn() => $service->change($reviewTask->id, 7, 'comment', ['note' => 'Nicht mehr ändern']), DomainException::class, 'Archived tasks reject all further user mutations');
checkWork(!str_contains((string) file_get_contents(dirname(__DIR__) . '/controllers/WorkController.php'), 'actionDelete'), 'Work controller has no task deletion endpoint');
$autoTask = $service->create(Space::findOne(1), 'Automatisch archivieren', 'Abgelehnte Aufgabe', 'task');
$autoTask = $service->change($autoTask->id, 0, 'reject', ['note' => 'Nicht weiterverfolgen']);
Yii::$app->db->createCommand()->update('{{%sg_work_item}}', ['updated_at' => time() - 172800], ['id' => $autoTask->id])->execute();
$config = Configuration::findOne(1); $config->work_auto_archive_days = 1; $config->save(false);
checkWork((new WorkArchiver())->archiveDue() === 1 && WorkItem::findOne($autoTask->id)->archived_at, 'Daily archiver preserves and archives due rejected tasks after the configured delay');
$atomic = $service->create(Space::findOne(1), 'Atomarer Verlauf', 'Text');
Yii::$app->db->getMasterPdo()->exec(Yii::$app->db->quoteSql("CREATE TRIGGER reject_work_audit BEFORE INSERT ON {{%sg_work_event}} BEGIN SELECT RAISE(ABORT, 'test audit failure'); END"));
deniedWork(fn() => $service->change($atomic->id, 0, 'edit', ['title' => 'Darf nicht bleiben', 'description' => 'Geändert']), \yii\db\Exception::class, 'Audit insertion failure aborts the mutation');
checkWork(WorkItem::findOne($atomic->id)->title === 'Atomarer Verlauf' && (int) WorkItem::findOne($atomic->id)->revision === 0, 'Work text and revision roll back together with audit failure');
Yii::$app->db->createCommand('DROP TRIGGER reject_work_audit')->execute();
deniedWork(fn() => $service->change($atomic->id, 0, 'comment', ['note' => ['invalid']]), DomainException::class, 'Array input is rejected server-side');
$beforeCount = WorkItem::find()->count();
deniedWork(fn() => $service->create(Space::findOne(1), str_repeat('x', 256), 'Text'), DomainException::class, 'Title is validated server-side');
checkWork(WorkItem::find()->count() == $beforeCount, 'Failed validation leaves no orphan item');
Yii::$app->user->isGuest = true;
deniedWork(fn() => $service->create(Space::findOne(1), 'Gast', 'Text'), ForbiddenHttpException::class, 'Guests cannot submit ideas');
Yii::$app->user->isGuest = false;
Space::$archived = [1];
checkWork(!WorkAccess::read(WorkItem::findOne($item->id)), 'Archive also hides tasks and audit history');
Space::$archived = [];
Yii::$app->db->createCommand()->update('{{%user}}', ['status' => 0], ['id' => 1])->execute();
deniedWork(fn() => $service->create(Space::findOne(1), 'Inaktiv', 'Text'), ForbiddenHttpException::class, 'Disabled users cannot create work');
checkWork(WorkEvent::find()->where(['work_item_id' => $item->id])->count() == 11, 'Complete lifecycle retains each successful change exactly once');
echo "$count work checks passed.\n";
