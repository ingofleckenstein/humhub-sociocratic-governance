# Installation von Version 0.2.0

Erste Entwicklungsfassung für HumHub Community Edition 1.18.5.
Vollständiger Installationstest auf MySQL/MariaDB steht noch aus.

## Deployment

Den aktuellen Stand von scripts/deploy-test.sh herunterladen und als root starten.
Vorher HUMHUB_ROOT, DEPLOY_USER und DOWNLOAD_ROOT für die eigene Testumgebung setzen.
Root kopiert das Modul und übergibt es an den konfigurierten Website-Benutzer.
HumHub-Befehle laufen unter diesem Benutzer. Siehe [Deployment](DEPLOYMENT.md).

## Erstaktivierung

1. Dateien in protected/modules/sociocratic-governance bereitstellen.
2. Unter Administration → Module „Sociocratic Governance“ aktivieren.
   Die Migrationen legen die Governance-Tabellen und die strukturierten Mandatsfelder
   mit dem konfigurierten Tabellenpräfix an.
3. Im gewünschten Space unter dessen Modulverwaltung „Arbeitskreis“ aktivieren.
   Der Space wird auf sichtbar für registrierte Nutzer*innen, Beitritt per Einladung
   und Anfrage sowie öffentliche Standardinhalte gesetzt. Die Sichtbarkeit kann danach
   im Space-Adminbereich geändert werden.
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

Zweck und strukturiertes Mandat pflegen, Oberkreis zuordnen, vier Rollen manuell
besetzen, aktuelle Rollen im Personenprofil anzeigen und methodische Hilfen lesen.
Eine Person darf mehrere Rollen tragen, aber niemals zugleich Kreisleitung und Delegierte*r.
Beim Speichern einer Kreisleitung wird diese Person Space-Besitzer*in; nur bestehende
Space-Besitzer*innen oder -Administrator*innen dürfen diesen Rollenwechsel ausführen.

Die globale Kreisübersicht ist über „Arbeitskreise“ in der oberen Navigation erreichbar.
Sie enthält eine hierarchische Tabelle und eine zoombare Karte.

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
- Aktivierung prüfen: Sichtbarkeit für registrierte Nutzer*innen, Beitritt per Einladung
  und Anfrage, öffentliche Standardinhalte sowie Schreibrecht für Mitglieder.
- Kreisleitung durch eine*n Space-Administrator*in wechseln und den Besitzerwechsel prüfen;
  als normales Mitglied muss dieser Wechsel abgelehnt werden.
- Kreisübersicht in Tabellen- und Kartenansicht mit sichtbaren und privaten Kreisen prüfen.

Bei Deaktivierung gibt es absichtlich keine Datenlöschung. Das endgültige Löschen eines
HumHub-Spaces entfernt jedoch seine Governance-Daten durch Fremdschlüssel.
Für Wissenserhalt Space archivieren statt löschen.

Eine fehlgeschlagene Migration wird nicht automatisch rückgängig gemacht. MySQL-DDL kann
bereits wirksam sein; bei Fehlern vorhandenes Backup und Protokoll heranziehen.
