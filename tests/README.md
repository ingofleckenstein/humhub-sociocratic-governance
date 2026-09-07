# Tests

Alle Tests stammen aus diesem Projekt. HumHub und Yii werden nur als Testplattform
separat bereitgestellt, nicht in das Modul kopiert.

## Komponententests

PHP 8.2+ mit PDO SQLite und mbstring; Yii 2.0.55 separat bereitstellen.
YII_FRAMEWORK zeigt auf das Verzeichnis mit Yii.php.

    YII_FRAMEWORK=/pfad/zu/yii2/framework php tests/run.php

Der Test verwendet die echte Yii-Datenbankschicht, die echte Modulmigration und unsere
Modelle/Services. HumHub-Grenzobjekte (Space, Nutzer, Mitgliedschaft) sind ausdrücklich
kleine Test-Doubles. Das ersetzt keine Installation von HumHub.

Geprüft werden Speicherung, Rollenregeln, veraltete Formulare, Kreiszyklen, Lese- und
Schreibzugriff, Profilanzeige, XSS-Escaping und referentielle Datenbankintegrität.
SQLite prüft nicht das MySQL-Sperrverhalten bei parallelen Verbindungen.

## API-Kompatibilität

    YII_FRAMEWORK=/pfad/zu/yii2/framework HUMHUB_SOURCE=/pfad/zu/humhub php tests/compatibility.php

Lädt Modul, Controller, Widgets und Eventregistrierungen gegen den echten HumHub-1.18.5-
Quellcode. Kein vollständiger Anwendungsstart und kein Ersatz für HTTP-Tests.

## Ansichten rendern

    mkdir -p /tmp/governance-preview
    YII_FRAMEWORK=/pfad/zu/yii2/framework PREVIEW_DIR=/tmp/governance-preview php tests/render.php

Rendert fünf echte View-Dateien mit Beispieldaten und prüft CSRF-Felder der Formulare.
Die Vorschau hat einen neutralen Seitenrahmen; das echte HumHub-Theme wird separat geprüft.
Die Links verweisen auf HumHub-Routen und funktionieren in der statischen Vorschau nicht.

## Syntax

    find . -name '*.php' -print0 | xargs -0 -n1 php -l
    bash -n scripts/deploy-test.sh

Backend-Autorisierung und HTTP-/CSRF-Verhalten auf HumHub mit Testkonten prüfen;
siehe [Abnahmeliste](../docs/INSTALLATION.md).

## VCard-Templates und optionale Ereignisse

    TEST_VENDOR=/pfad/zum/humhub/protected/vendor YII_FRAMEWORK=/pfad/zu/yii2/framework HUMHUB_SOURCE=/pfad/zu/humhub VCARD_SOURCE=/pfad/zu/popover-vcard php tests/vcard.php

Prüft echte Twig-Sandbox, Escaping, HTMLPurifier, einfache Platzhalter, bedingte
Vorlagen und fehlerhafte Templates. Mit HUMHUB_SOURCE und VCARD_SOURCE werden
auch die echten Widget-Erstellungsereignisse geprüft. Keine Datenbank erforderlich.
Für die Kompatibilitätsprüfung kann ebenfalls VCARD_SOURCE gesetzt werden;
ohne VCard werden dessen optionale Ereignisse übersprungen.

Die Komponentenprüfungen erfassen alle Mitglieder einschließlich Doppelrollen,
deaktivierte Personen, archivierte Kreise und wiederholbare Pflichtmoduleinrichtung
mit fehlenden/veralteten Modulen sowie Aktivierungsfehlern. Dabei bleiben HumHub-
Modulmanager Test-Doubles; die tatsächliche Aktivierung braucht die Testinstanz.
Der Renderer prüft, dass Mitgliedslinks nach HTML-Parsing innerhalb der Bubble
bleiben und keine Links ineinander verschachtelt sind.

## Vorhaben-Board

    YII_FRAMEWORK=/pfad/zu/yii2/framework php tests/work.php

Prüfungen für Ideen, Aufgaben, Kommentare, Themen, Ressourcen, persönliche Zusagen,
Archivierung, Selbstzuweisung, Abnahme,
Rollenwechsel, direkte Delegation, private Historien, veraltete Requests und
atomaren Rollback bei fehlschlagendem Historieneintrag. Die neue Migration wird
im gemeinsamen Harness ebenfalls ausgeführt. `tests/render.php` rendert jetzt
acht Ansichten, einschließlich Board, Idee und Abnahme, und prüft sichere
HTML-Ausgabe sowie POST/CSRF/Revision der neuen Formulare.

Diese isolierten Tests ersetzen keine HumHub-HTTP- und MySQL-Abnahme. Die echten
parallel laufenden MySQL-Verbindungen und das Theme sind weiterhin separat zu prüfen.
