<?php
// SPDX-License-Identifier: AGPL-3.0-only

use yii\db\Migration;

/** Repairs system-generated work links that were saved as literal HTML in Markdown posts. */
final class m260909_201000_fix_work_stream_links extends Migration
{
    public function safeUp(): void
    {
        $prefixes = [
            '💡 **Neuer Vorschlag:** ',
            '🗂️ **Neue Aufgabe:** ',
            '💬 **Gemeinsame Diskussion:** ',
            '🎉 **Ressourcen gedeckt!** Für ',
        ];

        foreach ($prefixes as $prefix) {
            $posts = $this->db->createCommand(
                'SELECT [[id]], [[message]] FROM {{%post}} WHERE [[message]] LIKE :pattern',
                [':pattern' => $prefix . '<a href="%']
            )->queryAll();

            foreach ($posts as $post) {
                if (!preg_match('/^(.*)<a href="([^"]+)">(.*?)<\\/a>(.*)$/us', (string) $post['message'], $match)) {
                    continue;
                }

                $url = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $title = html_entity_decode($match[3], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $title = str_replace(['\\', '[', ']'], ['\\\\', '\\[', '\\]'], $title);
                $url = str_replace(['(', ')'], ['%28', '%29'], $url);
                $message = $match[1] . '[' . $title . '](' . $url . ')' . $match[4];
                $this->update('{{%post}}', ['message' => $message], ['id' => $post['id']]);
            }
        }
    }

    public function safeDown(): bool
    {
        return false;
    }
}
