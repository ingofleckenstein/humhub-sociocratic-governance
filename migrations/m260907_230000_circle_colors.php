<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260907_230000_circle_colors extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_circle}}', 'color', $this->string(24)->notNull()->defaultValue('teal'));
    }

    public function safeDown()
    {
        echo "Governance data is retained. Restore a coordinated backup for a rollback.\n";
        return false;
    }
}
