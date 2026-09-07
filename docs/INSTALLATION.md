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

Jeder Kreis kann unter „Mandat & Rollen pflegen“ eine von sechs abgestimmten
Akzentfarben wählen. Die Farbe dient ausschließlich der Wiedererkennbarkeit:
Karten und Vorhaben erhalten eine schmale Kante und eine helle Tönung.

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

## VCard-Konfiguration und Kartenabnahme

Die optionale Anbindung ist gegen Virtual Card Popover 1.2.1 geprüft. Für
1.2.x ab 1.2.1 stellt Governance die zusätzlichen Template-Werte bereit:

```twig
{% if user.rolls %}Kreisrollen: {{ user.rolls }}{% endif %}
```

In der Space-Vorlage:

```twig
{% if space.purpose %}Zweck: {{ space.purpose }}<br>{% endif %}
{% if space.mandate %}Mandat: {{ space.mandate }}{% endif %}
```

Die früher gewünschten Schreibweisen `{user.rolls}`, `{space.purpose}` und
`{space.mandate}` werden ebenfalls aufgelöst. Die Vorlagen erhalten skalare
Daten; die Twig-Sandbox bleibt aktiv und die Ausgabe wird escaped und bereinigt.
Ungültige Vorlagen zeigen eine verständliche Meldung innerhalb der Karte.
Ohne eigene Platzhalter ergänzt das vorhandene VCard-Addon weiterhin die
Governance-Angaben. Bereits tatsächlich in der Beschreibung ausgegebene Angaben werden feldweise
im Addon unterdrückt; fehlende Angaben bleiben als Ergänzung sichtbar.

Technisch wird HumHubs `Widget::EVENT_CREATE` für `VCardUser` und `VCardSpace`
verwendet; die originalen VCard-Ansichten bleiben bestehen. Die Ereignisse werden
unabhängig von der Modul-Ladereihenfolge registriert. Ab VCard 1.3 wird der
Template-Adapter bis zu einer erneuten API-Prüfung nicht eingesetzt. Ohne VCard
bleiben Governance und die normalen Profil-Links nutzbar.

Die Karte zeigt alle aktiven, für die betrachtende Person sichtbaren Mitglieder.
Doppelrollen werden an einem Bild zusammengefasst; Rollen und Namen erscheinen
bei Hover und Tastaturfokus. Nicht besetzte Rollen erzeugen keine fiktiven Personen.
Bei vielen Mitgliedern wächst der Kreis. Kreisname und Profilbilder sind getrennte
Links; die Mandatskurzform erscheint bei Hover und Fokus, Escape blendet sie aus.

Auf der Testinstanz beide VCard-Typen und private, blockierte sowie archivierte
Kreise prüfen. Lange Namen, 255 Zeichen Kurztext, fehlende Rollen, viele Mitglieder
und mehrere Geschwisterkreise testen. Nach Bereitstellung die gemeldeten 401-
Antworten separat nachprüfen: Der belegte Twig-Fehler erklärt nicht zwingend jede
HTTP-Autorisierungsantwort. Die HTTP- und Theme-Abnahme dieser Korrektur steht aus.

## Pflichtmodule

Bei Aktivierung eines Arbeitskreises werden die global aktivierten und im Space
verfügbaren Pflichtmodule über HumHubs Modulverwaltung eingeschaltet:
Community-Mediathek (`peertube`) 2.8.6+, Share Content (`sharebetween`) 1.2.1+
und Wiki (`wiki`) 2.5.12+. Die Modulkennungen und Schnittstellen wurden gegen die
lokalen Modulquellen geprüft. Es werden keine Drittmodule heruntergeladen,
global aktiviert oder deren fachliche Rechte überschrieben.

Für bestehende Arbeitskreise: Im Governance-Backend als Systemadministrator*in
mit Zugang zum Backend **Pflichtmodule einrichten** ausführen. Der POST-Endpunkt
prüft zusätzlich die Governance-Backend-Berechtigung und verwendet CSRF-Schutz.
Bereits aktivierte Module bleiben bestehen. Jede Modulaktivierung verwendet eine
Transaktion; nicht verfügbare, veraltete oder fehlgeschlagene Module werden gemeldet.
Erfolgreiche Aktivierungen anderer Module bleiben erhalten. Fehlende Module zuerst
in der globalen Modulverwaltung bereitstellen, dann Einrichtung wiederholen.
Die Modulrechte und der Aktivierungslauf müssen noch auf der Testinstanz geprüft
werden. Das HumHub-Aufgabenmodul gehört nicht zu dieser Einrichtung.

## Vorhaben-Board (unveröffentlichter Arbeitsstand)

Die neue Migration `m260906_180000_work_board` legt Vorhaben und Ereignishistorie
an. Vor Bereitstellung Backup anlegen und Migrationen gemäß Deployment-Anleitung
auf der Testinstanz ausführen. Keine manuellen Schemaänderungen vornehmen.

Im eingerichteten Arbeitskreis „Vorhaben“ öffnen. Testablauf: Nichtmitglied reicht
Idee ein und kommentiert; Kreisleitung/Delegierte*r nehmen mit Mandatsbezug an;
Mitglied wählt „Mir zuweisen“, beginnt die Bearbeitung und legt ein Ergebnis vor.
Beide Verbindungsrollen bestätigen unabhängig; alternativ zur Nacharbeit zurückgeben.
Delegation nur zum direkten Oberkreis oder direkten Unterkreis prüfen, einschließlich
versteckter Kreise, Rollenwechsel, veralteter Formulare und paralleler Requests.

Die Historie wird erhalten. Bisher beteiligte Spaces und Vorhaben lassen sich mit
bestehender Historie nicht endgültig löschen (Fremdschlüssel mit RESTRICT).
Stattdessen archivieren. Beim Kreiswechsel bleiben alte Zugriffsgrenzen bestehen;
Personen ohne Zugang zu einem früheren Kreis sehen das gesamte Vorhaben nicht.
Konsentverfahren und Sitzungszuordnung sind noch nicht Bestandteil des Boards.
Neue Ideen benachrichtigen Kreisleitung und Delegierte*n im HumHub-Benachrichtigungsmenü.
Bei Annahme, Rückmeldung, Statuswechsel und Abnahme werden nur direkt beteiligte,
weiterhin zugriffsberechtigte Personen informiert. Die Kategorie ist standardmäßig
eine reine In-App-Benachrichtigung; sie versendet keine E-Mails. Siehe
[Vorhaben-Arbeitsstand](VORHABEN-PLAN.md).

Unter „Mitwirken“ im oberen Menü erscheint das Dashboard „Wie kann ich mich
einbringen?“. Es enthält nur Vorhaben, auf die die angemeldete Person Zugriff hat,
und lässt sich nach Themen oder Ressourcenbedarf ansehen. Zum Prüfen ein Vorhaben
mit Themen anlegen und bei einer Aufgabe mindestens eine Ressource mit Bedarf,
Einheit und zugesagter Menge eintragen. Nach einer neuen Idee muss der Hinweis im
Benachrichtigungsmenü der beiden aktiven Verbindungsrollen direkt erscheinen.
Die gesamte Vorhabenkachel öffnet das Vorhaben; der verlinkte Arbeitskreis bleibt
ein eigener, direkter Einstieg.
