# Vorhaben: Arbeitsstand und nächster Ausbau

Stand: 2026-09-07. Der erste Board-Ablauf ist lokal implementiert; die Abnahme
auf HumHub/MySQL und im tatsächlichen Theme steht aus. M2 ist damit nicht vollständig.

## Implementierter Ablauf

**Idee → Offen → In Bearbeitung → Zur Abnahme → Abgenommen**

- Aktive Communitymitglieder dürfen Ideen in lesbaren Kreisen einreichen und
  kommentieren. Eine Mitgliedschaft im adressierten Kreis ist nicht erforderlich.
- Kreisleitung oder Delegierte*r nehmen Ideen mit dokumentiertem Mandatsbezug
  als Aufgabe an. Nummer, Urheberschaft, ursprüngliche Idee und Verlauf bleiben
  durchgängig erhalten. Der Ideenstatus ist keine Sinnhaftigkeitsabstimmung.
- Eine der beiden Rollen kann einen Vorschlag an den direkten Oberkreis oder
  einen direkten Unterkreis weiterleiten, wenn dort das passende Mandat liegt.
  Am obersten Kreis ist ein begründeter Abschluss außerhalb des Gesamtmandats
  möglich. Eine Weiterleitung ist keine inhaltliche Ablehnung.
- Mitglieder dürfen Aufgaben direkt im bestehenden Mandat erstellen und offene
  Aufgaben über „Mir zuweisen“ übernehmen. Bereits übernommene Aufgaben können
  nicht von anderen Mitgliedern über diese Aktion umverteilt werden.
- Die verantwortliche Person startet die Bearbeitung und legt ihr Ergebnis zur
  Abnahme vor. Mitglieder können offene/in Bearbeitung befindliche Aufgaben
  begründet ablehnen; fertige Arbeit bleibt bis zur Abnahme getrennt erkennbar.
- Kreisleitung und Delegierte*r bestätigen jeweils. **Derzeit sind beide
  Bestätigungen erforderlich** (aus dem Auftrag abgeleitete Voreinstellung).
  Rollenwechsel entwerten eine noch offene Bestätigung der früheren Rollenperson.
  Zurückgeben zur Nacharbeit setzt die Bestätigungen zurück.
- Kreisleitung oder Delegierte*r können Vorschläge und Aufgaben ausschließlich
  an den direkten Oberkreis oder direkte Unterkreise delegieren. Im Zielkreis
  beginnen Aufgaben offen und ohne Zuweisung; gleichzeitig startet dort eine
  neue gemeinsame Diskussion. Andere Kreise und übersprungene Hierarchieebenen
  sind ausgeschlossen.
- Titel, Beschreibung, Kommentare und alle Prozessschritte erscheinen im Verlauf
  mit Zeitpunkt, Akteur, damaligem Kreis und vorherigem/neuem Stand. Jede
  inhaltliche Präzisierung einer Idee erzeugt zusätzlich eine unveränderliche
  Vorschlagsversion mit Titel, Beschreibung und Themen.
- Themen können beim Erstellen und Bearbeiten vergeben werden. Das globale
  Mitwirkungs-Dashboard fasst alle zugriffsberechtigten Ideen und Aufgaben nach
  Themen zusammen.
- Aufgaben haben ein Ressourcenfenster für Geld, Arbeitszeit, Expertise, Material
  und andere Beiträge. Nur Mitglieder des zuständigen Arbeitskreises schätzen und
  ändern den Bedarf. Andere zugriffsberechtigte Personen geben persönliche Zusagen
  ab; sichtbar sind nur die Mitwirkenden sowie der aggregierte Stand, niemals Art
  oder Höhe der einzelnen Zusage. Zeit wird als asynchron leistbare Stunden oder
  als termingebundene Zeit mit Rhythmus beschrieben. Das Dashboard kann darauf
  fokussieren. Sobald alles gedeckt ist, verlinkt die Streammeldung direkt auf das
  betreffende Vorhaben.
- Neue Ideen werden nach dem erfolgreichen Speichern sofort als reine In-App-
  Benachrichtigung an Kreisleitung und Delegierte*n zugestellt. Der zugehörige
  Streambeitrag ist zugleich die gemeinsame Diskussion: Kommentare, Antworten,
  Erwähnungen und Markdown erscheinen identisch im Stream und im Vorhaben.
- Die einreichende Person erhält bei jeder späteren, weiterhin zugriffsberechtigten
  Änderung eine In-App-Benachrichtigung. Die Detailansicht fasst Einreichung,
  zuständigen Kreis und verantwortliche Person zusammen und trennt verständlich
  zwischen „Worum geht es?“, „Mitmachen & umsetzen“ und „Bisheriger Weg“. Die
  Entscheidung „im Mandat annehmen“ und die Delegation an einen direkt höheren
  oder niedrigeren Kreis sind gemeinsam unter „Nächster Schritt“ zu finden.

## Rechte und technische Grenzen

Das Board ist über die Space-Navigation „Vorhaben“ und das Kreisprofil erreichbar.
Statuswechsel erfolgen über beschriftete Formulare; Drag-and-drop ist nicht
implementiert. Markdown in Beschreibung und Kommentaren wird mit HumHubs
Rich-Text-Ausgabe dargestellt; im isolierten Renderer wird escaped ausgegeben.

Eine Delegation überträgt den aktuellen Stand ausdrücklich in den Zielkreis.
Frühere Streamdiskussionen, Ereignisse und Vorschlagsfassungen aus geschützten
Kreisen bleiben für Personen ohne deren Zugriffsrecht verborgen. Dadurch ist die
Übergabe arbeitsfähig, ohne alte private Beratungen offenzulegen.

Alle Mutationen laufen über POST/CSRF und serverseitige Rechteprüfung. Status und
Revision werden in einer Transaktion geprüft, der Vorhabensatz auf MySQL vor
Änderung gesperrt und Historie zusammen mit der Änderung gespeichert. Datenbank-
Fremdschlüssel verhindern das Löschen beteiligter Spaces oder Vorhaben mitsamt
Historie. Deaktivierung löscht nichts. Umgesetzte und abgelehnte Aufgaben können
manuell archiviert werden und werden optional nach einer im Backend einstellbaren
Frist archiviert (0 bedeutet nie); Nutzende können Vorhaben nicht löschen.

## Noch offen innerhalb von M2

- Konsentrunden und Abstimmungen: Teilnehmerstand, unveränderliche
  Vorschlagsversionen, Verständnisfragen, Reaktionen, Einwände und Integration.
  Keine Gleichsetzung mit Aufgabenabnahme; kein automatischer Konsent bei Fristablauf.
- Einfache Sitzungszuordnung und Fortführung offener Beratungen in nächste Sitzungen.
- Verknüpfte Beschlüsse und Mandatsprüfung durch den Oberkreis, Beschlussregister,
  Reviewtermine und Kreisgründung als Verfahrensvariante.
- Auflösung widersprüchlicher Zuständigkeitsprüfungen/Weiterleitungsschleifen.
- Gezielte Rückmeldungen und Freigaben für Einreichende bei geschützten Kreisen.
- Wiederzuweisung bei Ausscheiden Verantwortlicher und weitergehende Filter.

Die fachliche Grundlage für Konsent bleibt das README. Teilnehmerregeln bei
Mitgliedschaftswechsel und Verfahrensvarianten müssen vor dessen Automatisierung
festgelegt werden. Das bestehende HumHub-Aufgabenmodul wird nicht eingebunden.

## Lokale Prüfung

`tests/work.php` prüft unter anderem Einreichung durch Nichtmitglieder, Rollenrechte,
Direktdelegation, Informationsschutz, vollständigen Ablauf, Rollenwechsel während
Abnahme und Rollback bei Fehlern des Historieneintrags. `tests/render.php` rendert
Board, Idee und Abnahmeansicht und prüft sichere Ausgabe und POST/CSRF/Revision.
SQLite ersetzt keinen Test paralleler MySQL-Verbindungen oder die HTTP-Abnahme.
