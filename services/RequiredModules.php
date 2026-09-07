<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

use humhub\modules\space\models\Space;

/** Only enables installed, globally available modules using HumHub's manager. */
final class RequiredModules
{
    public const MODULES = [
        'peertube' => ['Community-Mediathek', '2.8.6'],
        'sharebetween' => ['Share Content', '1.2.1'],
        'wiki' => ['Wiki', '2.5.12'],
    ];

    public static function enable(Space $space): array
    {
        $messages = [];
        $manager = $space->moduleManager;
        foreach (self::MODULES as $id => [$name, $minimum]) {
            $module = \Yii::$app->getModule($id);
            if (!$module || !$module->getIsEnabled() || version_compare($module->getVersion(), $minimum, '<')) {
                $messages[] = $name . ' benötigt Version ' . $minimum . '+ und globale Aktivierung.';
                continue;
            }
            if ($manager->isEnabled($id)) { continue; }
            if (!$manager->canEnable($id)) {
                $messages[] = $name . ' ist für diesen Space nicht zur Aktivierung verfügbar.';
                continue;
            }
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                if (!$manager->enable($id)) { throw new \RuntimeException('Module activation failed'); }
                $transaction->commit();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                $messages[] = $name . ' konnte nicht aktiviert werden. Bitte die Modulverwaltung prüfen.';
            }
        }
        return $messages;
    }
}
