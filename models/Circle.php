<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;
class Circle extends \yii\db\ActiveRecord
{
    public const COLORS = [
        'teal' => 'Seegrün',
        'moss' => 'Moosgrün',
        'terracotta' => 'Terrakotta',
        'plum' => 'Pflaume',
        'ochre' => 'Ocker',
        'blue' => 'Himmelblau',
    ];

    public static function tableName() { return '{{%sg_circle}}'; }
    public function getSpace() { return $this->hasOne(\humhub\modules\space\models\Space::class, ['id' => 'space_id']); }
    public function getRoles() { return $this->hasMany(Role::class, ['space_id' => 'space_id']); }

    public function mandateSummary(): string
    {
        return trim((string) $this->mandate_summary);
    }

    public function colorClass(): string
    {
        $color = (string) $this->color;
        return 'sg-tone-' . (array_key_exists($color, self::COLORS) ? $color : 'teal');
    }
}
