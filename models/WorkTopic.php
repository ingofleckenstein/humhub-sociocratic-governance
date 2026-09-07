<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;

/** A searchable participation topic attached to a work item. */
class WorkTopic extends \yii\db\ActiveRecord
{
    public static function tableName() { return '{{%sg_work_topic}}'; }
    public function rules()
    {
        return [
            [['work_item_id', 'name'], 'required'],
            ['work_item_id', 'integer', 'min' => 1],
            ['name', 'string', 'max' => 80],
        ];
    }
}
