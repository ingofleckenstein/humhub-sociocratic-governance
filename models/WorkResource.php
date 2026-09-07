<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;

/** A Commons-oriented resource need and its current contribution level. */
class WorkResource extends \yii\db\ActiveRecord
{
    public const TYPES = ['money' => 'Geld', 'time' => 'Arbeitszeit', 'expertise' => 'Expertise', 'material' => 'Material', 'other' => 'Anderes'];
    public const TIME_MODES = ['async' => 'Asynchron leistbar', 'scheduled' => 'Termingebunden'];

    public static function tableName() { return '{{%sg_work_resource}}'; }
    public function rules()
    {
        return [
            [['resource_type', 'label', 'unit', 'time_mode', 'time_pattern'], 'trim'],
            [['work_item_id', 'resource_type', 'label'], 'required'],
            ['work_item_id', 'integer', 'min' => 1],
            ['resource_type', 'in', 'range' => array_keys(self::TYPES)],
            ['time_mode', 'in', 'range' => array_merge([''], array_keys(self::TIME_MODES))],
            [['label'], 'string', 'max' => 255],
            [['unit'], 'string', 'max' => 32],
            [['time_pattern'], 'string', 'max' => 255],
            [['details'], 'string', 'max' => 20000],
            [['required_amount', 'committed_amount'], 'number', 'min' => 0],
        ];
    }

    public function getWorkItem() { return $this->hasOne(WorkItem::class, ['id' => 'work_item_id']); }
    public function getContributions() { return $this->hasMany(WorkResourceContribution::class, ['resource_id' => 'id'])->orderBy(['id' => SORT_ASC]); }
    public function getPersonalCommittedAmount(): float
    {
        if ($this->isRelationPopulated('contributions')) {
            return round(array_sum(array_map(static fn($contribution) => (float) $contribution->amount, $this->contributions)), 2);
        }
        return round((float) $this->getContributions()->sum('amount'), 2);
    }
    public function getTotalCommittedAmount(): float { return round((float) $this->committed_amount + $this->personalCommittedAmount, 2); }
    public function getProgress(): ?float
    {
        if ($this->required_amount === null || (float) $this->required_amount <= 0) { return null; }
        return min(100, round(100 * $this->totalCommittedAmount / (float) $this->required_amount, 1));
    }
}
