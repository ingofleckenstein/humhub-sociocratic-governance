<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;
class Configuration extends \yii\db\ActiveRecord
{
    public static function tableName() { return '{{%sg_config}}'; }
    public function rules()
    {
        return [
            [['root_space_id', 'authority_user_id'], 'default', 'value' => null],
            [['root_space_id', 'authority_user_id'], 'integer', 'min' => 1],
            ['organisation', 'string', 'max' => 255],
            ['root_space_id', 'exist', 'targetClass' => \humhub\modules\space\models\Space::class, 'targetAttribute' => 'id'],
            ['authority_user_id', 'exist', 'targetClass' => \humhub\modules\user\models\User::class, 'targetAttribute' => 'id'],
        ];
    }
    public function attributeLabels()
    {
        return ['root_space_id' => 'Kernkreis', 'authority_user_id' => 'Person mit Admin-Sonderrolle', 'organisation' => 'Übergeordnete Trägerorganisation'];
    }
}

