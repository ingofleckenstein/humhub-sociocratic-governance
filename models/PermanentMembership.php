<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;
class PermanentMembership extends \yii\db\ActiveRecord
{
    public static function tableName() { return '{{%sg_permanent}}'; }
    public function rules()
    {
        return [
            [['space_id', 'user_id', 'reason'], 'required'],
            [['space_id', 'user_id'], 'integer', 'min' => 1],
            ['reason', 'string', 'max' => 255],
            [['space_id', 'user_id'], 'unique', 'targetAttribute' => ['space_id', 'user_id']],
        ];
    }
    public function attributeLabels() { return ['space_id' => 'Kreis', 'user_id' => 'Person', 'reason' => 'Dauerhafte Rolle / Begründung']; }
    public function getSpace() { return $this->hasOne(\humhub\modules\space\models\Space::class, ['id' => 'space_id']); }
    public function getUser() { return $this->hasOne(\humhub\modules\user\models\User::class, ['id' => 'user_id']); }
}

