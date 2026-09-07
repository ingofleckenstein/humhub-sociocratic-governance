<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use Yii;
use humhub\modules\sociocraticGovernance\models\{Configuration, WorkEvent, WorkItem};
use yii\helpers\Json;

/** Archives eligible work records during HumHub's daily cron without deleting them. */
final class WorkArchiver
{
    public function archiveDue(): int
    {
        $days = (int) (Configuration::findOne(1)?->work_auto_archive_days ?? 0);
        if ($days <= 0) { return 0; }
        $before = time() - ($days * 86400);
        $count = 0;
        foreach (WorkItem::find()->where(['status' => ['done', 'rejected'], 'archived_at' => null])->andWhere(['<=', 'updated_at', $before])->each() as $candidate) {
            $tx = Yii::$app->db->beginTransaction();
            try {
                $sql = 'SELECT [[id]] FROM {{%sg_work_item}} WHERE [[id]]=:id';
                if (Yii::$app->db->driverName !== 'sqlite') { $sql .= ' FOR UPDATE'; }
                Yii::$app->db->createCommand($sql, [':id' => $candidate->id])->queryScalar();
                $item = WorkItem::findOne($candidate->id);
                if (!$item || $item->archived_at || !in_array($item->status, ['done', 'rejected'], true) || $item->updated_at > $before) {
                    $tx->rollBack();
                    continue;
                }
                $previous = $item->getAttributes();
                $item->archived_at = time();
                $item->archived_by = null;
                $item->updated_at = time();
                $item->revision++;
                if (!$item->save(false)) { throw new \RuntimeException('Vorhaben konnte nicht archiviert werden.'); }
                $event = new WorkEvent(['work_item_id' => $item->id, 'space_id' => $item->space_id, 'actor_id' => null,
                    'action' => 'archive', 'note' => 'Automatisch nach ' . $days . ' Tagen archiviert.',
                    'before_json' => Json::encode($previous), 'after_json' => Json::encode($item->getAttributes()), 'created_at' => time()]);
                if (!$event->save(false)) { throw new \RuntimeException('Archivierung konnte nicht protokolliert werden.'); }
                $tx->commit();
                $count++;
            } catch (\Throwable $e) {
                $tx->rollBack();
                throw $e;
            }
        }
        return $count;
    }
}
