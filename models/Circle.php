<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;
class Circle extends \yii\db\ActiveRecord
{
    public const TYPES = [
        'project' => 'Projektkreis',
        'competence' => 'Kompetenzkreis',
    ];
    public const COLORS = [
        'teal' => 'Seegrün',
        'moss' => 'Moosgrün',
        'terracotta' => 'Terrakotta',
        'plum' => 'Pflaume',
        'ochre' => 'Ocker',
        'blue' => 'Himmelblau',
        'coral' => 'Koralle',
        'indigo' => 'Indigo',
        'raspberry' => 'Himbeere',
        'jade' => 'Jade',
        'amber' => 'Bernstein',
        'slate' => 'Schieferblau',
        'spruce' => 'Fichtengrün',
    ];

    public static function tableName() { return '{{%sg_circle}}'; }
    public function getSpace() { return $this->hasOne(\humhub\modules\space\models\Space::class, ['id' => 'space_id']); }
    public function getRoles() { return $this->hasMany(Role::class, ['space_id' => 'space_id']); }
    public function getPermanentMemberships() { return $this->hasMany(PermanentMembership::class, ['space_id' => 'space_id']); }

    public function mandateSummary(): string
    {
        return trim((string) $this->mandate_summary);
    }

    public function colorClass(): string
    {
        $color = (string) $this->color;
        return 'sg-tone-' . (array_key_exists($color, self::COLORS) ? $color : 'teal');
    }

    public static function suggestedColor(?int $exceptSpaceId = null): string
    {
        $query = self::find()->select('color');
        if ($exceptSpaceId !== null) {
            $query->andWhere(['<>', 'space_id', $exceptSpaceId]);
        }
        $used = array_flip(array_filter($query->column(), static fn($color): bool => array_key_exists((string) $color, self::COLORS)));
        foreach (array_keys(self::COLORS) as $color) {
            if (!isset($used[$color])) {
                return $color;
            }
        }
        // A full palette must not prevent creating another circle. Reuse a
        // color deterministically only after every available tone is in use.
        $colors = array_keys(self::COLORS);
        return $colors[count($query->column()) % count($colors)];
    }

    public static function hasAvailableColor(?int $exceptSpaceId = null): bool
    {
        $query = self::find()->select('color');
        if ($exceptSpaceId !== null) {
            $query->andWhere(['<>', 'space_id', $exceptSpaceId]);
        }
        $used = array_flip(array_filter($query->column(), static fn($color): bool => array_key_exists((string) $color, self::COLORS)));
        return count($used) < count(self::COLORS);
    }

    public function isCompetenceCircle(): bool
    {
        return (string) $this->type === 'competence';
    }
}
