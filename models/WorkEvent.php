<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;

/** Append-only through WorkService; no public edit/delete endpoint. */
class WorkEvent extends \yii\db\ActiveRecord
{
    public const LABELS = ['create' => 'Erstellt', 'edit' => 'Text geändert', 'comment' => 'Kommentar',
        'accept' => 'Idee als Aufgabe angenommen', 'claim' => 'Übernommen', 'start' => 'Bearbeitung begonnen',
        'submit' => 'Zur Abnahme vorgelegt', 'approve' => 'Abnahme bestätigt', 'return' => 'Zur Nacharbeit',
        'reject' => 'Abgelehnt', 'delegate' => 'An benachbarten Kreis übergeben',
        'resource' => 'Ressourcenbedarf geändert', 'contribute' => 'Zusage angepasst', 'archive' => 'Archiviert'];
    public static function tableName() { return '{{%sg_work_event}}'; }
    public function getActor() { return $this->hasOne(\humhub\modules\user\models\User::class, ['id' => 'actor_id']); }
    public function getSpace() { return $this->hasOne(\humhub\modules\space\models\Space::class, ['id' => 'space_id']); }
}
