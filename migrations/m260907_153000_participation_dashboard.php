<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260907_153000_participation_dashboard extends Migration
{
    public function safeUp()
    {
        $longText = $this->db->driverName === 'mysql' ? 'MEDIUMTEXT' : 'TEXT';
        $this->createTable('{{%sg_work_topic}}', [
            'id' => $this->primaryKey(),
            'work_item_id' => $this->integer()->notNull(),
            'name' => $this->string(80)->notNull(),
            'FOREIGN KEY ([[work_item_id]]) REFERENCES {{%sg_work_item}} ([[id]]) ON DELETE RESTRICT',
        ]);
        $this->createIndex('uq_sg_work_topic', '{{%sg_work_topic}}', ['work_item_id', 'name'], true);
        $this->createIndex('idx_sg_work_topic_name', '{{%sg_work_topic}}', ['name']);
        $this->createTable('{{%sg_work_resource}}', [
            'id' => $this->primaryKey(),
            'work_item_id' => $this->integer()->notNull(),
            'resource_type' => $this->string(16)->notNull(),
            'label' => $this->string(255)->notNull(),
            'required_amount' => $this->decimal(12, 2)->null(),
            'committed_amount' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'unit' => $this->string(32)->notNull()->defaultValue(''),
            'details' => $longText,
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'FOREIGN KEY ([[work_item_id]]) REFERENCES {{%sg_work_item}} ([[id]]) ON DELETE RESTRICT',
        ]);
        $this->createIndex('idx_sg_work_resource_item', '{{%sg_work_resource}}', ['work_item_id']);
        $this->createIndex('idx_sg_work_resource_type', '{{%sg_work_resource}}', ['resource_type']);
    }

    public function safeDown()
    {
        echo "The participation dashboard data remains preserved. Use a coordinated backup for rollback.\n";
        return false;
    }
}
