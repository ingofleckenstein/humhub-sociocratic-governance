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
