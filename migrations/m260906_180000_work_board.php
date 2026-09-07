<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260906_180000_work_board extends Migration
{
    public function safeUp()
    {
        $longText = $this->db->driverName === 'mysql' ? 'MEDIUMTEXT' : 'TEXT';
        $this->createTable('{{%sg_work_item}}', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->null(),
            'assignee_id' => $this->integer()->null(),
            'title' => $this->string(255)->notNull(),
            'description' => $longText . ' NOT NULL',
            'kind' => $this->string(16)->notNull()->defaultValue('idea'),
            'status' => $this->string(24)->notNull()->defaultValue('idea'),
            'leader_approval_id' => $this->integer()->null(),
            'delegate_approval_id' => $this->integer()->null(),
            'revision' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'FOREIGN KEY ([[space_id]]) REFERENCES {{%space}} ([[id]]) ON DELETE RESTRICT',
            'FOREIGN KEY ([[author_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE SET NULL',
            'FOREIGN KEY ([[assignee_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE SET NULL',
            'FOREIGN KEY ([[leader_approval_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE SET NULL',
            'FOREIGN KEY ([[delegate_approval_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE SET NULL',
        ]);
        $this->createIndex('idx_sg_work_board', '{{%sg_work_item}}', ['space_id', 'status']);
        $this->createTable('{{%sg_work_event}}', [
            'id' => $this->primaryKey(),
            'work_item_id' => $this->integer()->notNull(),
            'space_id' => $this->integer()->notNull(),
            'actor_id' => $this->integer()->null(),
            'action' => $this->string(24)->notNull(),
            'note' => $longText,
            'before_json' => $longText . ' NOT NULL',
            'after_json' => $longText . ' NOT NULL',
            'created_at' => $this->integer()->notNull(),
            'FOREIGN KEY ([[work_item_id]]) REFERENCES {{%sg_work_item}} ([[id]]) ON DELETE RESTRICT',
            'FOREIGN KEY ([[space_id]]) REFERENCES {{%space}} ([[id]]) ON DELETE RESTRICT',
            'FOREIGN KEY ([[actor_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE SET NULL',
        ]);
        $this->createIndex('idx_sg_work_history', '{{%sg_work_event}}', ['work_item_id', 'id']);

    }
    public function safeDown()
    {
        echo "Vorhaben und Historie bleiben erhalten. Für Rollback abgestimmtes Backup verwenden.\n";
        return false;
    }
}
