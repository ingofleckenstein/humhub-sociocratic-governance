<?php
// SPDX-License-Identifier: AGPL-3.0-only
namespace humhub\modules\sociocraticGovernance\services;

/** Pure rules shared by persistence and tests; no HumHub data is read here. */
final class Rules
{
    public static function assertRoles(array $roles, array $memberIds): void
    {
        foreach ($roles as $key => $id) {
            if (!in_array($key, ['leader', 'delegate', 'facilitator', 'secretary'], true)) {
                throw new \DomainException('Unbekannte Rolle.');
            }
            if ($id !== null && (!is_int($id) || !in_array($id, $memberIds, true))) {
                throw new \DomainException('Rollen können nur aktiven Kreismitgliedern zugeordnet werden.');
            }
        }
        if (!empty($roles['leader']) && $roles['leader'] === ($roles['delegate'] ?? null)) {
            throw new \DomainException('Kreisleitung und Delegierte*r müssen verschiedene Personen sein.');
        }
    }

    public static function assertParent(int $spaceId, ?int $parentId, array $parents): void
    {
        $seen = [$spaceId => true];
        while ($parentId !== null) {
            if (isset($seen[$parentId])) {
                throw new \DomainException('Die Kreisstruktur darf keinen Kreis auf sich selbst zurückführen.');
            }
            if (!array_key_exists($parentId, $parents)) {
                throw new \DomainException('Der Oberkreis ist nicht verfügbar.');
            }
            $seen[$parentId] = true;
            $parentId = $parents[$parentId];
        }
    }
}

