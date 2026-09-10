<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260908_110000_company_account extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%sg_config}}', 'company_user_id', $this->integer()->null());
        if ($this->db->driverName !== 'sqlite') {
            $this->addForeignKey('fk_sg_config_company_user', '{{%sg_config}}', 'company_user_id', '{{%user}}', 'id', 'SET NULL');
        }
    }

    public function safeDown()
    {
        echo "The company account configuration remains preserved. Use a coordinated backup for rollback.\n";
        return false;
    }
}
