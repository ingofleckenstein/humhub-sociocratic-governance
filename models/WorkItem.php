<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;

class WorkItem extends \yii\db\ActiveRecord
{
    public const STATUSES = ['idea' => 'Ideen', 'open' => 'Offen', 'working' => 'In Bearbeitung',
        'review' => 'Zur Abnahme', 'done' => 'Abgenommen', 'rejected' => 'Abgelehnt'];
    public static function tableName() { return '{{%sg_work_item}}'; }
    public function rules()
    {
        return [
            [['title', 'description'], 'trim'],
            [['title', 'description', 'space_id'], 'required'],
            ['title', 'string', 'max' => 255], ['description', 'string', 'max' => 20000],
            ['kind', 'in', 'range' => ['idea', 'task']],
            ['status', 'in', 'range' => array_keys(self::STATUSES)],
            [['archived_at', 'archived_by'], 'integer', 'min' => 1],
        ];
    }
    public function getSpace() { return $this->hasOne(\humhub\modules\space\models\Space::class, ['id' => 'space_id']); }
    public function getAuthor() { return $this->hasOne(\humhub\modules\user\models\User::class, ['id' => 'author_id']); }
    public function getAssignee() { return $this->hasOne(\humhub\modules\user\models\User::class, ['id' => 'assignee_id']); }
    public function getEvents() { return $this->hasMany(WorkEvent::class, ['work_item_id' => 'id'])->orderBy(['id' => SORT_ASC]); }
    public function getTopics() { return $this->hasMany(WorkTopic::class, ['work_item_id' => 'id'])->orderBy(['name' => SORT_ASC]); }
    public function getResources() { return $this->hasMany(WorkResource::class, ['work_item_id' => 'id'])->orderBy(['id' => SORT_ASC]); }
    public function getCircle() { return $this->hasOne(Circle::class, ['space_id' => 'space_id']); }
    public function attributeLabels() { return ['title' => 'Titel', 'description' => 'Beschreibung']; }
}
