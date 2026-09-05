# Architekturentwurf

## Integration
Eigenständiges HumHub-Modul ohne Änderungen am Core. Zunächst ein Kreis pro Space.
Vor Implementierung: tatsächliche HumHub-Version der Zielinstallation erfassen.
Die Metadaten nennen 1.18 als Entwicklungsbasis, nicht als getestete Kompatibilitätszusage.

## Geplante Entitäten
- Circle: Space, Zweck, Domäne, Elternkreis.
- CircleMembership: Nutzer, Kreis, gültiger Zeitraum und Entscheidungsberechtigung.
- Role / RoleAssignment: Mandat, Person, Zeitraum, Wahlreferenz.
- CircleLink: zwei Kreise, Leitungsmandat und Delegationsmandat.
- Proposal / ProposalRevision: unveränderliche Textversionen und Prozessstatus.
- ConsentRound / RoundParticipant / Response: Teilnehmerstand, Revision und Rückmeldung.
- Objection / Integration: Begründung, Bearbeitung und Bestätigung.
- Election / Nomination: Rollenprofil, Kandidatur, Begründung und Konsentrunde.
- Meeting / AgendaItem / ActionItem: Sitzung, Bezug und Umsetzungsverantwortung.
- Decision / Review: Beschlussversion, Gültigkeit, Kriterien und Nachverfolgung.
- AuditEvent: Akteur, Zeitpunkt, Ereignis und referenzierte Version.

## Rechte
Space-Zugriff ist Voraussetzung für jede Abfrage, Datei, Suche und Benachrichtigung.
Dazu kommen getrennte Rechte für Kreisverwaltung, Vorschläge, Moderation und Konsent.
Jede Mutation prüft serverseitig Mitgliedschaft, Mandat und Prozesszustand.
Administrationsrechte erteilen keinen fachlichen Konsent.

## Konsistenz
Beschlussabschluss läuft in einer Datenbanktransaktion mit Sperre der Runde.
Eindeutigkeit einer Rückmeldung pro Person und Runde wird in der Datenbank erzwungen.
Konkurrierende Änderungen und Wiederholungen dürfen keine doppelten Beschlüsse erzeugen.
Versionswechsel invalidiert keinen Verlauf, verlangt aber eine neue Konsentrunde.
Alle Schreibaktionen verwenden POST und CSRF-Prüfung; Ausgabe wird kontextgerecht escaped.
Keine dynamische Ausführung von Vorschlagstexten oder unbereinigtem HTML.

## Betrieb
Versionierte Migrationen; Backup vor Updates. Deaktivierung löscht keine Governance-Daten.
Endgültige Löschung und Aufbewahrungsfristen werden separat spezifiziert.
Exporte enthalten nur Inhalte, die die anfragende Person lesen darf.
Auditdaten werden zugriffsgeschützt und datensparsam gespeichert; sie sind ohne weitere
technische Maßnahmen nicht als manipulationssicher zu bezeichnen.
