# Sociocratic Governance for HumHub

Eine soziokratische Governance-Schicht für HumHub.

**Status: Projektstart / Modulgerüst, keine produktionsreife Governance-Anwendung.**
Das Repository enthält die Modulregistrierung und die fachliche sowie technische Roadmap.
Die unten beschriebenen Benutzerfunktionen sind geplant und noch nicht implementiert.

## Ziel
Gemeinschaften organisieren ihre Zusammenarbeit in Kreisen mit klaren Zuständigkeiten.
Entscheidungen entstehen im Konsent: Ein begründeter Einwand wird bearbeitet; Schweigen
oder das Ende einer Frist zählt nicht automatisch als Konsent.

## Geplanter Umfang
- Kreise mit Zweck, Domäne, Verantwortlichkeiten und Mitgliedschaft.
- Doppelte Verknüpfung durch Kreisleitung und gewählte Delegation.
- Rollen mit Mandat, Amtszeit, Vertretung und Überprüfung.
- Vorschläge, Verständnisfragen, Meinungsrunde, Konsentrunde und Einwandintegration.
- Offene soziokratische Wahlen mit begründeten Nominierungen und Konsent.
- Sitzungen, Agenda, Runden, Protokoll und Aufgaben.
- Versionierte Beschlüsse mit Verantwortlichen, Reviewterminen und Erfolgskriterien.
- Transparenz innerhalb berechtigter Kreise, Änderungsverlauf und Benachrichtigungen.

## Entwicklung
Technische Ausgangsbasis ist das HumHub-Modulsystem (PHP / Yii2).
Zielbasis: HumHub 1.18 oder neuer; Kompatibilität ist noch nicht in einer HumHub-Installation geprüft.

Das Verzeichnis muss als `sociocratic-governance` in einem konfigurierten HumHub-Modulpfad liegen.
Nach Aktivierung registriert dieses Gerüst lediglich das Modul. Es bietet noch keine Oberfläche
und legt keine Datenbanktabellen an. Nicht als fertiges Plugin installieren.

- [Fachkonzept](docs/GOVERNANCE.md)
- [Architektur und Berechtigungen](docs/ARCHITECTURE.md)
- [Roadmap mit Abnahmekriterien](docs/ROADMAP.md)
- [Mitwirken](CONTRIBUTING.md)

## Prüfung
`php -l Module.php` und `php -l config.php` prüfen die Syntax.
Integrationstests mit HumHub und Datenbank sind vor einer ersten nutzbaren Version erforderlich.

## Referenzen
- [HumHub-Modulentwicklung](https://docs.humhub.org/docs/develop/modules/)
- [Offizielles Beispielmodul](https://github.com/humhub/example-basic)

## Lizenz
Eine Open-Source-Lizenz wird vor dem ersten Release festgelegt. Öffentlich sichtbar bedeutet
nicht automatisch, dass eine Nutzungslizenz erteilt wurde.
