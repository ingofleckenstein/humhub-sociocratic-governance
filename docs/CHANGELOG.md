# Änderungen

## 0.2.0 – 2026-09-06

- Mandat in Verantwortung, Befugnisse, Grenzen, Budget, Wiederwahl und Review
  gegliedert; Mandatskurzform mit maximal 255 Zeichen ergänzt.
- Markdown- und Emoji-Editoren für geeignete Mandats- und Zweckfelder ergänzt;
  die Ausgabe verwendet HumHubs sichere Rich-Text-Ausgabe.
- Aktivierung eines Arbeitskreises setzt die vereinbarten Space-Voreinstellungen
  und das Schreibrecht für öffentliche Inhalte von Mitgliedern.
- Kreisleitung wird beim Speichern als Space-Besitzer*in gesetzt; die Übertragung
  ist auf Space-Besitzer*innen und -Administrator*innen beschränkt.
- Globale Navigation, hierarchische Tabellenansicht und zoombare Bubble-Karte
  der sichtbaren Kreisstruktur ergänzt; Arbeitskreise werden aus dem allgemeinen
  Space-Verzeichnis ausgeblendet.
- Migration, Komponententests und Rendering-Vorschau für die neuen Felder und die
  Kreisübersicht erweitert.

## Generisches Deployment – 2026-09-06

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
