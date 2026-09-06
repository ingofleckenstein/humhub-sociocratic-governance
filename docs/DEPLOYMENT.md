# Manuelles Deployment auf die Testinstallation

Zielversion: HumHub Community Edition **1.18.5** (vom Betreiber angegeben).
Backups und Testkonten sind vorhanden. Es wird kein dauerhafter Agentenzugang benötigt:
Der Betreiber startet das Skript selbst per SSH als Systembenutzer der Website.

## Voraussetzungen

Linux mit Bash, Git, rsync, realpath, flock, find und passendem PHP-CLI.
PHP-CLI muss zur Website passen und deren benötigte Erweiterungen haben.
Nicht als root starten; unter Plesk den zur Testwebsite gehörenden Systembenutzer verwenden.
Das Skript verändert keine Eigentümer und legt keine Zugangsdaten ab.

## Erstmalig herunterladen

```bash
mkdir -p ~/temp
curl --fail --location https://raw.githubusercontent.com/ingofleckenstein/humhub-sociocratic-governance/main/scripts/deploy-test.sh -o ~/temp/deploy-governance.sh
bash ~/temp/deploy-governance.sh --dry-run
bash ~/temp/deploy-governance.sh --apply
```

Den heruntergeladenen Skripttext vor Ausführung ansehen. Nach Änderungen am Deployment-Skript
erneut herunterladen. Keine direkte Download-Pipe in eine Shell.

## Verzeichnisse und Einstellungen

- Git-Checkout: `~/temp/sociocratic-governance`
- HumHub-Root standardmäßig: `/var/www/vhosts/sexpositiv.events/testcommunity.selbstsein.events`
- Ziel: `protected/modules/sociocratic-governance` darunter.
- Branch: `main`; optional `DEPLOY_BRANCH`.
- PHP: `php`; optional `PHP_BIN` mit dem vollständigen Pfad zum passenden PHP-Binary.

Beispiel mit explizitem PHP-Pfad, den der Betreiber an seine Installation anpasst:

```bash
PHP_BIN=/pfad/zum/php bash ~/temp/deploy-governance.sh --apply
```

Falls im Standardverzeichnis kein `protected/yii` liegt, bricht das Skript ab.
Den tatsächlichen Installationspfad prüfen; das Skript nicht durch Umbenennen einer
Produktivinstallation überlisten. HUMHUB_ROOT darf auf einen anderen absoluten Pfad
mit dem abschließenden Verzeichnisnamen `testcommunity.selbstsein.events` gesetzt werden.

## Verhalten

1. Pfade, Werkzeuge und Schreibrechte prüfen; parallele Ausführung sperren.
2. Repository klonen oder unveränderten Checkout per Fast-forward synchronisieren.
3. Modul-ID, PHP-Syntax und unerwartete symbolische Links prüfen.
4. Vorschau oder Dateisynchronisation durchführen. Entfernte Quelldateien werden nur
   innerhalb des geprüften Modulziels entfernt; dort keine eigenen Daten speichern.
5. Bei `--apply` aus `protected` ausführen:

```bash
php yii cache/flush-all --interactive=0
php yii migrate/up --includeModuleMigrations=1 --interactive=0
php yii cache/flush-all --interactive=0
```

**Der Migrationsbefehl verarbeitet alle ausstehenden Core- und aktivierten
Modulmigrationen der Testinstallation.** Das ist kein ausschließlich auf dieses Modul
begrenzter Datenbanklauf. Kein Composer-Update, Core-Update oder Update anderer Moduldateien.

Eine Vorschau aktualisiert den temporären Git-Checkout, aber keine Website-Dateien oder
Datenbank. Die Dateikopie ist kein atomarer Release-Wechsel; während eines echten Updates
die Testwebsite nicht parallel benutzen. Das Skript richtet keinen Wartungsmodus ein.

## Erstaktivierung und Fehler

Nach dem ersten Kopieren das Modul in der HumHub-Administration aktivieren.
Das vorhandene Gerüst hat noch keine Migrationen oder Oberfläche. Der Upload ist deshalb
noch keine erste funktionale Pluginversion. Für später hinzukommende Migrationen die
Erstinstallation und Aktualisierung auf HumHub 1.18.5 testen.

Bei Fehlern stoppt das Skript. Es setzt keine Datenbankmigration automatisch zurück.
Dateien können dann bereits aktualisiert sein; Fehlermeldung prüfen und gegebenenfalls
das vorhandene Backup zur abgestimmten Wiederherstellung von Dateien und Datenbank verwenden.

## Prüfung des Skripts

Bash-Syntax lokal geprüft. Ein tatsächlicher Lauf mit Linux, rsync, HumHub 1.18.5 und
Datenbank auf dem Zielserver steht aus. Es wurde kein Deployment aus diesem Projektchat gestartet.

Quelle der Console-Befehle: [HumHub CLI](https://docs.humhub.org/docs/admin/console/).
