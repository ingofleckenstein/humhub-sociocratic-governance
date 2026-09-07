<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260907_240000_balance_circle_colors extends Migration
{
    public function safeUp()
    {
        $colors = ['teal', 'moss', 'terracotta', 'plum', 'ochre', 'blue'];
        $ids = (new \yii\db\Query())->select('space_id')->from('{{%sg_circle}}')->where(['color' => 'teal'])->orderBy(['space_id' => SORT_ASC])->column($this->db);
        foreach ($ids as $position => $spaceId) {
            $this->update('{{%sg_circle}}', ['color' => $colors[$position % count($colors)]], ['space_id' => $spaceId]);
        }
    }

    public function safeDown()
    {
        echo "Governance data is retained. Restore a coordinated backup for a rollback.\n";
        return false;
    }
}
