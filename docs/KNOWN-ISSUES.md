# Offene Testpunkte

Stand: 09.09.2026

Dies sind keine bestätigten Produktfehler, sondern noch ausstehende Prüfungen
oder Einschränkungen der lokalen Testumgebung.

1. **Privater Delegationspfad:** Es fehlt ein Browser-Test mit einem privaten
   Ausgangskreis und einem öffentlichen direkt benachbarten Zielkreis. Dabei
   muss nochmals sichtbar geprüft werden, dass alte Kommentare, Ereignisse und
   Vorschlagsfassungen im Zielkreis nicht erscheinen.
2. **Abweisung und Nacharbeit:** Die Pfade „begründet ablehnen“ und „zur
   Nacharbeit zurückgeben“ wurden noch nicht als eigene Systemnutzer-Abläufe
   durchgespielt.
3. **Automatisierte Komponenten-Tests:** Die Testskripte `tests/run.php` und
   `tests/work.php` können in der aktuellen WSL-PHP-Installation nicht starten,
   weil der PDO-SQLite-Treiber fehlt. Das ist eine Einschränkung der
   Testumgebung, kein reproduzierter Modulfehler.
