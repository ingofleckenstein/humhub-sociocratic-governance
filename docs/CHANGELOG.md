# Änderungen

## 0.4.1 – 2026-09-08


### Ressourcen als Startvoraussetzung – 2026-09-08

- Aufgaben mit definierten Ressourcen können erst in „In Bearbeitung“ wechseln,
  wenn jede Ressource eine Menge hat und vollständig zugesagt ist.
- Sobald die letzte Ressource eines Vorhabens gedeckt ist, erscheint einmalig
  eine Feiermeldung im Stream des zugehörigen Kreises.
- Die versionierte Migration `m260908_100000_resource_coverage_celebration`
  hält den Zeitpunkt fest, damit wiederholte Änderungen keine doppelten
  Feiermeldungen erzeugen.

### Optionales Unternehmenskonto für Feiermeldungen – 2026-09-08

- Das Backend kann ein aktives Unternehmenskonto wählen. Es veröffentlicht
  Feiermeldungen im Kreis-Stream; ohne Auswahl bleibt die auslösende Person
  die Autorin beziehungsweise der Autor.
- Die versionierte Migration `m260908_110000_company_account` speichert die
  optionale Zuordnung und entfernt sie automatisch, falls das Konto gelöscht wird.

### Mitwirkungs-Dashboard und zuverlässige Ideenhinweise – 2026-09-07

- Neues Dashboard „Wie kann ich mich einbringen?“ bündelt alle sichtbaren Ideen
  und Aufgaben, wahlweise nach Themen oder offenem Ressourcenbedarf.
- Themen sind am Vorhaben pflegbar und damit übergreifend nachvollziehbar.
  Aufgaben erfassen Commons-Ressourcen (Geld, Zeit, Expertise, Material oder
  andere Beiträge) mit Bedarf, Einheit und zugesagtem Stand.
- Neue Idee-Hinweise werden nach dem Commit unmittelbar im HumHub-
  Benachrichtigungsdienst angelegt, statt auf einen späteren Queue-Lauf zu warten.
- Versionierte Migration `m260907_153000_participation_dashboard` ergänzt die
  Tabellen für Themen und Ressourcen ohne vorhandene Vorhaben zu verändern.

### Vorhaben-Board und VCard-Korrektur – 2026-09-07

- Erstes Kanban-Board: Idee, offene Aufgabe, Bearbeitung, Abnahme und Ablehnung.
  Ideen durch Communitymitglieder mit Lesezugang; Annahme durch Kreisleitung oder
  Delegierte*n. Selbstzuweisung, Kommentare und nachvollziehbare Textänderungen.
- Delegation nur an direkte Nachbarkreise; frühere private Historie bleibt geschützt.
- Ergebnisabnahme mit beiden aktiven Verbindungsrollen und Rückgabe zur Nacharbeit.
  Keine Gleichsetzung von Abnahme mit Konsent. Konsent-/Sitzungswerkzeuge bleiben offen.
- Neue Migration für Vorhaben und Historie; atomare Änderungen mit Revision und
  MySQL-Sperre. Bestehende Historie verhindert endgültige Löschung beteiligter Spaces.
- Doppelte VCard-Angaben feldweise unterdrückt; Addon ergänzt nur fehlende Werte.
- Gestaltungsvorschlag für gruppierte Kreisvisualisierung dokumentiert.

### Bisherige Korrekturen

- Kreis- und Profil-Links getrennt; alle aktiven sichtbaren Kreismitglieder auf
  einem inneren Ring, Doppelrollen an einem Bild. Kreisgröße wächst mit der
  Mitgliederzahl; Kurzmandat bei Hover/Fokus.
- VCard-Template-Adapter für 1.2.x ab 1.2.1: `user.rolls`, `space.purpose` und
  `space.mandate`, einschließlich einfacher Klammern, mit Sandbox und Sanitizing.
  Ereignisregistrierung unabhängig von der Ladereihenfolge des optionalen Moduls.
- VCard-Mandat greift bei fehlenden strukturierten Angaben auf Altmandat zurück.
- Archivierte Kreise aus Governance-Leseansichten ausgeschlossen.
- Pflichtmodule bei neuer Kreisaktivierung einrichten; POST/CSRF-geschützte
  Nachholeinrichtung im Backend für bestehende Kreise, mit Versions-/Verfügbarkeitsprüfung.
- Vorhaben-Plan in `VORHABEN-PLAN.md` dokumentiert; M2 bleibt Planung.
- Lokal 41 Komponentenprüfungen, 11 VCard-Prüfungen, fünf gerenderte Ansichten
  und Kompatibilitätsprüfung gegen HumHub 1.18.5/Popover VCard 1.2.1 bestanden.
  HTTP-, Theme- und MySQL-Abnahme stehen aus.

## 0.2.1 – 2026-09-06

- Die Kreisübersicht rendert die sichtbaren Kreise nun als lokale,
  datengetriebene JavaScript-Karte. Karten lassen sich verschieben und zoomen;
  Details, Rollen und Mitglieder werden auf Wunsch direkt an der jeweiligen
  Karte eingeblendet. Die Doppelbindung bleibt als Profilbild-Verbindung
  sichtbar.
- Bubble-Karte lesbar skaliert und Kreisleitung sowie Delegierte*r klar
  gekennzeichnet.
- Optionales Virtual-Card-Popover-Addon für Version 1.2.1+ ergänzt. Es zeigt
  sichtbare Kreisrollen in der Reihenfolge zum Kernkreis sowie Zweck und Mandat
  von Arbeitskreisen, ohne das VCard-Plugin zu verändern.

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
