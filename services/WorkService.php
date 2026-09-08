<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\sociocraticGovernance\models\{Circle, Configuration, WorkItem, WorkEvent, WorkResource, WorkResourceContribution, WorkTopic};
use humhub\modules\post\models\Post;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\helpers\Json;
use yii\web\{ForbiddenHttpException, NotFoundHttpException, ConflictHttpException};

final class WorkService
{
    public function create(Space $space, string $title, string $description, string $kind = 'idea', string $topics = ''): WorkItem
    {
        if (!WorkAccess::activeUser() || !Access::read($space) || !Circle::findOne($space->id)
            || ($kind === 'task' && !Access::write($space))) { throw new ForbiddenHttpException(); }
        $item = new WorkItem(['space_id' => $space->id, 'author_id' => Yii::$app->user->id,
            'title' => $title, 'description' => $description, 'kind' => $kind,
            'status' => $kind === 'idea' ? 'idea' : 'open', 'revision' => 0,
            'created_at' => time(), 'updated_at' => time()]);
        $this->validate($item);
        $tx = Yii::$app->db->beginTransaction();
        try {
            if (!$item->save(false)) { throw new \RuntimeException('Vorhaben konnte nicht gespeichert werden.'); }
            $this->syncTopics($item, $topics);
            $this->record($item, 'create', '', [], (int) $space->id);
            $tx->commit();
            WorkNotifier::send($item, $kind === 'idea' ? 'created_idea' : 'created_task');
            return $item;
        } catch (\Throwable $e) { $tx->rollBack(); throw $e; }
    }

    public function change(int $id, int $revision, string $action, array $input = []): WorkItem
    {
        $tx = Yii::$app->db->beginTransaction();
        try {
            // Lock before loading the authoritative state, then also compare the submitted revision.
            $sql = 'SELECT [[id]] FROM {{%sg_work_item}} WHERE [[id]]=:id';
            if (Yii::$app->db->driverName !== 'sqlite') { $sql .= ' FOR UPDATE'; }
            Yii::$app->db->createCommand($sql, [':id' => $id])->queryScalar();
            $item = WorkItem::findOne($id);
            if (!$item || !WorkAccess::read($item)) { throw new NotFoundHttpException(); }
            if ((int) $item->revision !== $revision) {
                throw new ConflictHttpException('Das Vorhaben wurde inzwischen geändert. Bitte neu laden.');
            }
            if ($item->archived_at) { throw new \DomainException('Dieses Vorhaben ist archiviert und kann nicht mehr geändert werden.'); }
            $before = $item->getAttributes();
            $space = $item->space;
            $announceResourcesCovered = false;
            $actor = (int) Yii::$app->user->id;
            $member = Access::write($space);
            $reviewer = WorkAccess::reviewer($space);
            $note = $this->text($input['note'] ?? '', 20000);
            if (in_array($action, ['comment', 'accept', 'submit', 'return', 'reject', 'delegate'], true) && $note === '') {
                throw new \DomainException('Bitte Kommentar, Ergebnis oder Mandatsbegründung angeben.');
            }
            switch ($action) {
                case 'comment':
                    $this->permit($item->kind === 'idea' || $member || $actor === (int) $item->author_id);
                    break;
                case 'edit':
                    $this->permit(($item->kind === 'idea' && $actor === (int) $item->author_id) || $member);
                    $this->state($item, ['idea', 'open', 'working']);
                    $item->title = $this->text($input['title'] ?? '', 255);
                    $item->description = $this->text($input['description'] ?? '', 20000);
                    break;
                case 'resource':
                    $this->permit($member && $item->kind === 'task' && $item->status !== 'rejected');
                    $note = $this->saveResource($item, $input);
                    break;
                case 'contribute':
                    $this->permit($item->kind === 'task' && !in_array($item->status, ['done', 'rejected'], true));
                    $note = $this->saveContribution($item, $actor, $input);
                    break;
                case 'archive':
                    $this->permit($member && $item->kind === 'task');
                    $this->state($item, ['done', 'rejected']);
                    $item->archived_at = time();
                    $item->archived_by = $actor;
                    $note = 'Manuell archiviert.';
                    break;
                case 'accept':
                    $this->permit($reviewer);
                    $this->state($item, ['idea']);
                    $item->kind = 'task';
                    $item->status = 'open';
                    break;
                case 'claim':
                    $this->permit($member);
                    $this->state($item, ['open', 'working']);
                    if ($item->assignee_id) { throw new \DomainException('Diese Aufgabe ist bereits übernommen.'); }
                    $item->assignee_id = $actor;
                    break;
                case 'start':
                    $this->permit($member && (int) $item->assignee_id === $actor);
                    $this->state($item, ['open']);
                    if (!$this->resourcesCovered($item)) {
                        throw new \DomainException($this->resourceCoverageMessage($item));
                    }
                    $item->status = 'working';
                    break;
                case 'submit':
                    $this->permit($member && (int) $item->assignee_id === $actor);
                    $this->state($item, ['working']);
                    $item->status = 'review';
                    $item->leader_approval_id = $item->delegate_approval_id = null;
                    break;
                case 'approve':
                    $this->permit($reviewer);
                    $this->state($item, ['review']);
                    $people = WorkAccess::reviewers($space);
                    if (count($people) !== 2 || $people['leader'] === $people['delegate']) {
                        throw new \DomainException('Zur Abnahme müssen Kreisleitung und Delegiertenrolle mit zwei aktiven Mitgliedern besetzt sein.');
                    }
                    foreach (['leader', 'delegate'] as $role) {
                        $field = $role . '_approval_id';
                        if ((int) $item->$field !== $people[$role]) { $item->$field = null; }
                        if ($actor === $people[$role]) {
                            if ($item->$field) { throw new \DomainException('Deine Abnahme ist bereits bestätigt.'); }
                            $item->$field = $actor;
                        }
                    }
                    if ($item->leader_approval_id && $item->delegate_approval_id) { $item->status = 'done'; }
                    break;
                case 'return':
                    $this->permit($reviewer);
                    $this->state($item, ['review']);
                    $item->status = 'working';
                    $item->leader_approval_id = $item->delegate_approval_id = null;
                    break;
                case 'reject':
                    if ($item->kind === 'idea') {
                        $this->permit($reviewer && !Circle::findOne($space->id)->parent_space_id);
                        $this->state($item, ['idea']);
                        // Ideas are assessed for mandate, not popularity or perceived usefulness.
                        $note = 'Außerhalb des Gesamtmandats – nicht bearbeitbar: ' . $note;
                    } else {
                        $this->permit($member);
                        $this->state($item, ['open', 'working']);
                    }
                    $item->status = 'rejected';
                    break;
                case 'delegate':
                    $this->permit($member);
                    $this->state($item, ['idea', 'open', 'working']);
                    $target = filter_var($input['target_space_id'] ?? null, FILTER_VALIDATE_INT);
                    if (!$target || !isset(WorkAccess::neighbours($space)[$target])) {
                        throw new \DomainException('Nur der direkte Oberkreis oder ein direkter Unterkreis ist zulässig.');
                    }
                    if ($item->kind === 'idea') {
                        $this->permit($reviewer && (int) Circle::findOne($space->id)->parent_space_id === $target);
                    }
                    $item->space_id = $target;
                    $item->assignee_id = null;
                    $item->leader_approval_id = $item->delegate_approval_id = null;
                    $item->status = $item->kind === 'idea' ? 'idea' : 'open';
                    break;
                default: throw new \DomainException('Unbekannte Aktion.');
            }
            $this->validate($item);
            if ($action === 'edit' && array_key_exists('topics', $input)) {
                $this->syncTopics($item, $this->text($input['topics'], 1000));
            }
            if (in_array($action, ['resource', 'contribute'], true)
                && !$item->resources_covered_at && $this->resourcesCovered($item)) {
                $item->resources_covered_at = time();
                $announceResourcesCovered = true;
            }
            $item->updated_at = time();
            $item->revision = $revision + 1;
            if (!$item->save(false)) { throw new \RuntimeException('Vorhaben konnte nicht gespeichert werden.'); }
            $this->record($item, $action, $note, $before, (int) $space->id);
            $tx->commit();
            WorkNotifier::send($item, match ($action) {
                'accept' => 'accepted', 'comment' => 'commented', 'claim' => 'claimed',
                'start' => 'started', 'submit' => 'submitted', 'approve' => 'approved',
                'return' => 'returned', 'reject' => 'rejected', 'delegate' => 'delegated',
                'edit' => 'edited', 'resource' => 'resource', 'contribute' => 'updated', 'archive' => 'updated', default => 'updated',
            }, $before);
            if ($announceResourcesCovered) {
                $this->announceResourcesCovered($item);
            }
            return $item;
        } catch (\Throwable $e) { $tx->rollBack(); throw $e; }
    }
    private function record(WorkItem $item, string $action, string $note, array $before, int $spaceId): void
    {
        $event = new WorkEvent(['work_item_id' => $item->id, 'space_id' => $spaceId,
            'actor_id' => Yii::$app->user->id, 'action' => $action, 'note' => $note,
            'before_json' => Json::encode($before), 'after_json' => Json::encode($item->getAttributes()),
            'created_at' => time()]);
        if (!$event->save(false)) { throw new \RuntimeException('Verlauf konnte nicht gespeichert werden.'); }
    }
    private function permit(bool $allowed): void { if (!$allowed) { throw new ForbiddenHttpException('Für diese Aktion fehlt die Berechtigung.'); } }
    private function state(WorkItem $item, array $states): void
    {
        if (!in_array($item->status, $states, true)) { throw new \DomainException('Diese Aktion passt nicht zum aktuellen Bearbeitungsstand.'); }
    }
    private function text($value, int $max): string
    {
        if (!is_string($value) || mb_strlen(trim($value)) > $max) { throw new \DomainException('Ungültige oder zu lange Eingabe.'); }
        return trim($value);
    }
    private function validate(WorkItem $item): void
    {
        if (!$item->validate()) { throw new \DomainException(implode(' ', $item->getFirstErrors())); }
    }
    private function syncTopics(WorkItem $item, string $input): void
    {
        $topics = [];
        foreach (preg_split('/[,;\n]+/u', $input) ?: [] as $topic) {
            $topic = trim($topic);
            if ($topic === '') { continue; }
            if (mb_strlen($topic) > 80) { throw new \DomainException('Ein Thema darf höchstens 80 Zeichen lang sein.'); }
            $key = mb_strtolower($topic);
            $topics[$key] = $topic;
        }
        if (count($topics) > 8) { throw new \DomainException('Bitte höchstens acht Themen angeben.'); }
        WorkTopic::deleteAll(['work_item_id' => $item->id]);
        foreach ($topics as $topic) {
            $model = new WorkTopic(['work_item_id' => $item->id, 'name' => $topic]);
            if (!$model->validate() || !$model->save(false)) { throw new \RuntimeException('Thema konnte nicht gespeichert werden.'); }
        }
    }
    private function saveResource(WorkItem $item, array $input): string
    {
        $resourceId = filter_var($input['resource_id'] ?? null, FILTER_VALIDATE_INT);
        $resource = $resourceId ? WorkResource::findOne(['id' => $resourceId, 'work_item_id' => $item->id]) : new WorkResource(['work_item_id' => $item->id]);
        if (!$resource) { throw new \DomainException('Die Ressource gehört nicht zu diesem Vorhaben.'); }
        $resource->resource_type = $this->text($input['resource_type'] ?? '', 16);
        $resource->label = $this->text($input['label'] ?? '', 255);
        $resource->required_amount = $this->amount($input['required_amount'] ?? null, true);
        $resource->committed_amount = $this->amount($input['available_amount'] ?? ($resource->isNewRecord ? 0 : $resource->committed_amount), false);
        $resource->unit = $this->text($input['unit'] ?? '', 32);
        $resource->details = $this->text($input['details'] ?? '', 20000);
        if ($resource->resource_type === 'time') {
            $resource->time_mode = $this->text($input['time_mode'] ?? '', 16);
            if (!isset(WorkResource::TIME_MODES[$resource->time_mode])) {
                throw new \DomainException('Bitte angeben, ob die Zeit asynchron oder termingebunden geleistet wird.');
            }
            $resource->time_pattern = $this->text($input['time_pattern'] ?? '', 255);
            if ($resource->time_pattern === '') {
                throw new \DomainException('Bitte den zeitlichen Rahmen oder Termin angeben.');
            }
        } else {
            $resource->time_mode = '';
            $resource->time_pattern = '';
        }
        $resource->created_at = $resource->isNewRecord ? time() : $resource->created_at;
        $resource->updated_at = time();
        if (!$resource->validate() || !$resource->save(false)) { throw new \DomainException(implode(' ', $resource->getFirstErrors())); }
        $state = $resource->required_amount === null
            ? 'ohne Mengenangabe'
            : $resource->totalCommittedAmount . ' von ' . $resource->required_amount . ($resource->unit ? ' ' . $resource->unit : '');
        return 'Ressourcenbedarf „' . $resource->label . '“: ' . $state . '.';
    }
    private function saveContribution(WorkItem $item, int $actor, array $input): string
    {
        $resourceId = filter_var($input['resource_id'] ?? null, FILTER_VALIDATE_INT);
        $resource = $resourceId ? WorkResource::findOne(['id' => $resourceId, 'work_item_id' => $item->id]) : null;
        if (!$resource) { throw new \DomainException('Die Ressource gehört nicht zu diesem Vorhaben.'); }
        $contribution = WorkResourceContribution::findOne(['resource_id' => $resource->id, 'user_id' => $actor])
            ?? new WorkResourceContribution(['resource_id' => $resource->id, 'user_id' => $actor]);
        $contribution->amount = $this->amount($input['amount'] ?? null, false);
        $contribution->created_at = $contribution->isNewRecord ? time() : $contribution->created_at;
        $contribution->updated_at = time();
        if (!$contribution->validate() || !$contribution->save(false)) { throw new \DomainException(implode(' ', $contribution->getFirstErrors())); }
        return 'Eine Ressourcenzusage wurde angepasst.';
    }
    private function resourcesCovered(WorkItem $item): bool
    {
        foreach ($item->resources as $resource) {
            if ($resource->required_amount === null
                || $resource->totalCommittedAmount + 0.00001 < (float) $resource->required_amount) {
                return false;
            }
        }
        return true;
    }
    private function resourceCoverageMessage(WorkItem $item): string
    {
        $missing = [];
        foreach ($item->resources as $resource) {
            if ($resource->required_amount === null) {
                $missing[] = '„' . $resource->label . '“ (Menge noch offen)';
            } elseif ($resource->totalCommittedAmount + 0.00001 < (float) $resource->required_amount) {
                $unit = $resource->unit ? ' ' . $resource->unit : '';
                $missing[] = '„' . $resource->label . '“ (' . $resource->totalCommittedAmount . ' von '
                    . $resource->required_amount . $unit . ')';
            }
        }
        return 'Die Aufgabe kann erst in Bearbeitung gehen, wenn alle Ressourcen gedeckt sind: '
            . implode(', ', $missing) . '.';
    }
    private function announceResourcesCovered(WorkItem $item): void
    {
        $companyAccount = $this->companyAccount();
        try {
            $post = new Post($item->space);
            if ($companyAccount) {
                $post->content->created_by = $companyAccount->id;
                $post->content->updated_by = $companyAccount->id;
            }
            $post->message = '🎉 **Ressourcen gedeckt!** Für „' . $item->title
                . '“ sind alle vereinbarten Ressourcen vorhanden. Die Aufgabe kann jetzt starten.';
            if (!$post->save()) {
                Yii::error(['message' => 'Feiermeldung konnte nicht gespeichert werden.', 'errors' => $post->getFirstErrors()], __METHOD__);
            }
        } catch (\Throwable $error) {
            // A non-essential stream message must never undo a confirmed resource commitment.
            Yii::error($error, __METHOD__);
        }
    }
    private function companyAccount(): ?User
    {
        $config = Configuration::findOne(1);
        if (!$config || !$config->company_user_id) { return null; }
        $account = User::findOne((int) $config->company_user_id);
        return $account && (int) $account->status === User::STATUS_ENABLED ? $account : null;
    }
    private function amount($value, bool $allowEmpty): ?float
    {
        if ($value === '' || $value === null) {
            if ($allowEmpty) { return null; }
            return 0.0;
        }
        if (!is_scalar($value)) { throw new \DomainException('Ungültige Ressourcenmenge.'); }
        $normalized = str_replace(',', '.', trim((string) $value));
        if ($normalized === '' || !is_numeric($normalized) || (float) $normalized < 0) {
            throw new \DomainException('Ressourcenmengen müssen nichtnegative Zahlen sein.');
        }
        return round((float) $normalized, 2);
    }
}
