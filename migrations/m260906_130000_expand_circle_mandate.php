<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260906_130000_expand_circle_mandate extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_circle}}', 'mandate_summary', $this->string(255)->notNull()->defaultValue(''));
        $this->addColumn('{{%sg_circle}}', 'responsibility', $this->text());
        $this->addColumn('{{%sg_circle}}', 'authority', $this->text());
        $this->addColumn('{{%sg_circle}}', 'boundaries', $this->text());
        $this->addColumn('{{%sg_circle}}', 'budget', $this->text());
        $this->addColumn('{{%sg_circle}}', 'reelection_interval', $this->string(255));
        $this->addColumn('{{%sg_circle}}', 'review', $this->text());
    }

    public function safeDown()
    {
        echo "Governance data is retained. Restore a coordinated backup for a rollback.\n";
        return false;
    }
}
