<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\models;

/** An immutable, readable snapshot created for every submitted idea version. */
class WorkProposalRevision extends \yii\db\ActiveRecord
{
    public static function tableName() { return '{{%sg_work_proposal_revision}}'; }
    public function getWorkItem() { return $this->hasOne(WorkItem::class, ['id' => 'work_item_id']); }
    public function getAuthor() { return $this->hasOne(\humhub\modules\user\models\User::class, ['id' => 'author_id']); }
}
