<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260907_210000_personal_resource_contributions extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_work_resource}}', 'time_mode', $this->string(16)->notNull()->defaultValue(''));
        $this->addColumn('{{%sg_work_resource}}', 'time_pattern', $this->string(255)->notNull()->defaultValue(''));
        $this->createTable('{{%sg_work_resource_contribution}}', [
            'id' => $this->primaryKey(),
            'resource_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(12, 2)->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'FOREIGN KEY ([[resource_id]]) REFERENCES {{%sg_work_resource}} ([[id]]) ON DELETE RESTRICT',
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE RESTRICT',
        ]);
        $this->createIndex('uq_sg_resource_contribution_user', '{{%sg_work_resource_contribution}}', ['resource_id', 'user_id'], true);
        $this->createIndex('idx_sg_resource_contribution_user', '{{%sg_work_resource_contribution}}', ['user_id']);
    }

    public function safeDown()
    {
        echo "Personal resource contributions remain preserved. Use a coordinated backup for rollback.\n";
        return false;
    }
}
