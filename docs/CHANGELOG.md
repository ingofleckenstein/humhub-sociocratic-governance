# Änderungen

## 0.1.0 — 2026-09-06

Erste Umsetzung der Ausbaustufe 1 für den Test auf HumHub Community Edition 1.18.5.

- Arbeitskreis als aktivierbares Space-Modul mit Navigationslink und Seitenleistenkarte.
- Kreisprofil, Zweck, Mandat, Oberkreis und Übersicht sichtbarer Kreise.
- Manuelle Besetzung von Kreisleitung, Delegiertenrolle, Moderation und Dokumentation.
- Rollenanzeige im Profil mit Berücksichtigung von Mitgliedschaft und Kreis-Sichtbarkeit.
- Backend für Kernkreis, zuständige Person, Trägerorganisation und dauerhafte Vereinbarungen.
- Konsent-Ablaufhilfe, SMART-Beschlussvorlage und Beschreibungen des Kreislebens.
- Initiale Datenbankmigration, atomare Profil-/Rollenspeicherung und Konflikterkennung.
- Deployment ausschließlich unter sexpositiv.events_0chzqp83gyz5.

Geprüft: isolierte Komponententests mit Yii 2.0.55 und SQLite, PHP-Syntax,
Klassen-/Eventkompatibilität mit HumHub 1.18.5 und Rendering der fünf Ansichten.
Noch keine vollständige HumHub-/MySQL-Abnahme, kein Serverdeployment durchgeführt.

## 0.0.1

Modulregistrierung und initiale Konzeptdokumentation.
