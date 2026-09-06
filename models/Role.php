<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;
class Role extends \yii\db\ActiveRecord
{
    public const LABELS = [
        'leader' => 'Kreisleitung', 'delegate' => 'Delegierte*r',
        'facilitator' => 'Moderation', 'secretary' => 'Dokumentation',
    ];
    public static function tableName() { return '{{%sg_role}}'; }
    public function getUser() { return $this->hasOne(\humhub\modules\user\models\User::class, ['id' => 'user_id']); }
    public function getCircle() { return $this->hasOne(Circle::class, ['space_id' => 'space_id']); }
}

