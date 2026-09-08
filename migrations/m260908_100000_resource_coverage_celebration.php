<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260908_100000_resource_coverage_celebration extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_work_item}}', 'resources_covered_at', $this->integer()->null());
    }

    public function safeDown()
    {
        echo "The record of resource coverage remains preserved. Use a coordinated backup for rollback.\n";
        return false;
    }
}
