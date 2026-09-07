<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260907_220000_work_archiving extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_config}}', 'work_auto_archive_days', $this->integer()->notNull()->defaultValue(0));
        $this->addColumn('{{%sg_work_item}}', 'archived_at', $this->integer()->null());
        $this->addColumn('{{%sg_work_item}}', 'archived_by', $this->integer()->null());
        $this->addForeignKey('fk_sg_work_archived_by', '{{%sg_work_item}}', 'archived_by', '{{%user}}', 'id', 'SET NULL');
        $this->createIndex('idx_sg_work_archive', '{{%sg_work_item}}', ['archived_at', 'status', 'updated_at']);
    }

    public function safeDown()
    {
        echo "Archived work records remain preserved. Use a coordinated backup for rollback.\n";
        return false;
    }
}
