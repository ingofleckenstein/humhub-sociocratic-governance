# background
## Abhängigkeiten: 

## Optionale apps:
- Virtual Card Popover Version 1.2.1 --> Überall wo Userprofilbilder angezeigt werden, wird ein Popover angezeigt, das die wichtigsten Informationen über den User anzeigt. 
	- Falls dieses Plugin nicht installiert ist, werden die Profilbilder ohne Popover angezeigt und alles weitere, was mit dem Popover zusammenhängt, wird ignoriert. 
	- Falls dieses Plugin installiert ist, werden Variable Namen, für die Popover bereitgestellt. 
		- Bspw. {{user.rolls}} --> zeigt die Rollen des Users an. Format: AK-name:Rolle, AK-name:Rolle, ... (sortiert nach entfernung zum Kernkreis)
		- außerdem wird {space.purpose} bereitgestellt, um den Zweck des Kreises anzuzeigen.
		- außerdem wird {space.mandate} bereitgestellt, um das Mandat des Kreises anzuzeigen.
	- M3 ausbaustufe: 
		- Im backend wird ein Hilfsplugin (bei aktiviertem plugin) für VirtualCard Popover (zur installation) bereitgestellt.
		- Wenn das Plugin nicht installiert ist, wird ein Hinweis angezeigt, dass das Plugin (und Version) für die volle Funktionalität benötigt wird.

- Pflicht: Modul in angegebener Version oder höher muss installiert sein.
- Optional: Modul darf installiert sein, wird von Governance aber ignoriert, solange keine explizite Integration definiert wurde.
- Ausgeschlossen: Modul darf nicht parallel installiert/aktiviert sein, weil Governance diese Funktion selbst bereitstellt.
Modulübersicht
| Modul | Mindestversion | Status | Anmerkung |
|---|---:|---|---|
| Linkliste | 0.9.1+ | Optional | Wird ignoriert |
| Community-Mediathek | 2.8.6+ | **Pflicht** | Muss installiert und aktiviert sein |
| Eigene Seiten | 1.12.20+ | Optional | Wird ignoriert |
| Kalender | 1.8.17+ | Optional | Wird ignoriert |
| Share Content | 1.2.1+ | **Pflicht** | Muss installiert und aktiviert sein |
| Arbeitskreis / Governance | 0.1.0+ | **Pflicht** | Governance-Kernmodul |
| Wiki | 2.5.12+ | **Pflicht** | Perspektivisch Integration der benötigten Funktionen in Governance möglich |
| Fragen & Antworten | 1.1.3+ | Optional | Wird ignoriert |
| Aufgaben | – | **Ausgeschlossen** | Eigene Aufgabenverwaltung in Governance; keine Doppelinstallation |
| Externer Kalender | 1.6.4+ | Optional | Wird ignoriert |
| Galerie | 1.7.1+ | Optional | Wird ignoriert |
| Dateien | 0.17.4+ | Optional | Wird ignoriert |
| Let's Meet | 1.1.5+ | Optional | Wird ignoriert |


# Arbeitskreisübersicht im AK-modul
https://testcommunity.selbstsein.events/s/ak-tst10/sociocratic-governance/circle/edit
Teilung des bereichs "Mandat – Verantwortung, Befugnisse und Grenzen"
--> Verantwortung: Befugnisse: Grenzen / Budget: Wiederwahl: standardmäßig alle 6 Monate Review:
--> bekommen eigene Felder um seperat ausfüllbar und anzeigbar zu sein. 
- eine Lurzform des Mandates mit max 255 Zeichen wird definiert, um eine Übersicht über das Mandat zu geben.

## AK Moudulaktivierung
Eine AK-modulaktivierung setzt den Space automatisch auf "sichtbar" und "öffentlich". Die Sichtbarkeit kann im Space-Adminbereich geändert werden.
- Kreisleitung bekommt automatisch die Rolle "Besitzer"
- Sichtbarkeit: Öffentlich – nur registrierte Benutzer
- Beitritt: Einladung und Anfrage
- Standard-Inhaltssichtbarkeit: Öffentlich (nur registrierte Benutzer)
- Rollen: Besitzer | Administratoren | Moderatoren | Mitglieder | Benutzer
	- Mitglieder – Medien: Videos hochladen | Livestreams starten | Videos verwalten
	- Mitglieder – Inhalte: Beiträge erstellen | Themen hinzufügen/verwalten | Dateien hinzufügen
	- Mitglieder – Interaktion: Reagieren | Liken | Kommentieren | Umfragen | Q&A
	- Mitglieder – Sonstiges: Let's Meet verwalten | Benutzer einladen | öffentliche Inhalte erstellen
	- Benutzer/Nicht-Mitglieder: Reagieren | Liken | Kommentieren
	- Benutzer/Nicht-Mitglieder nicht erlaubt: Beiträge | Video-Upload | Livestreams	 | Videoverwaltung

# Globale Kreisübersicht
https://testcommunity.selbstsein.events/sociocratic-governance/directory/index
Es gibt 2 ansichten EIne grafische und eine tabelarische. Die grafische Ansicht ist eine Bubbleansicht. Die Tabelle ist eine klassische Tabelle mit Spalten und Zeilen.
## Tabelarische Ansicht
die tabelarischen ansicht startet mit dem Kreis Kern (definiert im backend) und listet alle Kreise auf, die unterhalb des Kernkreises liegen. Die Kreise werden in der tabelle ensprechend eingerückt dargestellt. Kreise der Gleichen Ebenee werden Alphabetisch sortiert. Die Spalten daneben sind: Kurzform des Mandat, Kreisrollen als Kreisrunde Profilbbilder (so dass VirteullCard popover diese erkennt). 
## Grafische Ansicht
- Die arbeitskreis bekommen eine interaktive Bubble ansicht. Ein kreis stellt eine Bubbel da. Die Doppelbindung wird durch 2 Profilbilder realisiert. Der Titel de Kreises wird in der Mitte der Bubble angezeigt. Um eine Überblick über das mandat zu geben, wird ein neu zu definierendes Mandat Kurzform in max 255 zeichen definiert 
- Die Arbeitskreisübersicht ist quasi wie eine Karte. Die Gezoomt und verschoben werden kann. Die Kreise sortieren sich direkt neben ihrem Elternelement ein. Finde einen Algorithmus der die Kreise immer so anordnet, dass sie sich nicht überlappen. Die verbindungen zwischen den kreisen Dürfen so lang wie notig werden. Jedoch so kurz wie möglich. 
- Die einzelenen Kreise sind anklickbar. Durch den link kommt man in den Kreis
- Die Kreisübersicht wird ab dem Zeitpunkt der Globalen Modulaktivierung in der Leiste oben dargestellt. 
- Bei öffnung wird die Kreisansicht auf ein (zu berechnendes) Zentrum fokussiert. Für die berechnung werden alle kreise herangezogen, in denen der User eine Rolle hat. Der Zoom ist so gewählt das alles auf eine Bildschirmbreite passt. 

AKs werden nicht in der Spaceübersicht angezeigt, sondern nur in der Kreisübersicht.

# Globale Humhub Erweiterungen
Mache auf Backend und Frontend Eingabefeldern (da wo sinnvoll) einen Markdown Editor verfügbar. Außerdem sollten die Eingabefelder Emojis unterstützen.


