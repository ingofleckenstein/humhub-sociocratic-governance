# Änderungen

## Generisches Deployment — 2026-09-06

- Konkrete Betriebsangaben aus aktuellem Repository entfernt.
- Installationspfad, Website-Benutzer und privates Downloadverzeichnis sind verpflichtende Einstellungen.
- Keine automatische Auswahl einer Installation anhand eingebauter Domains.

## Deployment-Überarbeitung — 2026-09-06

- Root lädt nach das konfigurierte private Downloadverzeichnis und kopiert ins Modulziel.
- Eigentümerübergabe nur für das Modul an den konfigurierten Website-Benutzer.
- Migrationen und Cacheleeren weiterhin als Website-Benutzer über runuser.
- Vorschau ändert keine Website-Dateien, Website-Eigentümer oder Datenbank.

## 0.1.0 — 2026-09-06

Erste Umsetzung der Ausbaustufe 1 für den Test auf HumHub Community Edition 1.18.5.

- Arbeitskreis als aktivierbares Space-Modul mit Navigationslink und Seitenleistenkarte.
- Kreisprofil, Zweck, Mandat, Oberkreis und Übersicht sichtbarer Kreise.
- Manuelle Besetzung von Kreisleitung, Delegiertenrolle, Moderation und Dokumentation.
- Rollenanzeige im Profil mit Berücksichtigung von Mitgliedschaft und Kreis-Sichtbarkeit.
- Backend für Kernkreis, zuständige Person, Trägerorganisation und dauerhafte Vereinbarungen.
- Konsent-Ablaufhilfe, SMART-Beschlussvorlage und Beschreibungen des Kreislebens.
- Initiale Datenbankmigration, atomare Profil-/Rollenspeicherung und Konflikterkennung.
- Ursprüngliches Deployment unter dem Website-Benutzer; durch die oben beschriebene Root-Variante ersetzt.

Geprüft: isolierte Komponententests mit Yii 2.0.55 und SQLite, PHP-Syntax,
Klassen-/Eventkompatibilität mit HumHub 1.18.5 und Rendering der fünf Ansichten.
Noch keine vollständige HumHub-/MySQL-Abnahme, kein Serverdeployment durchgeführt.

## 0.0.1

Modulregistrierung und initiale Konzeptdokumentation.
