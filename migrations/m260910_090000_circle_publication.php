<?php
// SPDX-License-Identifier: AGPL-3.0-only

use yii\db\Migration;

/** Adds a one-way publication state for circles that already have a profile. */
final class m260910_090000_circle_publication extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%sg_circle}}', 'is_published', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('{{%sg_circle}}', 'published_at', $this->integer()->null());
        $this->addColumn('{{%sg_circle}}', 'published_by', $this->integer()->null());
        // SQLite cannot add a foreign key after table creation. Production
        // MySQL/MariaDB receives the same protection as the existing schema.
        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey('fk_sg_circle_published_by', '{{%sg_circle}}', 'published_by', '{{%user}}', 'id', 'SET NULL');
        }

        // Existing visible circles keep their status during the update. A private
        // circle remains a draft until an administrator explicitly publishes it.
        $this->execute('UPDATE {{%sg_circle}} SET [[is_published]]=1 WHERE [[space_id]] IN '
            . '(SELECT [[id]] FROM {{%space}} WHERE [[visibility]] <> 0)');
    }

    public function safeDown(): bool
    {
        echo "Publication state is retained. Use a coordinated backup for rollback.\n";
        return false;
    }
}
