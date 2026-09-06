# AGENTS.md – Sociocratic Governance

## Auftrag und aktueller Stand

Dieses Repository ist das eigenständige HumHub-Modul
`sociocratic-governance` (PHP/Yii2) für soziokratische Arbeitskreise.
Zielplattform ist **HumHub Community Edition 1.18.5**; Version und
Installationsstand vor Arbeiten gegen eine echte Instanz prüfen.

Die veröffentlichte Implementierung ist **0.1.0 / M1**: Kreisprofil,
Oberkreis, vier manuell gepflegte Rollen, Backend-Konfiguration,
Profilanzeige und eine einfache globale Kreisübersicht. Sie ist noch
nicht vollständig auf einer HumHub-/MySQL-Instanz abgenommen.

`ToDo.md` ist der Arbeitsauftrag für die nächste Weiterentwicklung. Es
beschreibt geplantes Verhalten, nicht den aktuellen Codezustand.

## Orientierung, ohne das ganze Repository zu lesen

1. Lies zuerst diese Datei, dann den passenden Abschnitt in `ToDo.md`.
2. Für fachliche Regeln und den verbindlichen Konzeptstand lies `README.md`.
   Die dort als festgelegt bezeichneten Regeln gehen älteren Entwürfen vor.
3. Für die fachliche Roadmap und Abnahmekriterien lies `docs/ROADMAP.md`.
   Für Architektur- und Sicherheitsregeln lies `docs/ARCHITECTURE.md`.
4. Vor Installation, Deployment oder Arbeiten an einer echten Instanz lies
   `docs/INSTALLATION.md` und `docs/DEPLOYMENT.md`. Diese Schritte nicht
   anhand von geratenen Serverpfaden oder Zugangsdaten ausführen.
5. Vor Annahmen zu selbstsein.events, HumHub, Infrastruktur oder
   Entwicklungsstand die externe Wissensbasis konsultieren, insbesondere
   deren `README.md`, `AGENTS.md` und `99-reference/current-state.md`.
   Die Wissensbasis nie ohne ausdrücklichen Auftrag ändern und keine Secrets
   in Repository, Tests oder Dokumentation ablegen.

Bei Konflikten gelten tatsächlicher Code bzw. Zielsystem vor Dokumentation;
den Widerspruch im Ergebnis nennen.

## Nächste Aufgaben aus `ToDo.md`

### Kreisprofil und Mandat

Die bisherigen Felder sind `purpose` und `mandate`. Das Mandat soll in
getrennte Angaben erweitert werden: Verantwortung, Befugnisse,
Grenzen, Budget, Wiederwahl (standardmäßig sechs Monate) und Review;
zusätzlich ist eine Mandatskurzform von höchstens 255 Zeichen nötig.

Bearbeitungsorte:

- Schema: neue, **versionierte** Migration unter `migrations/`.
- Datenmodell und Validierung: `models/Circle.php`, `models/CircleForm.php`.
- Persistenz, Transaktion und konkurrierende Änderungen: `services/CircleService.php`.
- Eingabe: `views/circle/edit.php`.
- Darstellung in Kreisprofil und Verzeichnis: `views/circle/index.php` und
  `views/directory/index.php`.

Bestehende Mandate nicht stillschweigend verlieren. Eingaben serverseitig
validieren, nur kontextgerecht escaped ausgeben und bei Änderungen die
optimistische `revision`-Prüfung erhalten.

### Aktivierung eines Arbeitskreises und Rechte

Bei Aktivierung soll der Space mit den in `ToDo.md` beschriebenen
Voreinstellungen eingerichtet werden: sichtbar/öffentlich für angemeldete
Personen, Beitritt per Einladung und Anfrage, öffentliche Standardinhalte
sowie die beschriebenen Rechte für Mitglieder und Nichtmitglieder.
Die Kreisleitung soll Besitzer*in werden.

Die Einstiegspunkte sind `Module.php` (Lebenszyklus eines Space-Moduls),
`services/Access.php` (Governance-Lese- und Schreibschutz) und
`services/CircleService.php` (Rollenänderungen). Vor der Implementierung
die APIs von HumHub 1.18.5 für Space-Sichtbarkeit, Mitgliedschaften und
Rechte gegen den tatsächlichen Core prüfen. Keine Core-Patches verwenden.

Space-Zugriff und fachliche Governance-Rechte bleiben getrennt. Jeder
schreibende Endpunkt braucht serverseitige Autorisierung, POST und CSRF.
Private, blockierte, archivierte oder deaktivierte Kreise dürfen weder im
Verzeichnis noch in Profilen Informationen preisgeben.

### Globale Kreisübersicht

Die Route besteht bereits in `controllers/DirectoryController.php` mit
`views/directory/index.php`; die aktuelle Ansicht ist eine Kartenliste.
Gefordert sind:

- Tabellenansicht ab dem konfigurierten Kernkreis, mit eingerückter
  Hierarchie, alphabetischer Sortierung gleicher Ebenen, Mandatskurzform
  und kreisrunden Rollenbildern;
- interaktive, verschiebbare und zoombare Bubble-Karte: ein Kreis je Bubble,
  klickbare Kreise, Titel und Mandatskurzform, Doppelbindung über zwei
  Profilbilder sowie überlappungsfreie, möglichst kurze Verbindungen;
- anfänglicher Fokus auf die Kreise, in denen die angemeldete Person eine
  Rolle hat;
- globaler Navigationseintrag nach der Modulaktivierung;
- Arbeitskreise nicht zusätzlich im allgemeinen HumHub-Space-Verzeichnis.

Bearbeitungsorte: Datenaufbereitung zunächst im `DirectoryController`
oder einem neuen, testbaren Service; Markup in `views/directory/index.php`;
Styles in `resources/governance.css`; zusätzliche Browser-Assets über
`assets/GovernanceAsset.php`; globale Navigation über `Events.php` und eine
gegen HumHub 1.18.5 geprüfte Eventregistrierung in `config.php`.
Die Sichtbarkeitsfilter aus `Access::visibleCircles()` sind bei jeder
Ansicht und bei API-/Asset-Datenlieferungen beizubehalten. Für die
Kartenanordnung einen deterministischen, testbaren Layout-Algorithmus
verwenden; keine privaten Kreise zur Layoutberechnung an den Browser geben.

### Editor und optionale Integrationen

Wo es sinnvoll ist, sollen Backend- und Frontend-Eingaben Markdown und Emoji
unterstützen. Rohes Markdown/HTML nie ungefiltert ausgeben: Renderer und
Sanitizing anhand der vorhandenen HumHub-1.18.5-Mechanismen prüfen.

Die Integration von *Virtual Card Popover* (mindestens 1.2.1) ist optional.
Fehlt das Modul, müssen Profilbilder und Governance weiter funktionieren.
Die später geforderten Werte `user.rolls`, `space.purpose` und
`space.mandate` nur über eine dokumentierte, versionsgeprüfte Schnittstelle
bereitstellen. Ein Installationshinweis bzw. Hilfsplugin gehört laut ToDo
erst in Ausbaustufe M3.

Die in `ToDo.md` genannten Community-Mediathek-, Share-Content- und
Wiki-Module sind als Pflichtumgebung bezeichnet; ihre konkrete Integration
ist noch nicht spezifiziert. Vor einer technischen Kopplung einen
abgestimmten Anwendungsfall und kompatible Modul-APIs feststellen. Das
HumHub-Aufgabenmodul ist ausgeschlossen, weil Governance später eigene
Aufgaben verwaltet.

## Codekarte

| Bereich | Dateien |
| --- | --- |
| Modul, Events und Routen | `Module.php`, `Events.php`, `config.php`, `controllers/` |
| Kreis- und Konfigurationsdaten | `models/`, `migrations/` |
| Geschäftsregeln und Zugriffe | `services/Access.php`, `services/CircleService.php`, `services/Rules.php` |
| Oberflächen | `views/`, `widgets/`, `resources/governance.css`, `assets/GovernanceAsset.php` |
| Isolierte Prüfungen | `tests/run.php`, `tests/compatibility.php`, `tests/render.php`, `tests/README.md` |
| Konzept, Installations- und Änderungsstand | `README.md`, `docs/`, `ToDo.md` |

## Verbindliche Entwicklungsregeln

- Keine HumHub-Core-Patches und keine fest eingebauten Personen,
  Organisationen, Domains, Serverpfade oder Konten.
- Lizenz aller neuen Quelldateien: `SPDX-License-Identifier: AGPL-3.0-only`.
  Keine Fremdcodeübernahme ohne Lizenz- und Kompatibilitätsprüfung.
- Datenbankschema ausschließlich über neue Migrationen ändern. Migrationen
  sind versioniert; Deaktivierung darf Governance-Daten nicht löschen.
- Alle Mutationen: Autorisierung auf dem Server, POST, CSRF, Validierung und
  bei mehrstufigen Änderungen Transaktionen. Berechtigungen dürfen nicht
  ausschließlich in Views oder JavaScript liegen.
- Kreiszyklen verhindern. Kreisleitung und Delegierte*r dürfen nie dieselbe
  Person sein. Rollen dürfen nur aktiven Kreismitgliedern zugeordnet werden.
- Rollen- oder Governance-Änderungen dürfen nicht unbemerkt allgemeine
  HumHub-Administrationsrechte oder Konsentrechte verleihen.
- Deutsch bleibt die aktuelle Oberfläche. Barrierefreiheit beachten:
  Text/Symbole zusätzlich zu Farben, Tastaturbedienung und sichere,
  übersichtliche Formulare.

## Prüfung und Dokumentation

Für jede funktionale Änderung passende Tests in `tests/` ergänzen oder
anpassen. Mindestens PHP-Syntax prüfen; bei verfügbaren Abhängigkeiten auch
die Komponententests, Kompatibilitätsprüfung gegen HumHub 1.18.5 und den
View-Renderer gemäß `tests/README.md` ausführen. Für Rechte-, Migration-,
Navigation- oder Layoutänderungen anschließend in einer echten
HumHub-/MySQL- oder MariaDB-Testinstanz prüfen.

Aktualisiere bei sichtbaren oder verhaltensrelevanten Änderungen `README.md`,
`docs/ROADMAP.md`, `docs/INSTALLATION.md` und/oder `docs/CHANGELOG.md` nur
dort, wo der neue tatsächliche Stand es erfordert. Plane M2/M3-Funktionen
nicht als bereits implementiert ein.
