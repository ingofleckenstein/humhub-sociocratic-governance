<?php
// SPDX-License-Identifier: AGPL-3.0-only
use yii\db\Migration;

class m260909_180000_proposal_versions extends Migration
{
    public function safeUp()
    {
        $longText = $this->db->driverName === 'mysql' ? 'MEDIUMTEXT' : 'TEXT';
        $this->addColumn('{{%sg_work_item}}', 'proposal_version', $this->integer()->notNull()->defaultValue(0));
        $this->createTable('{{%sg_work_proposal_revision}}', [
            'id' => $this->primaryKey(),
            'work_item_id' => $this->integer()->notNull(),
            'version' => $this->integer()->notNull(),
            'title' => $this->string(255)->notNull(),
            'description' => $longText . ' NOT NULL',
            'topics_json' => $longText . ' NOT NULL',
            'author_id' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
            'FOREIGN KEY ([[work_item_id]]) REFERENCES {{%sg_work_item}} ([[id]]) ON DELETE RESTRICT',
            'FOREIGN KEY ([[author_id]]) REFERENCES {{%user}} ([[id]]) ON DELETE SET NULL',
        ]);
        $this->createIndex('uq_sg_work_proposal_version', '{{%sg_work_proposal_revision}}', ['work_item_id', 'version'], true);

        // Existing ideas receive their current visible text as version 1. No
        // historic text is inferred or rewritten during an upgrade.
        foreach ($this->db->createCommand('SELECT [[id]], [[author_id]], [[title]], [[description]], [[created_at]] FROM {{%sg_work_item}} WHERE [[kind]]=:kind', [':kind' => 'idea'])->queryAll() as $idea) {
            $topics = $this->db->createCommand('SELECT [[name]] FROM {{%sg_work_topic}} WHERE [[work_item_id]]=:id ORDER BY [[name]] ASC', [':id' => $idea['id']])->queryColumn();
            $this->insert('{{%sg_work_proposal_revision}}', [
                'work_item_id' => $idea['id'], 'version' => 1, 'title' => $idea['title'],
                'description' => $idea['description'], 'topics_json' => json_encode($topics, JSON_UNESCAPED_UNICODE),
                'author_id' => $idea['author_id'], 'created_at' => $idea['created_at'],
            ]);
            $this->update('{{%sg_work_item}}', ['proposal_version' => 1], ['id' => $idea['id']]);
        }
    }
    public function safeDown()
    {
        echo "Vorschlagsversionen bleiben erhalten. Für ein Rollback ein abgestimmtes Backup verwenden.\n";
        return false;
    }
}
