# HumHub-Kompatibilität 0.6.7

Stand: 30.10.2026. Ziel: HumHub CE 1.18.6 und 1.19.3, PHP 8.2+.
Die bisherige Mindestversion bleibt erhalten; die obere Grenze ist bewusst auf 1.19.x gesetzt.

VCard-Adapter unterstützt 1.2.1 bis 1.3.x. Dashboard-Weiterleitung respektiert bestehende Weiterleitungen und User-Gates.

Geprüft in getrennten frischen HumHub-/MariaDB-Installationen mit den offiziellen Paketen 1.18.6 und 1.19.3: Modulaktivierung/Migrationen, Laden der Modulklassen und Rendern der Verwaltungs-/Navigationsansichten. Die bestehenden Geschäftsregeln wurden nicht erweitert. Produktive Dateien und Datenbanken wurden nicht geändert.

API-Kompatibilitäts- und VCard-Prüfungen bestehen mit beiden Core-Versionen. Die älteren SQLite-Suiten `tests/run.php` und `tests/work.php` scheitern bereits im Ausgangsstand an nicht aktualisierten Fixtures/Umgebung (URL-Host bzw. unveröffentlichte Kreise); sie sind keine erfolgreiche Gesamtabnahme. Die echte Testinstallation konnte Verzeichnis, persönlichen Einstieg und Verwaltung mit beiden Core-Versionen rendern.

Ein vollständiger Test der bestehenden produktiven Daten, des individuellen Themes und aller übrigen Community-Module ist damit nicht ersetzt. Insbesondere Keycloak und Share Content gehören nicht zu diesem Änderungssatz.
