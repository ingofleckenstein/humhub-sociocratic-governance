# Konfigurierbares manuelles Deployment

## Ablauf

Root lädt den Git-Checkout in ein ausdrücklich angegebenes privates Verzeichnis,
kopiert das Modul in die ausgewählte HumHub-Installation und übergibt ausschließlich
den Modulordner an deren Systembenutzer. Migrationen und Cacheleeren laufen über
runuser unter diesem Website-Benutzer.

Es gibt keine fest eingebauten Serveradressen, Benutzer oder Installationspfade.
Zunächst auf einer Testinstallation verwenden.

## Konfiguration

| Variable | Bedeutung |
|---|---|
| HUMHUB_ROOT | Pflicht: absoluter Installationspfad mit protected/yii |
| DEPLOY_USER | Pflicht: vorhandener Website-Systembenutzer, nicht root |
| DOWNLOAD_ROOT | Pflicht: vorhandenes Downloadverzeichnis, root-Eigentum und Modus 700 |
| PHP_BIN | Optional: PHP-CLI-Binary passend zur Website; Standard php |
| DEPLOY_BRANCH | Optional: Git-Branch; Standard main |

Der Checkout liegt unter DOWNLOAD_ROOT/sociocratic-governance.
Das Modulziel ist HUMHUB_ROOT/protected/modules/sociocratic-governance.
Download- und Installationsverzeichnis dürfen sich nicht überlappen.

## Einmalig vorbereiten

Als root ausführen und die Beispielwerte durch die eigenen Angaben ersetzen:

```bash
export HUMHUB_ROOT="/srv/example/humhub-test"
export DEPLOY_USER="website-user"
export DOWNLOAD_ROOT="/root/module-downloads"
export PHP_BIN="/usr/bin/php"

install -d -m 700 -o root -g root "$DOWNLOAD_ROOT"
curl --fail --location \
  https://raw.githubusercontent.com/ingofleckenstein/humhub-sociocratic-governance/main/scripts/deploy-test.sh \
  -o "$DOWNLOAD_ROOT/deploy-governance.sh"

# Skript prüfen, anschließend Vorschau:
bash "$DOWNLOAD_ROOT/deploy-governance.sh" --dry-run

# Bewusst anwenden:
bash "$DOWNLOAD_ROOT/deploy-governance.sh" --apply
```

Die Werte können alternativ in einer privaten, root-eigenen Konfigurationsdatei
außerhalb des Repositories als export-Anweisungen gespeichert und vor dem Start
in die Shell geladen werden. Keine tatsächlichen Betriebsangaben committen.
Das laufende Skript außerhalb des synchronisierten Modul-Checkouts speichern.

## Voraussetzungen und Schutz

Benötigt werden Bash, Git, rsync, realpath, flock, find, runuser, id, chown, chmod,
stat sowie PHP-CLI mit passenden Erweiterungen. Das Skript startet nur als root.

Ein bestehender Checkout muss root gehören, den richtigen Ursprung und Branch haben
und frei von lokalen Änderungen sein. Updates erfolgen nur als Fast-forward.
Symbolische Links im Modulquell- und Zielbaum werden abgelehnt.
Die Sperrdatei verhindert gleichzeitige Deployments mit demselben Downloadverzeichnis.

Die Vorschau aktualisiert nur den privaten Checkout. Website-Dateien, Eigentümer
der Website und Datenbank bleiben unverändert.
Beim Anwenden entfernt rsync veraltete Dateien ausschließlich im geprüften Modulziel,
setzt Verzeichnisse auf 755 und Dateien auf 644 und übergibt sie dem Website-Benutzer
und dessen primärer Gruppe. Keine Laufzeitdaten im Modulordner speichern.

## HumHub-Befehle

Nach der Kopie werden im protected-Verzeichnis unter DEPLOY_USER ausgeführt:

```bash
php yii cache/flush-all --interactive=0
php yii migrate/up --includeModuleMigrations=1 --interactive=0
php yii cache/flush-all --interactive=0
```

Dabei wird das konfigurierte PHP_BIN verwendet.
**Der Migrationslauf verarbeitet alle ausstehenden Core- und aktivierten Modulmigrationen
der ausgewählten Installation.** Es findet kein Core-Datei- oder Composer-Update statt.

Nach der ersten Bereitstellung das Modul global und im gewünschten Space aktivieren.
Siehe [Installation](INSTALLATION.md).

Dateikopie und Datenbankänderung sind kein atomarer Release-Wechsel. Bei Fehlern
stoppt das Skript ohne automatischen Rollback. Vorhandene Backups müssen eine
abgestimmte Wiederherstellung von Dateien und Datenbank ermöglichen.

Bash-Syntax lokal geprüft; der tatsächliche Root-/runuser-/rsync-Lauf muss auf der
jeweiligen Testinstallation geprüft werden.

Quelle: [HumHub CLI](https://docs.humhub.org/docs/admin/console/).
