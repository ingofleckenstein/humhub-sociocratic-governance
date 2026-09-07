<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\notifications;

use Yii;
use humhub\helpers\Html;
use humhub\modules\notification\components\BaseNotification;
use humhub\modules\sociocraticGovernance\models\WorkItem;
use humhub\modules\sociocraticGovernance\services\WorkAccess;
use humhub\modules\user\models\User;
use yii\helpers\Url;

final class WorkItemNotification extends BaseNotification
{
    public $moduleId = 'sociocratic-governance';
    public $eventType = 'updated';

    public function category() { return new WorkItemNotificationCategory(); }

    public function event(string $eventType): self
    {
        $this->eventType = $eventType;
        return $this;
    }

    public function getUrl()
    {
        if (!$this->source instanceof WorkItem || !$this->source->space) { return Url::to('/', true); }
        return Url::to($this->source->space->createUrl('/sociocratic-governance/work/view', ['id' => $this->source->id]), true);
    }

    public function getSpaceId()
    {
        return $this->source instanceof WorkItem ? (int) $this->source->space_id : null;
    }

    public function isBlockedForUser(User $user): bool
    {
        return !$this->source instanceof WorkItem || !WorkAccess::readForUser($this->source, $user);
    }

    public function html()
    {
        $actor = $this->originator ? Html::encode($this->originator->displayName) : Yii::t('SociocraticGovernanceModule.base', 'Das System');
        $title = $this->source instanceof WorkItem ? Html::encode($this->source->title) : '';
        return '<strong>' . $actor . '</strong> ' . Html::encode($this->eventLabel())
            . ' <strong>' . $title . '</strong>.';
    }

    public function getMailSubject() { return $this->text(); }

    public function __serialize(): array
    {
        return array_merge(parent::__serialize(), ['eventType' => $this->eventType]);
    }

    public function __unserialize($data)
    {
        parent::__unserialize($data);
        $this->eventType = $data['eventType'] ?? 'updated';
    }

    private function eventLabel(): string
    {
        return match ($this->eventType) {
            'created_idea' => Yii::t('SociocraticGovernanceModule.base', 'hat eine neue Idee eingereicht:'),
            'created_task' => Yii::t('SociocraticGovernanceModule.base', 'hat eine neue Aufgabe angelegt:'),
            'accepted' => Yii::t('SociocraticGovernanceModule.base', 'hat die Idee als Aufgabe übernommen:'),
            'commented' => Yii::t('SociocraticGovernanceModule.base', 'hat eine Rückmeldung ergänzt zu:'),
            'claimed' => Yii::t('SociocraticGovernanceModule.base', 'hat die Aufgabe übernommen:'),
            'started' => Yii::t('SociocraticGovernanceModule.base', 'hat die Bearbeitung begonnen:'),
            'submitted' => Yii::t('SociocraticGovernanceModule.base', 'hat ein Ergebnis zur Abnahme vorgelegt:'),
            'approved' => Yii::t('SociocraticGovernanceModule.base', 'hat die Abnahme bestätigt für:'),
            'returned' => Yii::t('SociocraticGovernanceModule.base', 'hat Nacharbeit erbeten für:'),
            'rejected' => Yii::t('SociocraticGovernanceModule.base', 'hat das Vorhaben abgeschlossen:'),
            'delegated' => Yii::t('SociocraticGovernanceModule.base', 'hat das Vorhaben weitergegeben:'),
            'edited' => Yii::t('SociocraticGovernanceModule.base', 'hat das Vorhaben geändert:'),
            default => Yii::t('SociocraticGovernanceModule.base', 'hat das Vorhaben aktualisiert:'),
        };
    }
}
