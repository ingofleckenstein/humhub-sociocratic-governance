<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;

/** A member's current, private-in-detail contribution to a resource need. */
class WorkResourceContribution extends \yii\db\ActiveRecord
{
    public static function tableName() { return '{{%sg_work_resource_contribution}}'; }
    public function rules()
    {
        return [
            [['resource_id', 'user_id', 'amount', 'created_at', 'updated_at'], 'required'],
            [['resource_id', 'user_id', 'created_at', 'updated_at'], 'integer', 'min' => 1],
            ['amount', 'number', 'min' => 0],
        ];
    }
    public function getResource() { return $this->hasOne(WorkResource::class, ['id' => 'resource_id']); }
    public function getUser() { return $this->hasOne(\humhub\modules\user\models\User::class, ['id' => 'user_id']); }
}
