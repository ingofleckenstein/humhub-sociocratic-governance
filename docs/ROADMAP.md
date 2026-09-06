# Roadmap

Version 0.2.0 erweitert den Kern von Ausbaustufe 1 um strukturierte Mandate und die globale Kreisorientierung. Die Abnahme auf HumHub 1.18.5 steht noch aus. M2/M3/M4 sind technische Pakete der zweiten Ausbaustufe; KI bildet eine spätere dritte Ausbaustufe.

## M1 — Orientierung und manuelle Kreisverwaltung (Ausbaustufe 1)
- [x] Space-Modul, Navigation, Kreisprofil und initiale Migration.
- [x] Lesen entsprechend Space-Sichtbarkeit, Schreiben nur als Kreismitglied; keine Gäste.
- [x] Zweck, Mandat, Oberkreis und vier manuell zugeordnete Rollen.
- [x] Profilanzeige, Kernkreis und Backend für dauerhafte Mitgliedschaften als dokumentierte Vereinbarungen.
- [x] Grafische Konsent-Anleitung, SMART und Beschlussvorlage.
- [x] Komponententests, PHP-Syntax und Klassenkompatibilitätsprüfung.
- [x] Strukturierte Mandatsfelder, Mandatskurzform sowie Markdown- und Emoji-Eingaben.
- [x] Aktivierungsvoreinstellungen für sichtbare Arbeitskreise, Beitritt per Einladung/Anfrage und öffentliche Standardinhalte.
- [x] Globale Kreisübersicht: hierarchische Tabelle und zoombare Kartenansicht; Arbeitskreise aus dem allgemeinen Space-Verzeichnis ausblenden.
- [ ] Installation und Migration auf HumHub 1.18.5/MySQL oder MariaDB abnehmen.
- [ ] End-to-End-Prüfung mit Mitglied, Nichtmitglied und Admin im echten HumHub-Layout.

Keine automatischen Wahlen, Mitgliedschaftsübernahmen oder Krisenaktionen in M1.

## M2 — Vorhaben: Ideen, Mandatsprüfung, Aufgaben, Konsent und Beschlüsse
M2 gehört zur Ausbaustufe 2 (gezielte Werkzeuge), nicht zur KI-Integration. Der gesamte Weg von der Idee bis zum Beschluss ist Bestandteil dieses Meilensteins.

- [ ] Zentrales Vorhabensystem als eigene Domäne: Idee → Aufgabe → Konsent → Umsetzung → Review. Beschluss und Umsetzung bleiben getrennt.
- [ ] Kreisgründung als Sonderfall: Idee → Aufgabe → Gründungsvorschlag → Konsent / Gründungsbeschluss → Kreis mit Mandat.
- [ ] Ideenstatus: Alle angemeldeten Menschen der Organisation können Ideen erstellen und einem beliebigen Kreis zuordnen, auch ohne dortige Mitgliedschaft. Kein Zugriff auf interne Kreisinhalte durch Einreichung.
- [ ] Mandatsprüfung ausschließlich auf Zuständigkeit; keine vorgelagerte Bewertung der Sinnhaftigkeit.
- [ ] Innerhalb des Mandats: Idee nachvollziehbar in eine eigenständige Aufgabe umwandeln.
- [ ] Außerhalb des Mandats: Weiterleitung an den nächsthöheren Kreis mit Mandatsbegründung; dort erneute Prüfung, gegebenenfalls bis zum obersten Kreis.
- [ ] Passende Verteilung durch höhere Kreise unter Wahrung bereits delegierter Mandate.
- [ ] Urheberschaft, ursprüngliche Idee, aktueller zuständiger Kreis und vollständiger Weiterleitungsverlauf bleiben erhalten.
- [ ] Aufgaben als Arbeitsaufträge für kommende Sitzungen zuordnen; Beratungsstand sitzungsübergreifend bis zum Konsent fortführen. Diese einfache Sitzungszuordnung ist bereits M2.
- [ ] Sichtbarer Verlauf: Idee → Mandatsprüfung → gegebenenfalls Weiterleitung und erneute Prüfung → Aufgabe → Sitzungsbearbeitung mit Verfahrensphase → Konsent / Beschluss.
- [ ] Durchgehende Änderungshistorie von Idee über Aufgabe bis Konsent: jede Bearbeitung mit Datum/Uhrzeit, damaligem Space/Kreis, Autor*in und nachvollziehbarer Änderung (vorher/nachher). Auch kleine Änderungen, Statuswechsel, Weiterleitungen, Termine, Verantwortliche, Mandatsbezug und Einwandbearbeitung erfassen; Systemaktionen kennzeichnen.
- [ ] Historie bleibt bei Umwandlung und Kreiswechsel verbunden; frühere Einträge werden durch spätere Bearbeitung nicht überschrieben. Zugriff auf Historie folgt Inhaltsrechten.
- [ ] Vorschläge mit unveränderlichen Revisionen.
- [ ] Runden, Teilnehmerstand, Rückmeldungen und Einwandintegration.
- [ ] Atomarer Beschlussabschluss, Beschlussregister und Reviews.
Abnahme: Fehlende Rückmeldung und offener Einwand verhindern Abschluss.
Textänderung erfordert neue Runde. Parallelzugriffe erzeugen nur einen Beschluss.
Unberechtigte Personen können keine geschützten Inhalte lesen oder Konsententscheidungen treffen; das organisationsweite Einreichungsrecht bleibt davon getrennt.
Zusätzliche Abnahme: Ein Nichtmitglied reicht eine Idee bei einem Kreis ein. Eine negative Mandatsprüfung leitet sie mit Historie an den Oberkreis weiter, ohne Sinnhaftigkeitsbewertung. Mehrstufige Eskalation und passende Verteilung sind nachvollziehbar. Bei positiver Prüfung entsteht genau eine verknüpfte Aufgabe. Sie bleibt über mehrere Sitzungen mit offenen Einwänden in Bearbeitung, bis Konsent erreicht wird. Beschlussstatus und Umsetzungsstatus bleiben getrennt.
Zusätzliche Abnahme der Historie: Idee erstellen, Text ändern, weiterleiten, in Aufgabe umwandeln, Termin und Verantwortliche ändern und Konsent dokumentieren. Jeder Schritt zeigt korrekten Zeitpunkt, damaligen Kreis, handelnde Person und Änderung; alte Stände bleiben erhalten und für Unberechtigte geschützt.
Vor Implementierung zu klären: Zuständigkeit für die Mandatsprüfung, Schutz bei widersprüchlichen Prüfungen beziehungsweise Weiterleitungsschleifen und Behandlung einer Idee außerhalb des Gesamtmandats am obersten Kreis.

## M3 — Wahlen und Sitzungen
- [ ] Offene Rollenwahl einschließlich Zustimmung der kandidierenden Person.
- [ ] Ausbau der bereits in M2 vorhandenen Aufgaben- und Sitzungszuordnung um vollständige Agenda, Moderationsrunden und Protokoll.
Abnahme: Vollständige Wahl vom Rollenprofil bis zum befristeten Mandat;
Rückzug und gescheiterter Konsent; Protokoll mit Versionsbezug.

## M4 — Alltag und Pilot
- [ ] Benachrichtigungen ohne Informationsleck und Review-Erinnerungen.
- [ ] Suche, Filter und berechtigungsgeschützte Exporte.
- [ ] Deutsche und englische Oberfläche, Tastaturbedienung und barrierearme Formulare.
- [ ] Installation, Updates, Backup/Restore und dokumentierte Versionsmatrix.
- [x] GNU AGPL Version 3 (AGPL-3.0-only) festlegen und vollständigen Lizenztext hinzufügen.
- [ ] Lizenzbedingungen integrierter Drittkomponenten vor Veröffentlichung prüfen.
Abnahme: Pilot mit mindestens zwei Kreisen, dokumentierte Rückmeldungen,
bestandene Integrations- und Berechtigungstests vor Version 0.1.0.

## Noch gemeinsam festzulegen
- HumHub-Version und Testinstanz.
- Kompatibilität der konkret integrierten Drittkomponenten mit AGPL-3.0-only.
- Pilotgemeinschaft und konkreter erster Entscheidungsablauf.
- Aufbewahrung, Export und Löschung von Governance-Daten.
