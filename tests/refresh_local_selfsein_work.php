<?php
// SPDX-License-Identifier: AGPL-3.0-only

declare(strict_types=1);

/**
 * Replaces all work items in the local HumHub test instance with a small,
 * selfsein.events-oriented starting set. It never runs outside that instance.
 *
 * Usage (WSL):
 * HUMHUB_ROOT="$HOME/.local/share/humhub-test/app" php tests/refresh_local_selfsein_work.php
 */

use humhub\components\console\Application;
use humhub\modules\sociocraticGovernance\models\{Circle, CircleForm, WorkEvent, WorkItem, WorkResource, WorkResourceContribution, WorkTopic};
use humhub\modules\sociocraticGovernance\services\{CircleService, WorkService};
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\services\BootstrapService;

$root = rtrim((string) getenv('HUMHUB_ROOT'), '/');
if ($root === '' || !str_contains($root, '/.local/share/humhub-test/app')) {
    throw new RuntimeException('Dieses Skript darf nur mit der lokalen humhub-test-Instanz ausgeführt werden.');
}

$protected = $root . '/protected';
$loader = require $protected . '/vendor/autoload.php';
Dotenv\Dotenv::createMutable($root, '.env')->safeLoad();
$loader->addClassMap(['humhub\\services\\BootstrapService' => $protected . '/humhub/services/BootstrapService.php']);
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
require $protected . '/vendor/yiisoft/yii2/Yii.php';
Yii::setAlias('@humhub', $protected . '/humhub');
$app = new Application((new BootstrapService())->getConfig('console'));

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

$ensureCompetenceCircle = static function (string $name, string $description, string $purpose) use ($as, $ingo): Space {
    $space = Space::findOne(['name' => $name]);
    if (!$space instanceof Space) {
        $space = $as($ingo, static function () use ($name, $description): Space {
            $newSpace = new Space(['scenario' => Space::SCENARIO_CREATE]);
            $newSpace->name = $name;
            $newSpace->description = $description;
            $newSpace->visibility = Space::VISIBILITY_REGISTERED_ONLY;
            $newSpace->join_policy = Space::JOIN_POLICY_APPLICATION;
            if (!$newSpace->save()) {
                throw new RuntimeException('Kompetenzkreis konnte nicht angelegt werden: ' . implode(' ', $newSpace->getFirstErrors()));
            }
            return $newSpace;
        });
    } else {
        $space->description = $description;
        $space->save(false, ['description']);
    }
    if (!$space->moduleManager->isEnabled('sociocratic-governance') && !$space->moduleManager->enable('sociocratic-governance')) {
        throw new RuntimeException('Governance-Modul konnte nicht aktiviert werden: ' . $name);
    }
    $space = Space::findOne($space->id);
    if (!$space->isMember($ingo->id) && !$space->addMember($ingo->id, 1, true, Space::USERGROUP_ADMIN)) {
        throw new RuntimeException('Ingo Testkonto konnte nicht Mitglied des Kompetenzkreises werden: ' . $name);
    }
    $existingCircle = Circle::findOne($space->id);
    $form = CircleForm::forCircle($existingCircle);
    if ($existingCircle && Circle::find()->where(['color' => $form->color])->andWhere(['<>', 'space_id', $space->id])->exists()) {
        $form->color = Circle::suggestedColor($space->id);
    }
    $form->type = 'competence';
    $form->purpose = $purpose;
    $form->mandate = 'Bietet Menschen aus unterschiedlichen Projektkreisen einen offenen Raum, um Erfahrungen, Methoden und Wissen miteinander zu teilen.';
    $form->mandate_summary = $purpose;
    $form->parent_space_id = null;
    if (!$as($ingo, static fn(): bool => (new CircleService())->save($space, $form))) {
        throw new RuntimeException('Kompetenzkreis konnte nicht gespeichert werden: ' . $name . ' – ' . implode(' ', $form->getFirstErrors()));
    }
    return $space;
};

$communicationCompetence = $ensureCompetenceCircle(
    'Kompetenzkreis Kommunikation & Marketing',
    'Wissensaustausch zu Einladung, Sprache, Sichtbarkeit und verantwortungsvoller Kommunikation.',
    'Menschen aus verschiedenen Projekten teilen Wissen darüber, wie selbstsein.events verständlich, einladend und stimmig sichtbar wird.'
);
$projectCompetence = $ensureCompetenceCircle(
    'Kompetenzkreis Projektmanagement',
    'Wissensaustausch zu gemeinsamer Planung, Durchführung und Reflexion von Projekten.',
    'Menschen aus verschiedenen Projekten teilen praktische Erfahrungen zu klaren nächsten Schritten, Zusammenarbeit und lernenden Projektabläufen.'
);
$spaceHoldingCompetence = $ensureCompetenceCircle(
    'Kompetenzkreis Raumhaltung',
    'Wissensaustausch über die Gestaltung und Reflexion sicherer, einladender und tragfähiger Räume.',
    'Menschen, die Treffen oder Gesprächsräume halten, reflektieren Erfahrungen und entwickeln gemeinsam eine hilfreiche Orientierung für gute Raumhaltung.'
);

$communicationProject = Space::findOne(['name' => 'Kreis Kommunikation & Wirkung']);
if ($communicationProject instanceof Space) {
    foreach ($communicationProject->getMemberListService()->getQuery()->all() as $member) {
        if ((int) $member->status === User::STATUS_ENABLED && !$communicationCompetence->isMember($member->id)) {
            $communicationCompetence->addMember($member->id, 1, true);
        }
    }
}

$assignedColors = [];
$recolored = 0;
foreach (Circle::find()->orderBy(['space_id' => SORT_ASC])->all() as $circle) {
    $color = (string) $circle->color;
    if (array_key_exists($color, Circle::COLORS) && !isset($assignedColors[$color])) {
        $assignedColors[$color] = true;
        continue;
    }
    $color = Circle::suggestedColor((int) $circle->space_id);
    if ($color === '') {
        throw new RuntimeException('Für jeden Kreis wird eine eigene Farbe benötigt; die Farbpalette ist ausgeschöpft.');
    }
    $circle->color = $color;
    if (!$circle->save(false, ['color'])) {
        throw new RuntimeException('Kreisfarbe konnte nicht gespeichert werden.');
    }
    $assignedColors[$color] = true;
    $recolored++;
}

$resourcesOnly = in_array('--resources-only', $argv, true);
$deleted = 0;
if (!$resourcesOnly) {
    $transaction = Yii::$app->db->beginTransaction();
    try {
        foreach (WorkItem::find()->all() as $item) {
            WorkEvent::deleteAll(['work_item_id' => $item->id]);
            foreach (WorkResource::find()->where(['work_item_id' => $item->id])->all() as $resource) {
                WorkResourceContribution::deleteAll(['resource_id' => $resource->id]);
            }
            WorkResource::deleteAll(['work_item_id' => $item->id]);
            WorkTopic::deleteAll(['work_item_id' => $item->id]);
            $item->delete();
            $deleted++;
        }
        $transaction->commit();
    } catch (Throwable $exception) {
        $transaction->rollBack();
        throw $exception;
    }
}

$findSpace = static function (array $names): ?Space {
    foreach ($names as $name) {
        $space = Space::findOne(['name' => $name]);
        if ($space instanceof Space && Circle::findOne($space->id)) {
            return $space;
        }
    }
    return null;
};
$specifications = [
    [['Kreis Kommunikation & Wirkung', 'Arbeitskreis Kommunikation & Wirkung'], 'Selbstsein.events in drei Sätzen erklären', 'Eine gemeinsame, menschliche Kurzbeschreibung formulieren: Worum geht es, für wen ist der Raum da und wie kann jemand behutsam den ersten Kontakt aufnehmen?', 'Kommunikation, Einstieg', [
        ['resource_type' => 'time', 'label' => 'Gemeinsame Textzeit', 'required_amount' => 4, 'unit' => 'Stunden', 'time_mode' => 'async', 'time_pattern' => 'Bis zur nächsten gemeinsamen Abstimmung.', 'details' => 'Entwürfe lesen, Rückmeldungen geben und eine verständliche Fassung zusammenführen.'],
        ['resource_type' => 'expertise', 'label' => 'Unterschiedliche Perspektiven auf die Einladung', 'required_amount' => 3, 'unit' => 'Perspektiven', 'details' => 'Menschen mit unterschiedlicher Nähe zu selbstsein.events prüfen, ob die Beschreibung verständlich und einladend wirkt.'],
    ]],
    [['Arbeitskreis selbstsein Auszeit'], 'Nächste Selbstsein Auszeit gemeinsam vorbereiten', 'Einen passenden Termin, einen zugänglichen Ort und eine ruhige Einladung zusammentragen. Erst wenn ein Termin trägt, entsteht daraus ein zeitlich begrenztes Umsetzungsteam.', 'Auszeit, Veranstaltung', [
        ['resource_type' => 'time', 'label' => 'Vorbereitungstreffen', 'required_amount' => 3, 'unit' => 'Stunden', 'time_mode' => 'scheduled', 'time_pattern' => 'Ein gemeinsamer Termin vor der Veröffentlichung.', 'details' => 'Termin, Ablauf, Zugänge und offene Fragen gemeinsam klären.'],
        ['resource_type' => 'other', 'label' => 'Ruhiger und zugänglicher Ort', 'required_amount' => 1, 'unit' => 'Ort', 'details' => 'Ein Raum mit Rückzugsmöglichkeiten, klaren Anreiseinformationen und guter Erreichbarkeit.'],
    ]],
    [['Arbeitskreis Gesprächskreis'], 'Offenen Gesprächskreis einladend gestalten', 'Ein Thema, einen verlässlichen Rahmen und eine Einladung vorbereiten, damit Menschen ohne Vorerfahrung gut ankommen und zuhören können.', 'Gespräch, Teilhabe', [
        ['resource_type' => 'expertise', 'label' => 'Erfahrung in wertschätzender Moderation', 'required_amount' => 1, 'unit' => 'Person', 'details' => 'Jemand, der oder die beim Rahmen, beim Ankommen und beim Abschluss mitdenkt.'],
        ['resource_type' => 'time', 'label' => 'Zeit für den Gesprächsrahmen', 'required_amount' => 2, 'unit' => 'Stunden', 'time_mode' => 'scheduled', 'time_pattern' => 'Vor dem ersten offenen Gesprächskreis.', 'details' => 'Thema, Ablauf, Pausen und Hinweise zur Freiwilligkeit gemeinsam vorbereiten.'],
    ]],
    [['Kreis Gestaltung & Zugänglichkeit', 'Arbeitskreis Gestaltung & Zugänglichkeit'], 'Einladung und Informationen auf Verständlichkeit prüfen', 'Gemeinsam prüfen: Sind Sprache, Ablauf, Hinweise zu Ort und Kontakt für unterschiedliche Menschen klar und zugänglich?', 'Zugänglichkeit, Kommunikation', [
        ['resource_type' => 'expertise', 'label' => 'Prüfung auf Verständlichkeit und Zugänglichkeit', 'required_amount' => 2, 'unit' => 'Rückmeldungen', 'details' => 'Rückmeldungen von Menschen mit unterschiedlichen Voraussetzungen und Erfahrungen einholen.'],
        ['resource_type' => 'time', 'label' => 'Überarbeitungszeit', 'required_amount' => 3, 'unit' => 'Stunden', 'time_mode' => 'async', 'time_pattern' => 'Nach den Rückmeldungen bis zur Veröffentlichung.', 'details' => 'Texte und Hinweise anhand der Rückmeldungen verständlicher machen.'],
    ]],
    [['Kreis Digitale Räume & Infrastruktur', 'Arbeitskreis Digitale Räume & Infrastruktur'], 'Gemeinschaftlichen Kalender für Selbstsein-Termine einrichten', 'Einen einfachen, gut auffindbaren Überblick für Gespräche, Auszeiten und offene Treffen schaffen – mit klaren Kontakt- und Zugangshinweisen.', 'Digitale Räume, Orientierung', [
        ['resource_type' => 'time', 'label' => 'Einrichtung und Test des Kalenders', 'required_amount' => 4, 'unit' => 'Stunden', 'time_mode' => 'async', 'time_pattern' => 'Vor der nächsten Terminveröffentlichung.', 'details' => 'Kalender einrichten, mit einem Testtermin prüfen und Hinweise zur Nutzung ergänzen.'],
        ['resource_type' => 'expertise', 'label' => 'Erfahrung mit verständlichen digitalen Zugängen', 'required_amount' => 1, 'unit' => 'Person', 'details' => 'Prüft, ob der Kalender ohne Vorkenntnisse gut auffindbar und nutzbar ist.'],
    ]],
    [['Arbeitskreis Wheel of Consent'], 'Wheel of Consent als Gesprächsformat behutsam vorbereiten', 'Für ein erstes Format einen geschützten Rahmen, klare Hinweise zur Freiwilligkeit und eine einladende Beschreibung vorbereiten.', 'Körperwissen, Consent, Gespräch', [
        ['resource_type' => 'expertise', 'label' => 'Kenntnis zu Consent und Freiwilligkeit', 'required_amount' => 1, 'unit' => 'Person', 'details' => 'Jemand mit Erfahrung, der oder die auf klare Grenzen, Sprache und Freiwilligkeit achtet.'],
        ['resource_type' => 'other', 'label' => 'Geschützter Rahmen', 'required_amount' => 1, 'unit' => 'Vereinbarung', 'details' => 'Eine klar kommunizierte Vereinbarung zu Freiwilligkeit, Pausen, Grenzen und Rückzugsmöglichkeiten.'],
    ]],
    [[$communicationCompetence->name], 'Erfahrungsaustausch: Was macht eine Einladung stimmig?', 'Menschen aus verschiedenen Projekten sammeln Beispiele und Kriterien für Sprache, Bilder und Wege der Ansprache, die neugierig machen ohne zu drängen.', 'Kommunikation, Wissensaustausch', [
        ['resource_type' => 'time', 'label' => 'Austausch- und Reflexionszeit', 'required_amount' => 2, 'unit' => 'Stunden', 'time_mode' => 'scheduled', 'time_pattern' => 'Ein gemeinsamer Kompetenzkreis-Termin.', 'details' => 'Beispiele teilen, Erfahrungen vergleichen und hilfreiche Kriterien sammeln.'],
        ['resource_type' => 'material', 'label' => 'Beispiele von Einladungen', 'required_amount' => 5, 'unit' => 'Beispiele', 'details' => 'Einladungen aus unterschiedlichen Projekten als Gesprächsgrundlage.'],
    ]],
    [[$projectCompetence->name], 'Erfahrungsaustausch: Projekte mit klaren nächsten Schritten starten', 'Eine kurze gemeinsame Praxis sammeln: Wie wird aus einer Initiative ein überschaubares Vorhaben, das Menschen mit unterschiedlichen Fähigkeiten mittragen können?', 'Projektmanagement, Wissensaustausch', [
        ['resource_type' => 'time', 'label' => 'Lernwerkstatt Projektstart', 'required_amount' => 2, 'unit' => 'Stunden', 'time_mode' => 'scheduled', 'time_pattern' => 'Ein gemeinsamer Kompetenzkreis-Termin.', 'details' => 'Erfahrungen zu ersten Schritten, Verantwortung und überschaubaren Aufgaben teilen.'],
        ['resource_type' => 'expertise', 'label' => 'Erfahrungen aus unterschiedlichen Projekten', 'required_amount' => 3, 'unit' => 'Beispiele', 'details' => 'Konkrete Geschichten darüber, was Projektstarts erleichtert oder erschwert hat.'],
    ]],
    [[$spaceHoldingCompetence->name], 'Leitfaden für gute Raumhaltung entwickeln', 'Raumhaltende reflektieren gemeinsam, was einen sicheren und einladenden Rahmen trägt. Daraus entsteht ein kurzer Leitfaden mit Mindestanforderungen und hilfreichen Fragen für Vorbereitung, Durchführung und Nachbereitung.', 'Raumhaltung, Reflexion, Leitfaden', [
        ['resource_type' => 'time', 'label' => 'Reflexionsrunde mit Raumhaltenden', 'required_amount' => 3, 'unit' => 'Stunden', 'time_mode' => 'scheduled', 'time_pattern' => 'Ein moderierter Kompetenzkreis-Termin.', 'details' => 'Erfahrungen zu Ankommen, Grenzen, Freiwilligkeit, Pausen und Abschied gemeinsam reflektieren.'],
        ['resource_type' => 'expertise', 'label' => 'Erfahrungen aus der Raumhaltung', 'required_amount' => 3, 'unit' => 'Perspektiven', 'details' => 'Menschen, die bereits Räume gehalten haben, bringen konkrete Erfahrungen und offene Fragen ein.'],
        ['resource_type' => 'material', 'label' => 'Entwurf für den Leitfaden', 'required_amount' => 1, 'unit' => 'Dokument', 'details' => 'Ein gemeinsamer Entwurf, der nach der Reflexionsrunde weiterentwickelt werden kann.'],
    ]],
];

$created = 0;
$enriched = 0;
$work = new WorkService();
foreach ($specifications as [$names, $title, $description, $topics, $resources]) {
    $space = $findSpace($names);
    if (!$space instanceof Space) {
        continue;
    }
    $circle = Circle::findOne($space->id);
    $actor = $circle?->roles[0]?->user ?? $ingo;
    if (!$actor instanceof User || (int) $actor->status !== User::STATUS_ENABLED) {
        $actor = $ingo;
    }
    $item = WorkItem::findOne(['space_id' => $space->id, 'title' => $title]);
    if (!$item instanceof WorkItem) {
        $item = $as($actor, static fn() => $work->create($space, $title, $description, 'task', $topics));
        $created++;
    } else {
        foreach (WorkResource::find()->where(['work_item_id' => $item->id])->all() as $resource) {
            WorkResourceContribution::deleteAll(['resource_id' => $resource->id]);
        }
        WorkResource::deleteAll(['work_item_id' => $item->id]);
        $enriched++;
    }
    foreach ($resources as $resource) {
        $item = $as($actor, static fn() => $work->change($item->id, $item->revision, 'resource', $resource));
    }
}

echo "Vorhaben bereinigt: {$deleted}. Neu angelegt: {$created}. Mit Ressourcen ergänzt: {$enriched}. Kreisfarben neu vergeben: {$recolored}. Kompetenzkreise Kommunikation & Marketing, Projektmanagement sowie Raumhaltung sind eingerichtet.\n";
