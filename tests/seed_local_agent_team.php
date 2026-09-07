<?php
// SPDX-License-Identifier: AGPL-3.0-only

declare(strict_types=1);

/**
 * Seeds the local HumHub test instance with a transparent, fictional
 * sociocratic test team. It deliberately has no network or mail integration.
 *
 * Usage (WSL):
 * HUMHUB_ROOT="$HOME/.local/share/humhub-test/app" php tests/seed_local_agent_team.php
 */

use humhub\components\console\Application;
use humhub\modules\sociocraticGovernance\models\Circle;
use humhub\modules\sociocraticGovernance\models\CircleForm;
use humhub\modules\sociocraticGovernance\models\Configuration;
use humhub\modules\sociocraticGovernance\models\WorkEvent;
use humhub\modules\sociocraticGovernance\models\WorkItem;
use humhub\modules\sociocraticGovernance\models\WorkResource;
use humhub\modules\sociocraticGovernance\models\WorkResourceContribution;
use humhub\modules\sociocraticGovernance\models\WorkTopic;
use humhub\modules\sociocraticGovernance\services\CircleService;
use humhub\modules\sociocraticGovernance\services\WorkService;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\services\BootstrapService;

$root = rtrim((string) getenv('HUMHUB_ROOT'), '/');
if ($root === '' || !str_contains($root, '/.local/share/humhub-test/app')) {
    throw new RuntimeException('Dieses Test-Seed-Skript darf nur mit der lokalen humhub-test-Instanz ausgeführt werden.');
}

$protected = $root . '/protected';
$loader = require $protected . '/vendor/autoload.php';
Dotenv\Dotenv::createMutable($root, '.env')->safeLoad();
$loader->addClassMap(['humhub\\services\\BootstrapService' => $protected . '/humhub/services/BootstrapService.php']);
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
require $protected . '/vendor/yiisoft/yii2/Yii.php';
Yii::setAlias('@humhub', $protected . '/humhub');
$bootstrap = new BootstrapService();
$app = new Application($bootstrap->getConfig('console'));

/** @param callable():mixed $operation */
$as = static function (User $user, callable $operation): mixed {
    $previous = Yii::$app->user->getIdentity();
    Yii::$app->user->setIdentity($user);
    try {
        return $operation();
    } finally {
        Yii::$app->user->setIdentity($previous);
    }
};

$ingo = User::findOne(['username' => 'Ingo']);
if (!$ingo instanceof User || (int) $ingo->status !== User::STATUS_ENABLED) {
    throw new RuntimeException('Das lokale Konto „Ingo Testkonto“ fehlt oder ist nicht aktiv.');
}

$agents = [
    'lina.morgen' => ['title' => 'Test-Agentin · Produktkoordination', 'job' => 'übersetzt Ingos Erweiterungsvorschläge in überprüfbare Vorhaben und hält den Gesamtfokus.'],
    'karim.vogt' => ['title' => 'Test-Agent · Systementwicklung', 'job' => 'prüft technische Machbarkeit, Architektur und sichere Umsetzungen.'],
    'meera.falk' => ['title' => 'Test-Agentin · Community-Research', 'job' => 'macht Bedürfnisse, Verständlichkeit und Beteiligung sichtbar.'],
    'jonas.hart' => ['title' => 'Test-Agent · Qualitätssicherung', 'job' => 'reproduziert Fehler, dokumentiert sie nachvollziehbar und prüft Korrekturen.'],
    'elif.kern' => ['title' => 'Test-Agentin · Soziokratische Moderation', 'job' => 'achtet auf klare Mandate, Beteiligung und nachvollziehbare Entscheidungen.'],
    'paula.sturm' => ['title' => 'Test-Agentin · Inklusion & Mitgliederpflege', 'job' => 'testet Einstiegshürden und Folgen für neue sowie bestehende Mitglieder.'],
    'theo.brandt' => ['title' => 'Test-Agent · Interaction Design', 'job' => 'entwickelt verständliche, zugängliche Abläufe und prüft die Oberfläche.'],
    'nora.stein' => ['title' => 'Test-Agentin · Dokumentation', 'job' => 'hält Entscheidungen, Tests und bekannte Grenzen verständlich fest.'],
    'rafael.winter' => ['title' => 'Test-Agent · Soziologie', 'job' => 'prüft Folgen für Rollen, Gruppen, Machtverteilung und Gemeinschaftsdynamik.'],
    'juno.adler' => ['title' => 'Test-Agentin · Psychologie', 'job' => 'prüft menschliche Kompatibilität, Belastung, Sicherheit und psychologische Verständlichkeit.'],
];

$users = [];
foreach ($agents as $username => $specification) {
    $user = User::findOne(['username' => $username]);
    if (!$user instanceof User || (int) $user->status !== User::STATUS_ENABLED) {
        throw new RuntimeException('Erwartetes lokales Testkonto fehlt oder ist nicht aktiv: ' . $username);
    }
    $profile = $user->profile;
    $profile->title = $specification['title'];
    $profile->about = 'Lokale, fiktive Test-Agentin bzw. lokaler Test-Agent. ' . $specification['job'] . ' Arbeitet ausschließlich innerhalb dieser lokalen HumHub-Testcommunity.';
    if (!$profile->save(false, ['title', 'about'])) {
        throw new RuntimeException('Testprofil konnte nicht aktualisiert werden: ' . $username);
    }
    $users[$username] = $user;
}

$configuration = Configuration::findOne(1);
$parent = $configuration?->root_space_id ? Space::findOne((int) $configuration->root_space_id) : null;
if (!$parent instanceof Space || !Circle::findOne($parent->id)) {
    throw new RuntimeException('Der konfigurierte Kernkreis fehlt. Die Test-Team-Hierarchie wird nicht außerhalb eines Kernkreises angelegt.');
}
$users['ingo'] = $ingo;

/** @param list<string> $members */
$createOrUpdateCircle = static function (
    string $name,
    string $description,
    ?Space $parentSpace,
    array $members,
    array $roles,
    string $purpose,
    string $mandate
) use ($as, $ingo, $users): Space {
    $space = Space::findOne(['name' => $name]);
    if (!$space instanceof Space) {
        $space = $as($ingo, static function () use ($name, $description): Space {
            $newSpace = new Space(['scenario' => Space::SCENARIO_CREATE]);
            $newSpace->name = $name;
            $newSpace->description = $description;
            $newSpace->visibility = Space::VISIBILITY_REGISTERED_ONLY;
            $newSpace->join_policy = Space::JOIN_POLICY_APPLICATION;
            $newSpace->color = '#546E7A';
            if (!$newSpace->save()) {
                throw new RuntimeException('Arbeitskreis konnte nicht angelegt werden: ' . implode(' ', $newSpace->getFirstErrors()));
            }
            return $newSpace;
        });
    } else {
        $space->description = $description;
        $space->save(false, ['description']);
    }

    if (!$space->moduleManager->isEnabled('sociocratic-governance')) {
        if (!$space->moduleManager->enable('sociocratic-governance')) {
            throw new RuntimeException('Governance-Modul konnte nicht aktiviert werden: ' . $name);
        }
        $space = Space::findOne($space->id);
    }

    if (!$space->addMember($ingo->id, 1, true, Space::USERGROUP_ADMIN)) {
        throw new RuntimeException('Ingo Testkonto konnte nicht als lokale Anforderungsschnittstelle gesetzt werden: ' . $name);
    }

    foreach ($members as $username) {
        if (!$space->addMember($users[$username]->id, 1, true)) {
            throw new RuntimeException('Mitgliedschaft konnte nicht gesetzt werden: ' . $username . ' / ' . $name);
        }
    }

    $circle = Circle::findOne($space->id);
    $form = new CircleForm([
        'purpose' => $purpose,
        'mandate' => $mandate,
        'mandate_summary' => $purpose,
        'responsibility' => 'Bearbeitet die zugeordneten Vorhaben transparent im lokalen Testsystem.',
        'authority' => 'Kann innerhalb des eigenen Mandats Arbeitsweisen, Tests und Unterkreise vorschlagen.',
        'boundaries' => 'Keine externe Kommunikation, keine Änderungen außerhalb der lokalen Testcommunity.',
        'budget' => 'Keine externen Ressourcen; nur die lokale Testumgebung.',
        'reelection_interval' => 'Nach jeder Testphase',
        'review' => 'Die Rollen und das Mandat werden nach einem abgeschlossenen Vorhaben gemeinsam überprüft.',
        'parent_space_id' => $parentSpace?->id,
        'revision' => $circle ? (int) $circle->revision : -1,
        'leader' => $users[$roles['leader']]->id,
        'delegate' => $users[$roles['delegate']]->id,
        'facilitator' => $users[$roles['facilitator']]->id,
        'secretary' => $users[$roles['secretary']]->id,
    ]);
    $saved = $as($ingo, static function () use ($space, $name, $form): bool {
        if (!\humhub\modules\sociocraticGovernance\services\Access::write($space)) {
            throw new RuntimeException('Ingo Testkonto hat im Arbeitskreis keine Schreibberechtigung: ' . $name);
        }
        return (new CircleService())->save($space, $form);
    });
    if (!$saved) {
        throw new RuntimeException('Arbeitskreis konnte nicht konfiguriert werden: ' . $name . ' – ' . implode(' ', $form->getFirstErrors()));
    }

    return $space;
};

$coreCircle = Circle::findOne($parent->id);
foreach (['elif.kern', 'paula.sturm', 'juno.adler'] as $username) {
    if (!$parent->addMember($users[$username]->id, 1, true)) {
        throw new RuntimeException('Kernteam-Mitgliedschaft konnte nicht bestätigt werden: ' . $username);
    }
}
$coreForm = CircleForm::forCircle($coreCircle);
$coreForm->leader = $users['elif.kern']->id;
$coreForm->delegate = $users['paula.sturm']->id;
$coreForm->facilitator = $users['juno.adler']->id;
$coreForm->secretary = $ingo->id;
if (!$as($ingo, static fn(): bool => (new CircleService())->save($parent, $coreForm))) {
    throw new RuntimeException('Kernteam-Rollen konnten nicht gesetzt werden: ' . implode(' ', $coreForm->getFirstErrors()));
}

$legacyTeam = Space::findOne(['name' => 'Test-Team · Selbstsein App']);
if ($legacyTeam instanceof Space && !Space::findOne(['name' => 'Test-Team · Umsetzung & Integration'])) {
    $legacyTeam->name = 'Test-Team · Umsetzung & Integration';
    if (!$legacyTeam->save(false, ['name'])) {
        throw new RuntimeException('Der Umsetzungskreis konnte nicht umbenannt werden.');
    }
}

$teamUsernames = array_keys($agents);
$team = $createOrUpdateCircle(
    'Test-Team · Umsetzung & Integration',
    'Fiktives, transparentes Team für die koordinierte lokale Umsetzung.',
    $parent,
    $teamUsernames,
    ['leader' => 'lina.morgen', 'delegate' => 'karim.vogt', 'facilitator' => 'elif.kern', 'secretary' => 'nora.stein'],
    'Anforderungen aus dem Kernteam in passende Fachkreise verteilen und ihre Umsetzung integrieren.',
    'Nimmt vom KERN legitimierte Vorhaben auf, verteilt sie an direkte Fachkreise und führt technische wie soziale Ergebnisse wieder zusammen.'
);

$product = $createOrUpdateCircle(
    'Test-Team · Produkt & Erfahrung',
    'Nutzerbedürfnisse, Produktentscheidungen und verständliche Abläufe.',
    $team,
    ['lina.morgen', 'meera.falk', 'paula.sturm', 'theo.brandt', 'elif.kern', 'nora.stein'],
    ['leader' => 'meera.falk', 'delegate' => 'paula.sturm', 'facilitator' => 'elif.kern', 'secretary' => 'nora.stein'],
    'Neue Erweiterungen in sinnvolle, testbare und inklusive Produktvorhaben übersetzen.',
    'Sammelt Anforderungen und führt sie nur mit nachvollziehbarer Wirkung auf die Community weiter.'
);

$technical = $createOrUpdateCircle(
    'Test-Team · Technik & Qualität',
    'Technische Umsetzbarkeit, Fehlerberichte, Korrekturen und Abnahme.',
    $team,
    ['karim.vogt', 'jonas.hart', 'nora.stein', 'rafael.winter'],
    ['leader' => 'karim.vogt', 'delegate' => 'jonas.hart', 'facilitator' => 'nora.stein', 'secretary' => 'rafael.winter'],
    'Fehler reproduzierbar machen und sichere, überprüfte Korrekturen bereitstellen.',
    'Führt einen transparenten internen Fehler- und Abnahmeprozess; bei wachsendem Aufwand darf ein Unterkreis entstehen.'
);

$human = $createOrUpdateCircle(
    'Test-Team · Mensch & Gemeinschaft',
    'Soziologische und psychologische Folgen für Menschen und Gruppen.',
    $team,
    ['juno.adler', 'rafael.winter', 'meera.falk', 'paula.sturm', 'elif.kern'],
    ['leader' => 'juno.adler', 'delegate' => 'rafael.winter', 'facilitator' => 'elif.kern', 'secretary' => 'paula.sturm'],
    'Menschliche und gemeinschaftliche Kompatibilität der Community-App sichern.',
    'Prüft Verständlichkeit, psychologische Sicherheit, soziale Dynamik, Rollenfolgen und Teilhabe.'
);

$design = $createOrUpdateCircle(
    'Test-Team · Gestaltung & Zugänglichkeit',
    'Interaktionsdesign, Verständlichkeit und Barrierearmut.',
    $product,
    ['theo.brandt', 'lina.morgen', 'meera.falk', 'paula.sturm', 'juno.adler'],
    ['leader' => 'theo.brandt', 'delegate' => 'lina.morgen', 'facilitator' => 'meera.falk', 'secretary' => 'paula.sturm'],
    'Eine klare, zugängliche und angenehme Nutzung der Community-App gestalten.',
    'Bewertet konkrete Oberflächen und Abläufe; kann bei Bedarf einen eigenen Testkreis beantragen.'
);

$seedItems = [
    [$parent, 'Entwicklungsauftrag: Selbstsein Community-App'],
    [$parent, 'Beispielidee: Rückmeldungen ohne Zeitdruck'],
    [$parent, 'Beispielidee: Nicht im Mandat'],
    [$team, 'Entwicklungsauftrag: Selbstsein Community-App'],
    [$product, 'Beispielaufgabe: Ressourcen für einen Beteiligungstest'],
    [$design, 'Beispielaufgabe: Entwurf wird gerade ausgearbeitet'],
    [$technical, 'Fehlerbericht: Vorhaben-Board benötigt Datenbankmigration'],
    [$technical, 'Beispielaufgabe: Prüfnachweise warten auf Abnahme'],
];
foreach ($seedItems as [$space, $title]) {
    foreach (WorkItem::find()->where(['space_id' => $space->id, 'title' => $title])->all() as $item) {
        WorkEvent::deleteAll(['work_item_id' => $item->id]);
        foreach (WorkResource::find()->where(['work_item_id' => $item->id])->all() as $resource) {
            WorkResourceContribution::deleteAll(['resource_id' => $resource->id]);
        }
        WorkResource::deleteAll(['work_item_id' => $item->id]);
        WorkTopic::deleteAll(['work_item_id' => $item->id]);
        $item->delete();
    }
}

$obsolete = Space::findOne(['name' => 'TEST123']);
if ($obsolete instanceof Space && !$obsolete->isArchived() && !WorkItem::find()->where(['space_id' => $obsolete->id])->exists()) {
    $obsolete->archive();
}

$work = new WorkService();
$productBrief = $as($ingo, static fn() => $work->create(
    $parent,
    'Entwicklungsauftrag: Selbstsein Community-App',
    'Ingo Testkonto gibt Erweiterungen als zu prüfende Vorhaben ein. Das KERN-Team legitimiert und verteilt sie, das Umsetzungsteam koordiniert die Fachkreise. Befunde und sichere Korrekturen bleiben intern in der lokalen Umgebung.',
    'idea'
));
$productBrief = $as($users['elif.kern'], static fn() => $work->change($productBrief->id, $productBrief->revision, 'accept', [
    'note' => 'Das Vorhaben liegt im Gesamtmandat. Das KERN-Team legitimiert eine erste Umsetzungs- und Testschleife.',
]));
$productBrief = $as($users['elif.kern'], static fn() => $work->change($productBrief->id, $productBrief->revision, 'delegate', [
    'target_space_id' => $team->id,
    'note' => 'Das Umsetzungsteam klärt die Fachaufteilung und koordiniert die nächsten Schritte.',
]));
$productBrief = $as($users['karim.vogt'], static fn() => $work->change($productBrief->id, $productBrief->revision, 'claim'));
$productBrief = $as($users['karim.vogt'], static fn() => $work->change($productBrief->id, $productBrief->revision, 'start'));
$as($users['karim.vogt'], static fn() => $work->change($productBrief->id, $productBrief->revision, 'comment', [
    'note' => 'Technik & Qualität übernimmt die erste Architektur- und Regressionseinschätzung. Ergebnisse bleiben im Vorhaben-Verlauf.',
]));

$bug = $as($users['jonas.hart'], static fn() => $work->create(
    $technical,
    'Fehlerbericht: Vorhaben-Board benötigt Datenbankmigration',
    'Beim Test fehlten die Tabellen für Vorhaben und Verlauf. Der Befund war reproduzierbar und blockierte den Workflow.',
    'idea'
));
$bug = $as($users['karim.vogt'], static fn() => $work->change($bug->id, $bug->revision, 'accept', [
    'note' => 'Der Fehler liegt im technischen Mandat und wird als dringliche Korrektur übernommen.',
]));
$bug = $as($users['nora.stein'], static fn() => $work->change($bug->id, $bug->revision, 'claim'));
$bug = $as($users['nora.stein'], static fn() => $work->change($bug->id, $bug->revision, 'start'));
$bug = $as($users['nora.stein'], static fn() => $work->change($bug->id, $bug->revision, 'submit', [
    'note' => 'Migration m260906_180000_work_board ist lokal ausgeführt; beide Tabellen und Indizes sind vorhanden.',
]));
$bug = $as($users['karim.vogt'], static fn() => $work->change($bug->id, $bug->revision, 'approve', [
    'note' => 'Technische Prüfung bestanden.',
]));
$as($users['jonas.hart'], static fn() => $work->change($bug->id, $bug->revision, 'approve', [
    'note' => 'Regressionstest bestanden: Vorhaben und Verlauf lassen sich anlegen.',
]));

$idea = $as($ingo, static fn() => $work->create(
    $parent,
    'Beispielidee: Rückmeldungen ohne Zeitdruck',
    'Eine noch nicht legitimierte Idee. Sie zeigt den Ideeneingang und kann im KERN-Team angenommen, abgelehnt oder an den Umsetzungskreis weitergegeben werden.',
    'idea',
    'Community, Feedback, Zugänglichkeit'
));

$rejected = $as($ingo, static fn() => $work->create(
    $parent,
    'Beispielidee: Nicht im Mandat',
    'Diese Idee bleibt als nachvollziehbares Beispiel für eine Mandatsprüfung sichtbar.',
    'idea',
    'Governance, Priorisierung'
));
$as($users['elif.kern'], static fn() => $work->change($rejected->id, $rejected->revision, 'reject', [
    'note' => 'Der Inhalt betrifft ein externes Angebot und liegt nicht im Mandat dieser lokalen Testcommunity.',
]));

$resourceTask = $as($users['meera.falk'], static fn() => $work->create(
    $product,
    'Beispielaufgabe: Ressourcen für einen Beteiligungstest',
    'Offene Beispielaufgabe mit allen Ressourcenarten. Die individuellen Zusagen sind als Beiträge erfasst; auf der Aufgabenkarte werden nur die Mitwirkenden gezeigt.',
    'task',
    'Community, Produktentwicklung, Qualitätssicherung'
));
$addResource = static function (WorkItem $item, User $actor, array $input) use ($as, $work): WorkItem {
    return $as($actor, static fn() => $work->change($item->id, $item->revision, 'resource', $input));
};
$resourceTask = $addResource($resourceTask, $users['meera.falk'], ['resource_type' => 'money', 'label' => 'Budget für Tests', 'required_amount' => 250, 'available_amount' => 0, 'unit' => '€', 'details' => 'Kleines Budget für barrierearme Testmaterialien.']);
$resourceTask = $addResource($resourceTask, $users['meera.falk'], ['resource_type' => 'time', 'label' => 'Texte lesen und zusammenfassen', 'required_amount' => 8, 'available_amount' => 0, 'unit' => 'Stunden', 'time_mode' => 'async', 'time_pattern' => 'Im eigenen Rhythmus bis zur nächsten Reviewrunde.', 'details' => 'Asynchron leistbare Vorbereitungszeit.']);
$resourceTask = $addResource($resourceTask, $users['meera.falk'], ['resource_type' => 'time', 'label' => 'Koordinationsrunde', 'required_amount' => 3, 'available_amount' => 0, 'unit' => 'Stunden', 'time_mode' => 'scheduled', 'time_pattern' => 'Alle zwei Wochen donnerstags, 18:00–21:00 Uhr.', 'details' => 'Termingebundene gemeinsame Arbeitszeit.']);
$resourceTask = $addResource($resourceTask, $users['meera.falk'], ['resource_type' => 'expertise', 'label' => 'Barrierefreiheit prüfen', 'required_amount' => 1, 'available_amount' => 0, 'unit' => 'Prüfung', 'details' => 'Fachliche Einschätzung der geplanten Beteiligungsschritte.']);
$resourceTask = $addResource($resourceTask, $users['meera.falk'], ['resource_type' => 'material', 'label' => 'Moderationskarten', 'required_amount' => 2, 'available_amount' => 0, 'unit' => 'Sets', 'details' => 'Material für einen kleinen vor-Ort-Test.']);
$resourceTask = $addResource($resourceTask, $users['meera.falk'], ['resource_type' => 'other', 'label' => 'Ruhiger Testraum', 'required_amount' => 1, 'available_amount' => 0, 'unit' => 'Raumtermin', 'details' => 'Ein störungsarmer, gut erreichbarer Raum für den Test.']);

$contribute = static function (WorkItem $item, User $actor, string $label, float $amount) use ($as, $work): WorkItem {
    $resource = WorkResource::findOne(['work_item_id' => $item->id, 'label' => $label]);
    if (!$resource instanceof WorkResource) { throw new RuntimeException('Beispielressource fehlt: ' . $label); }
    return $as($actor, static fn() => $work->change($item->id, $item->revision, 'contribute', ['resource_id' => $resource->id, 'amount' => $amount]));
};
$resourceTask = $contribute($resourceTask, $users['lina.morgen'], 'Budget für Tests', 50);
$resourceTask = $contribute($resourceTask, $users['nora.stein'], 'Texte lesen und zusammenfassen', 4);
$resourceTask = $contribute($resourceTask, $users['paula.sturm'], 'Koordinationsrunde', 3);
$resourceTask = $contribute($resourceTask, $users['theo.brandt'], 'Barrierefreiheit prüfen', 1);
$resourceTask = $contribute($resourceTask, $users['meera.falk'], 'Moderationskarten', 1);
$resourceTask = $contribute($resourceTask, $users['juno.adler'], 'Ruhiger Testraum', 1);

$working = $as($users['theo.brandt'], static fn() => $work->create(
    $design,
    'Beispielaufgabe: Entwurf wird gerade ausgearbeitet',
    'Beispiel für eine bereits übernommene Aufgabe in Bearbeitung.',
    'task',
    'Nutzererlebnis, Dokumentation'
));
$working = $as($users['theo.brandt'], static fn() => $work->change($working->id, $working->revision, 'claim'));
$as($users['theo.brandt'], static fn() => $work->change($working->id, $working->revision, 'start'));

$review = $as($users['jonas.hart'], static fn() => $work->create(
    $technical,
    'Beispielaufgabe: Prüfnachweise warten auf Abnahme',
    'Beispiel für eine erledigte Umsetzung, die noch die unabhängige Abnahme von Kreisleitung und Delegation braucht.',
    'task',
    'Qualitätssicherung, Abnahme, Transparenz'
));
$review = $as($users['jonas.hart'], static fn() => $work->change($review->id, $review->revision, 'claim'));
$review = $as($users['jonas.hart'], static fn() => $work->change($review->id, $review->revision, 'start'));
$as($users['jonas.hart'], static fn() => $work->change($review->id, $review->revision, 'submit', [
    'note' => 'Prüfnachweise, Schritte und Ergebnis sind im Verlauf festgehalten und bereit für die Abnahme.',
]));

echo "Lokales Test-Team eingerichtet: 10 klar gekennzeichnete Profile, 5 Arbeitskreise und Beispiele für alle Vorhabenstände sowie Ressourcenarten.\n";
