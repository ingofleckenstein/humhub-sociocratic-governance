# Manuelles Deployment auf die Testinstallation

## Zuständigkeiten

1. **Root** lädt das Repository nach /root/temp/data/sociocratic-governance.
2. **Root** kopiert die Moduldateien in die Testinstallation.
3. **Root** übergibt ausschließlich das Modulverzeichnis an **sexpositiv.events_0chzqp83gyz5** und dessen primäre Gruppe.
4. **Der Website-Benutzer** führt über runuser Migrationen und Cacheleeren aus.

Das Skript muss auch für eine Vorschau als root gestartet werden. Es führt kein
rekursives chown über die gesamte Website aus und verändert keine anderen Moduleigentümer.

## Herunterladen und starten

In einer Root-Sitzung:

```bash
mkdir -p /root/temp
curl --fail --location https://raw.githubusercontent.com/ingofleckenstein/humhub-sociocratic-governance/main/scripts/deploy-test.sh -o /root/temp/deploy-governance.sh

# Skript ansehen, dann Vorschau:
bash /root/temp/deploy-governance.sh --dry-run

# Dateien übertragen, Eigentümer setzen und HumHub aktualisieren:
bash /root/temp/deploy-governance.sh --apply
```

Das Skript nach Änderungen erneut herunterladen. Es liegt außerhalb des synchronisierten
Checkouts, damit ein Git-Update nicht das gerade laufende Skript überschreibt.

## Einstellungen

- Git-Checkout: /root/temp/data/sociocratic-governance
- Standard-HumHub-Verzeichnis: /var/www/vhosts/sexpositiv.events/testcommunity.selbstsein.events
- Modulziel darunter: protected/modules/sociocratic-governance
- Branch: main, alternativ DEPLOY_BRANCH
- PHP-CLI: php, alternativ PHP_BIN als vollständiger Pfad zur passenden PHP-Version
- Zielsystem: HumHub Community Edition 1.18.5

Beispiel mit einem zur Website passenden PHP-Binary:

```bash
PHP_BIN=/pfad/zum/php bash /root/temp/deploy-governance.sh --apply
```

HUMHUB_ROOT kann den Installationspfad überschreiben, muss aber mit dem Verzeichnisnamen
testcommunity.selbstsein.events enden und protected/yii enthalten. Ein abweichendes
Document-Root-Layout muss vor dem Deployment geprüft werden.

Benötigt: Bash, Git, rsync, realpath, flock, find, runuser, id, chown, chmod und PHP-CLI
mit den zur Website passenden Erweiterungen.

## Ablauf und Prüfungen

- Nur root darf starten; der Website-Benutzer muss existieren und PHP ausführen können.
- Downloadverzeichnis wird root zugeordnet und mit Modus 700 geschützt.
- Gleichzeitige Deployments werden über eine Sperrdatei verhindert.
- Bestehende Checkouts müssen root gehören, den richtigen Ursprung und Branch haben
  und frei von lokalen Änderungen sein. Updates erfolgen nur als Fast-forward.
- Symbolische Links im Modulquell- oder Zielbaum werden abgelehnt.
- Modul-ID und PHP-Syntax werden vor dem Kopieren geprüft; PHP läuft dabei bereits
  als Website-Benutzer, Inhalte werden aus dem privaten Root-Checkout über stdin zugereicht.
- rsync kopiert als root und entfernt veraltete Dateien ausschließlich im geprüften Modulziel.
  Verzeichnisse erhalten 755, Dateien 644. Keine Laufzeitdaten im Modulverzeichnis speichern.
- Danach wird der gesamte Modulzielbaum an den Website-Benutzer und dessen primäre Gruppe übergeben.
- Aus protected werden die folgenden Befehle jeweils per runuser als Website-Benutzer gestartet:

```bash
php yii cache/flush-all --interactive=0
php yii migrate/up --includeModuleMigrations=1 --interactive=0
php yii cache/flush-all --interactive=0
```

**Der Migrationsbefehl verarbeitet alle ausstehenden Core- und aktivierten Modulmigrationen
der Testinstallation**, nicht nur dieses Modul. Es werden keine Core-Dateien aktualisiert
und keine Composer-Updates ausgeführt.

Eine Vorschau synchronisiert den Root-Checkout und prüft ihn. Website-Dateien,
Website-Eigentümer und Datenbank bleiben dabei unverändert.
Die Dateikopie ist nicht atomar; während eines Updates die Testwebsite nicht parallel benutzen.

## Erstaktivierung und Fehler

Nach dem ersten Kopieren das Modul global und anschließend im gewünschten Space aktivieren.
Siehe [Installation und Abnahme](INSTALLATION.md).

Bei Fehlern stoppt das Skript. Dateien können bereits aktualisiert sein. Kein automatischer
Datenbank-Rollback; bei Bedarf vorhandenes Backup zur abgestimmten Wiederherstellung verwenden.

Bash-Syntax lokal geprüft. Ein tatsächlicher Root-/runuser-/rsync-Lauf auf dem Zielserver
steht aus. Durch diese Änderung wurde kein Serverdeployment ausgeführt.

Quelle der Console-Befehle: [HumHub CLI](https://docs.humhub.org/docs/admin/console/).
