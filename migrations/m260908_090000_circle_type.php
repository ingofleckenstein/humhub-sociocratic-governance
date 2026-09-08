<?php
// SPDX-License-Identifier: AGPL-3.0-only

use yii\db\Migration;

class m260908_090000_circle_type extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_circle}}', 'type', $this->string(20)->notNull()->defaultValue('project'));
        $this->createIndex('idx_sg_circle_type', '{{%sg_circle}}', 'type');
    }

    public function safeDown()
    {
        echo "Governance data is retained. Restore a coordinated backup for a rollback.\n";
        return false;
    }
}
