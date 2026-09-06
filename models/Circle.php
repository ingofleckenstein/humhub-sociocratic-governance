<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;
class Circle extends \yii\db\ActiveRecord
{
    public static function tableName() { return '{{%sg_circle}}'; }
    public function getSpace() { return $this->hasOne(\humhub\modules\space\models\Space::class, ['id' => 'space_id']); }
    public function getRoles() { return $this->hasMany(Role::class, ['space_id' => 'space_id']); }

    public function mandateSummary(): string
    {
        return trim((string) $this->mandate_summary);
    }
}
