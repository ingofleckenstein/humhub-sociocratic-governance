# Änderungen

## 0.6.1 – 2026-09-11

### Ein persönlicher Einstieg

- Der frühere Dashboard- beziehungsweise HOME-Aufruf leitet angemeldete
  Personen jetzt unmittelbar auf „Start“. Dadurch gibt es keinen zweiten,
  konkurrierenden persönlichen Einstieg.

## 0.6.0 – 2026-09-11

### Persönlicher Start, Navigation und globale Governance-Suche

- „Start“ zeigt ungelesene persönliche Hinweise vor Aufgaben und Zusagen. Der
  Stream beschreibt nun korrekt die zuletzt sichtbaren Aktivitäten, ohne einen
  nicht vorhandenen Besuchszeitpunkt zu behaupten.
- „Mitwirken“ kann zusätzlich nach einem Kreis gefiltert werden. Der gewählte
  Kreis bleibt beim Öffnen eines Vorhabens und beim Zurückgehen erhalten.
- Das Kreisverzeichnis hat Suche, Kreisart- und „Nur meine Kreise“-Filter.
- Die Space-Navigation trennt jetzt sichtbar „MEINE SPACES“ und „SPACES
  ENTDECKEN“. Kreisleitungen und andere Rollen sehen ihre Rolle im Kreisprofil;
  Verwaltungsaktionen liegen dort gebündelt unter „Kreis verwalten“.
- Der neue Einstieg „Start“ ersetzt in der Hauptnavigation die reine
  Stream-Übersicht. Er priorisiert offene Aufgaben und aktive Zusagen; ohne
  diese persönlichen Anknüpfungspunkte führt er direkt zu Mitwirken sowie zum
  Entdecken von Kreisen und Spaces. Die getrennten, meist stabilen
  Zugehörigkeiten bleiben aufklappbar erreichbar. Der bisherige Stream bleibt
  dort als „Aktivität“ erreichbar.
- Die Themenwahl in „Mitwirken“ ist ein kompaktes Aufklappmenü. Die sichtbaren
  Ideen und Aufgaben beginnen damit ohne vorheriges Scrollen.
- „Letzte Aktivitäten“ beginnt nach den eigenen Zugehörigkeiten am Ende von
  „Start“ und verwendet den regulären, sichtbarkeitsgeprüften HumHub-Stream.
- Vorhabensdetails führen nun kontextabhängig zurück zu „Start“, „Mitwirken“
  oder zum zugehörigen Board. Die Bereichswechsel im Vorhaben behalten diesen
  Rückweg bei; die Kreis-Arbeitshilfe verwendet dieselbe sichtbare Rückwegform.
- Rückwege stehen jetzt einheitlich oben rechts: Kreisprofil führt zur
  Kreisübersicht, Board zum Kreis beziehungsweise Space, neue Vorhaben zum
  Board und Mitwirken, Kreisübersicht sowie Suche zu Start.
- Die globale HumHub-Suche enthält nun sichtbare Projekt- und Kompetenzkreise,
  Ideen/Aufgaben und Ressourcenbedarfe. Ressourcen werden über ihre Bezeichnung
  gefunden; vertrauliche Detailangaben werden nicht durchsucht.

## 0.5.11 – 2026-09-10

### Klarer Zugang und Rückmeldung bei Kreisveröffentlichungen

- Der Space-Menüpunkt „Projektkreis“ und die Kreisansicht bleiben bei
  Kompetenzkreisen immer verfügbar, sobald die Person den Space sehen darf.
- Ein nicht erfülltes Veröffentlichungs-Kriterium wird direkt in der
  Kreisansicht als rote, verständliche Fehlermeldung angezeigt.
- Der Veröffentlichungsbutton sendet ohne JavaScript-Zwischenschritt direkt
  an die Veröffentlichungsaktion.

## 0.5.10 – 2026-09-10

### Kreisveröffentlichung und dauerhafte Mitgliedschaften

- Neue Arbeitskreise beginnen als private Entwürfe; ihre Governance-Ansichten
  sind bis zur Veröffentlichung nur für Space-Administrator*innen zugänglich.
- Neben „Kreisprofil pflegen“ können Space-Administrator*innen einen vollständig
  beschriebenen Kreis einmalig veröffentlichen. Dabei werden Sichtbarkeit,
  Beitrittsmodus und Standard-Inhaltssichtbarkeit gesetzt.
- Die Veröffentlichung erzeugt atomar eine Streamnachricht des im Backend
  gewählten Kommunikationskontos: Mandatskurzform, Link „Kreis ansehen“ sowie
  Hinweise zu Beitritt, Austritt und Folgen.
- Dokumentierte dauerhafte Mitgliedschaften stehen jetzt im jeweiligen Kreis
  unter „Rollen“ direkt bei der Person beziehungsweise als eigenes dauerhaftes
  Kreismitglied.
- Sind alle Akzentfarben vergeben, werden sie für weitere Kreise kontrolliert
  wiederverwendet, statt die Kreisgründung zu blockieren.

## 0.5.9 – 2026-09-09

### Abnahmeprüfung und klare Rückmeldungen

- Das Begründungsfeld ist bei Annahme, Delegation, Rückgabe und Ergebnisvorlage
  immer sichtbar; nur die Auswahl des Zielkreises wird bei einer Delegation
  eingeblendet.
- Benachrichtigungen speichern den konkreten Schritt dauerhaft. Neue Vorschläge
  erscheinen dadurch als „hat eine neue Idee eingereicht“ statt nur als
  unspezifische Aktualisierung.
- Nach einer eigenen Abnahme verschwindet deren Schaltfläche. Erst die andere
  erforderliche Rolle kann die zweite Bestätigung abgeben.
- Beim Übergang aus einem geschützten Kreis wird dessen Name nicht mehr über
  die Detailansicht des Übergabeereignisses preisgegeben.

## 0.5.8 – 2026-09-09

### Sichere Übergaben und klare Zuständigkeit

- Kreisleitung und Delegierte*r entscheiden einheitlich über jede Delegation.
  Normale Mitglieder sehen stattdessen ihren sinnvollen nächsten Beitrag.
- Eine Delegation überträgt den aktuellen Stand in den Zielkreis und startet
  dort einen neuen gemeinsamen Streambeitrag. Geschützte frühere Diskussionen,
  Ereignisse und Vorschlagsfassungen bleiben im Ursprungskreis verborgen.
- Einzelansichten verwenden jetzt „Vorschlag“ beziehungsweise „Aufgabe“ und
  verständliche Bearbeitungsstände statt der pluralen Board-Spaltenüberschrift.

## 0.5.7 – 2026-09-09

### Zuständigkeit beim nächsten Schritt

- Für Vorschläge können die Kreisleitung und die delegierte Person annehmen
  oder an einen direkt benachbarten Kreis delegieren. Alle anderen sehen eine
  verständliche Erklärung ihres nächsten möglichen Beitrags statt einer
  nicht ausführbaren Entscheidungsauswahl.

## 0.5.6 – 2026-09-09

### Verlinkte Streambeiträge

- Streambeiträge zu Vorhaben verwenden nun HumHubs Markdown-Links statt
  sichtbar ausgegebenem HTML. Bereits erzeugte Systembeiträge werden bei der
  Aktualisierung ebenfalls repariert.

## 0.5.5 – 2026-09-09

### Gemeinsame Diskussion und kompakte Aktionen

- Jedes neue Vorhaben erhält einen eigenen Streambeitrag. Seine HumHub-
  Kommentarleiste ist zugleich die Diskussion im Vorhaben – mit Markdown,
  Antworten, Erwähnungen und Benachrichtigungen, ohne doppelte Kommentare.
- Bereits bestehende Vorhaben können die gemeinsame Diskussion gezielt im
  Vorhaben anlegen. So werden alte Streams nicht ungefragt mit Beiträgen gefüllt.
- Die Karten für „Nächster Schritt“ und „… präzisieren“ sind einklappbar.
  Bei einer offenen Aufgabe ist „Nächster Schritt“ zunächst geschlossen.
- Unter „Worum geht es?“ zeigt eine kompakte Ressourcenübersicht jeden Bedarf
  und den Anteil der vollständig gedeckten Ressourcen in Prozent.

## 0.5.4 – 2026-09-09

### Ressourcen verständlicher erfassen

- Die Form der Mitwirkung und der zeitliche Rahmen sind bei Arbeitszeit und
  Expertise verpflichtend. Bei „Anderes“ können sie optional ergänzt werden;
  bei Geld und Material bleiben sie ausgeblendet.
- Jede Ressource braucht nun eine Einheit. Ist keine passend, wird ausdrücklich
  „o. E.“ (ohne Einheit) eingetragen.

## 0.5.3 – 2026-09-09

### Vorschläge, Delegation und Kreis-Karte

- Jede neue Idee beginnt als Vorschlagsversion 1. Jede inhaltliche
  Präzisierung erstellt eine unveränderliche nächste Version mit Titel,
  Beschreibung und Themen; alle Fassungen sind im Verlauf direkt lesbar.
- Ideen können durch Kreisleitung oder Delegierte*n nun ebenso an einen
  direkten Unterkreis wie an den Oberkreis delegiert werden. Die Zielauswahl
  erscheint nur, wenn „An einen anderen Kreis delegieren“ gewählt ist.
- Die Schaltflächen „Infos“, „Mitglieder“ und „Vorhaben“ auf der Kreis-Karte
  führen zu den jeweiligen Seiten, statt Informationen innerhalb der Karte
  aufzuklappen.

## 0.5.2 – 2026-09-09

### Entscheidungen und Rollen verständlich bündeln

- „Nächster Schritt“ befindet sich unter „Mitmachen & umsetzen“. Die Annahme
  im Mandat und die Delegation sind dort eine gemeinsame Auswahl; Delegationen
  bleiben auf den direkt höheren oder niedrigeren Kreis beschränkt.
- „Inhalt bearbeiten“ heißt nun kontextgerecht „Aufgabe präzisieren“ oder
  „Vorschlag präzisieren“.
- Die Profilkategorie heißt „Rollen & Zuständigkeiten“. Ein virtuelles,
  schreibgeschütztes Profilfeld zeigt darin alle sichtbaren Projekt- und
  Kompetenzkreise einer Person sowie die dort ausgeübten Rollen.

## 0.5.1 – 2026-09-09

### Verständlichere Vorhabenkommunikation

- Neue Ideen werden zusätzlich im Stream des zuständigen Kreises angekündigt.
- Die Meldung über vollständig gedeckte Ressourcen und die Ideenmeldung verlinken
  direkt auf das jeweilige Vorhaben.
- Das im Backend wählbare Konto heißt nun „Unternehmenskonto für allgemeine
  Kommunikation“ und veröffentlicht beide Arten von Streammeldungen.
- Einreichende erhalten bei späteren Änderungen weiterhin zugriffsberechtigte
  In-App-Benachrichtigungen. Die Detailansicht fasst Einreichung, Kreis und
  zuständige Person zusammen, gruppiert alle Mitwirkungsaktionen an einer Stelle
  und zeigt den Verlauf ohne technische Kennungen.

### Profilangaben zum soziokratischen Modell

- Die Migration `m260909_170000_sociocratic_profile_fields` ergänzt im HumHub-
  Profilattribute-Management die Kategorie „Soziokratisches Modell“ und das
  Auswahlfeld „Form der Mitarbeit“ mit hauptamtlich, hauptamtlich
  (unentgeltlich) und ehrenamtlich.

## 0.5.0 – 2026-09-08


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

### Community-Theme selbstsein.events – 2026-09-08

- Neues, vom HumHub-Standardtheme abgeleitetes Community-Theme mit warmer,
  ruhiger Grundfläche sowie Grün für Orientierung, Koralle für Begegnung und
  Gold für Aufmerksamkeit.
- Navigation, Karten, Stream, Formulare, Buttons, Tabellen und Fokuszustände
  erhalten ein gemeinsames, barrierebewusstes Erscheinungsbild ohne Core-Patch.

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
