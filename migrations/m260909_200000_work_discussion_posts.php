<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260909_200000_work_discussion_posts extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_work_item}}', 'stream_post_id', $this->integer()->null());
        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey('fk_sg_work_stream_post', '{{%sg_work_item}}', 'stream_post_id', '{{%post}}', 'id', 'SET NULL', 'RESTRICT');
        }
    }
    public function safeDown()
    {
        echo "Verknüpfungen zu den gemeinsamen Diskussionen bleiben erhalten. Für ein Rollback ein abgestimmtes Backup verwenden.\n";
        return false;
    }
}
