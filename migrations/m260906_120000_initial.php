<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260906_120000_initial extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%sg_circle}}', [
            'space_id' => $this->integer()->notNull(),
            'parent_space_id' => $this->integer(),
            'purpose' => $this->text()->notNull(),
            'mandate' => $this->text()->notNull(),
            'revision' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull(),
            'updated_by' => $this->integer()->notNull(),
            'PRIMARY KEY ([[space_id]])',
            'FOREIGN KEY ([[space_id]]) REFERENCES {{%space}} ([[id]]) ON DELETE CASCADE',
            'FOREIGN KEY ([[parent_space_id]]) REFERENCES {{%space}} ([[id]]) ON DELETE SET NULL',
        ]);
        $this->createTable('{{%sg_role}}', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'role_key' => $this->string(40)->notNull(),
            'FOREIGN KEY ([[space_id]]) REFERENCES {{%sg_circle}} ([[space_id]]) ON DELETE CASCADE',
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
        ]);
        $this->createIndex('uq_sg_role', '{{%sg_role}}', ['space_id', 'role_key'], true);
        $this->createTable('{{%sg_config}}', [
            'id' => $this->integer()->notNull(),
            'root_space_id' => $this->integer(),
            'authority_user_id' => $this->integer(),
            'organisation' => $this->string(255)->notNull()->defaultValue(''),
            'PRIMARY KEY ([[id]])',
            'FOREIGN KEY ([[root_space_id]]) REFERENCES {{%space}} ([[id]]) ON DELETE SET NULL',
            'FOREIGN KEY ([[authority_user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE SET NULL',
        ]);
        $this->insert('{{%sg_config}}', ['id' => 1]);
        $this->createTable('{{%sg_permanent}}', [
            'id' => $this->primaryKey(),
            'space_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'reason' => $this->string(255)->notNull(),
            'FOREIGN KEY ([[space_id]]) REFERENCES {{%space}} ([[id]]) ON DELETE CASCADE',
            'FOREIGN KEY ([[user_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE CASCADE',
        ]);
        $this->createIndex('uq_sg_permanent', '{{%sg_permanent}}', ['space_id', 'user_id'], true);
    }

    public function safeDown()
    {
        echo "Governance data is retained. Restore a coordinated backup for a rollback.\n";
        return false;
    }
}
