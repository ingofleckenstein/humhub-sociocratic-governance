# Installation von Version 0.1.0

Erste Entwicklungsfassung für HumHub Community Edition 1.18.5.
Vollständiger Installationstest auf MySQL/MariaDB steht noch aus.

## Deployment

Den aktuellen Stand von scripts/deploy-test.sh erneut herunterladen; ältere Downloads
kennen die neue Benutzerprüfung nicht. Das Skript ausschließlich als
**root** starten. Es lädt nach /root/temp/data, kopiert die Dateien und übergibt das Modul
an **sexpositiv.events_0chzqp83gyz5**. HumHub-Befehle laufen als Website-Benutzer. Siehe [Deployment](DEPLOYMENT.md).

## Erstaktivierung

1. Dateien in protected/modules/sociocratic-governance bereitstellen.
2. Unter Administration → Module „Sociocratic Governance“ aktivieren.
   Die initiale Migration legt vier Tabellen mit dem konfigurierten Tabellenpräfix an:
   sg_circle, sg_role, sg_config, sg_permanent.
3. Im gewünschten Space unter dessen Modulverwaltung „Arbeitskreis“ aktivieren.
   Eine vorhandene private Sichtbarkeit wird nicht verändert. Für organisationsweites
   Lesen die Space-Sichtbarkeit auf registrierte Nutzer*innen einstellen.
4. Als Space-Mitglied „Arbeitskreis“ öffnen und „Mandat & Rollen pflegen“ wählen.
5. Den Kernkreis zuerst ohne Oberkreis speichern. Weitere Kreise danach einrichten.
6. In der globalen Modulkonfiguration Kernkreis, Trägerorganisation und bei Bedarf
   Admin-Sonderrolle festlegen.

Manuelle Direktlinks, falls die Navigation noch nicht angezeigt wird:

- Kreisübersicht: index.php?r=sociocratic-governance/directory/index
- Backend: index.php?r=sociocratic-governance/admin/index

Nach Konfiguration einer Admin-Sonderrolle kann nur diese Person das Backend bedienen.
Deren Konto muss aktiv bleiben. Ein Notfall-Recovery-Verfahren ist noch nicht implementiert;
vor einem Kontowechsel die Zuständigkeit auf der Backend-Seite übertragen.

## Was bereits funktioniert

Zweck und Mandat pflegen, Oberkreis zuordnen, vier Rollen manuell besetzen,
aktuelle Rollen im Personenprofil anzeigen und methodische Hilfen lesen.
Eine Person darf mehrere Rollen tragen, aber niemals zugleich Kreisleitung und Delegierte*r.

Dauerhafte Mitgliedschaften sind dokumentierte Vereinbarungen mit vorhandenen
Kreismitgliedern. Sie erzwingen noch keine Mitgliedschaft und keinen Austrittsschutz.

## Abnahme auf der Testinstallation

- Global und im Space aktivieren; alle Seiten ohne Fehler öffnen.
- Als Kreismitglied Zweck, Mandat und verschiedene Personen in den Verbindungsrollen speichern.
- Als registriertes Nichtmitglied lesen; direkter Aufruf des Bearbeitungslinks muss scheitern.
- Als Gast dürfen die Pluginseiten nicht erreichbar sein.
- Privaten Kreis im Profil und in Kreisübersicht vor Nichtmitgliedern verbergen.
- Zwei Bearbeitungsformulare öffnen: nach Speichern des ersten muss der zweite Stand
  als veraltet abgelehnt werden.
- Oberkreisbeziehung auf sich selbst und indirekte Schleife versuchen: beide müssen scheitern.
- Profilanzeige und Verwaltung der dauerhaften Vereinbarungen prüfen.
- Modul im Space deaktivieren und wieder aktivieren: Angaben bleiben erhalten.
- Global deaktivieren/reaktivieren: Tabellen und Inhalt bleiben erhalten.

Bei Deaktivierung gibt es absichtlich keine Datenlöschung. Das endgültige Löschen eines
HumHub-Spaces entfernt jedoch seine Governance-Daten durch Fremdschlüssel.
Für Wissenserhalt Space archivieren statt löschen.

Eine fehlgeschlagene Migration wird nicht automatisch rückgängig gemacht. MySQL-DDL kann
bereits wirksam sein; bei Fehlern vorhandenes Backup und Protokoll heranziehen.
