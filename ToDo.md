# Aktueller Arbeitsstand und Prüfliste — 2026-09-06

Die folgenden Punkte sind der verbindliche Prüfabschnitt. Neue Aufgaben und
Rückfragen werden hier künftig immer als Checkbox ergänzt.

## Erledigt

- [x] Mandat in Kurzform, Verantwortung, Befugnisse, Grenzen, Budget,
  Wiederwahl und Review aufgeteilt.
- [x] Tabellenansicht der Kreisübersicht mit hierarchischer Einrückung und
  Profilbildern umgesetzt und geprüft.
- [x] Bubbleansicht korrigiert: größere, echte Kreise; lesbarer Titel und
  Mandatskurzform; Kreisleitung und Delegierte*r sind mit Bild und Rolle
  erkennbar.
- [x] VCard-Anbindung als Governance-Addon umgesetzt. Sie wird nur bei
  installiertem und aktiviertem Virtual Card Popover ab Version 1.2.1 geladen
  und ergänzt Benutzerkarten um Kreisrollen sowie Arbeitskreiskarten um Zweck
  und Mandat. Das VCard-Plugin selbst bleibt unverändert.
- [x] Markdown-Editoren und Emoji-Eingabe für passende Mandats- und Zweckfelder
  umgesetzt.
- [x] Aktivierungslogik umgesetzt: sichtbarer Space für angemeldete Personen,
  Beitritt per Einladung/Anfrage, öffentliche Standardinhalte und das Recht
  für Mitglieder, öffentliche Inhalte zu erstellen.

## Jetzt auf der Testinstallation prüfen

- [ ] Arbeitskreis-Modul in einem Test-Space aktivieren und die sechs
  Aktivierungsvorgaben im Space-Adminbereich kontrollieren.
- [ ] Bubbleansicht mit mindestens drei Ebenen und mehreren Geschwisterkreisen
  öffnen: Lesbarkeit, Überlappungen, Zoom, Verschieben und Links prüfen.
- [ ] Virtual Card Popover 1.2.1 oder neuer aktivieren und sowohl ein
  Benutzerbild als auch ein Arbeitskreisbild überfahren. Prüfen, dass die
  Rollen nach Entfernung zum Kernkreis sortiert sowie Zweck und Mandat sichtbar
  sind.
- [ ] Die Kartenansicht mit einem Konto prüfen, das Rollen in mehreren weit
  auseinanderliegenden Kreisen hat; alle diese Rollen müssen beim Öffnen im
  sichtbaren Bereich liegen.

## Benötigte Daten oder Entscheidungen

- [ ] **Ich brauche Testdaten:** einen Testaufbau mit Kernkreis, mindestens zwei
  Unterkreisen und klar gepflegter Kreisleitung sowie Delegiertenrolle. Keine
  Zugangsdaten hier eintragen; ein vorhandenes Testkonto genügt für die
  gemeinsame Prüfung.
- [x] Entscheidung: VCard-Inhalte werden als Governance-Addon eingeblendet;
  keine Änderung am VCard-Plugin und keine zusätzlichen Twig-Ausdrücke in dessen
  Templates.
- [ ] **Ich brauche Testdaten:** für die PeerTube-401-Meldungen eine
  reproduzierbare Freigabekonstellation und das erwartete Ergebnis (öffentlich,
  organisationsweit, Mitgliedschaft oder Passwort). Keine Passwörter in dieser
  Datei speichern.

## Nächste Themen nach der Abnahme

- [ ] Pflichtmodule auf der Testinstallation gegen die Modulübersicht prüfen
  und fehlende/inkompatible Module mit konkreter Versionsnummer festhalten.
- [ ] Rechte der angebundenen Module (Mediathek, Share Content, Wiki, Kalender,
  Let's Meet) pro Arbeitskreis praktisch testen und danach die nötigen
  Integrationen für M2 priorisieren.
- [ ] M2 fachlich zuschneiden: Vorhaben, Mandatsprüfung, Aufgaben und
  nachvollziehbarer Konsentverlauf benötigen zuerst ein abgestimmtes Daten- und
  Rechtekonzept.

# background
## Abhängigkeiten: 

## Optionale apps:
- **Entscheidung 2026-09-06:** Die unten beschriebenen Governance-Inhalte werden
  über das VCard-Addon eingeblendet. Das VCard-Plugin wird nicht geändert;
  eigene Twig-Platzhalter darin sind deshalb nicht Teil der Umsetzung.
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


--------------

Update: 6.9.26 ca 14:30

Schau dir die Bilder in /temp an. Die Bubbleansicht ist noch nicht so wie ich sie mir vorstelle. Die Texte sind nicht lesbar und nicht an die kreise angepasst. Außerdem sind die Kreisleiter und Delegierten nicht erkennbar wer jetzt so ist.

Tabelarische ansicht: Check
Grafische ansicht --> fehler!
Virtual Card Popover --> Nicht implementiert! Nachholen!
AK Moudulaktivierung --> Wurde von mir noch nicht gecheckt ist das umgesetzt?
Mardwodn Editor --> Ckeck

Welche themen sind die nächsten um das projekt weiter voranzubringen=?


Fehler

Warnungen zu Content-Security-Policy 2
Synchrone XMLHttpRequests am Haupt-Thread sollte nicht mehr verwendet werden, weil es nachteilige Effekte für das Erlebnis der Endbenutzer hat. Für weitere Hilfe siehe https://xhr.spec.whatwg.org/#sync-warning humhub-app.js:1:78697
Referrer Policy: Die weniger eingeschränkte Referrer Policy "no-referrer-when-downgrade" für die Website-übergreifende Anfrage wird ignoriert: https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg Google_Play_Store_badge_EN.svg
Referrer Policy: Die weniger eingeschränkte Referrer Policy "no-referrer-when-downgrade" für die Website-übergreifende Anfrage wird ignoriert: https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg download-on-the-app-store.svg
Enabling peertube-core-dark-brown theme style. jschannel-BWfacTIE.js:1:17066
XHR GET
https://video.selbstsein.events/api/v1/videos/2f5c065e-58e6-415d-8445-23e40266fe8d
[HTTP/2 401  47ms]
Enabling peertube-core-dark-brown theme style. jschannel-BWfacTIE.js:1:17066
Setting password from API 2 jschannel-BWfacTIE.js:1:17066
XHR GET
https://video.selbstsein.events/api/v1/videos/0f377b61-eabc-4214-a7f1-ac33529a95b7
[HTTP/2 401  44ms]
XHR GET
https://video.selbstsein.events/api/v1/videos/2f5c065e-58e6-415d-8445-23e40266fe8d
[HTTP/2 401  35ms]
XHR GET
https://video.selbstsein.events/api/v1/videos/0f377b61-eabc-4214-a7f1-ac33529a95b7
[HTTP/2 401  65ms]
Waiting for password from Embed API jschannel-BWfacTIE.js:1:17066
Using video password from API jschannel-BWfacTIE.js:1:17066
Loading peertube-core-dark-brown theme plugins. jschannel-BWfacTIE.js:1:17066
Enabling internal theme peertube-core-dark-brown jschannel-BWfacTIE.js:1:17066
Loading script /plugins/humhub-permalinks/1.2.0/client-scripts/client/embed-client-plugin.js of plugin humhub-permalinks jschannel-BWfacTIE.js:1:17066
Waiting for password from Embed API jschannel-BWfacTIE.js:1:17066
Using video password from API jschannel-BWfacTIE.js:1:17066
Loading peertube-core-dark-brown theme plugins. jschannel-BWfacTIE.js:1:17066
Enabling internal theme peertube-core-dark-brown jschannel-BWfacTIE.js:1:17066
Loading script /plugins/humhub-permalinks/1.2.0/client-scripts/client/embed-client-plugin.js of plugin humhub-permalinks jschannel-BWfacTIE.js:1:17066
Running hook action:embed.player.loaded of plugin humhub-permalinks jschannel-BWfacTIE.js:1:17066
Running hook action:embed.player.loaded of plugin humhub-permalinks jschannel-BWfacTIE.js:1:17066
Layout-Darstellung wurde erzwungen, bevor die Seite vollständig geladen war. Falls Stylesheets noch nicht geladen sind, kann dies zu einer kurzzeitigen Darstellung des Inhalts ohne Formatierung führen. stylesheets-manager.js:697:11
Enabling peertube-core-dark-brown theme style. jschannel-BWfacTIE.js:1:17066
Cannot get server translations DOMException: The operation was aborted. jschannel-BWfacTIE.js:1:17269
Cannot send client warn/error to server. TypeError: NetworkError when attempting to fetch resource.
    sendClientLog https://video.selbstsein.events/client/standalone/videos/assets/jschannel-BWfacTIE.js:1
    registerServerSending https://video.selbstsein.events/client/standalone/videos/assets/jschannel-BWfacTIE.js:1
    runHooks https://video.selbstsein.events/client/standalone/videos/assets/jschannel-BWfacTIE.js:1
    error https://video.selbstsein.events/client/standalone/videos/assets/jschannel-BWfacTIE.js:1
    getServerTranslations https://video.selbstsein.events/client/standalone/videos/assets/embed-BKNiLO-K.js:2
    promise callback*getServerTranslations https://video.selbstsein.events/client/standalone/videos/assets/embed-BKNiLO-K.js:2
    init https://video.selbstsein.events/client/standalone/videos/assets/embed-BKNiLO-K.js:2
    main https://video.selbstsein.events/client/standalone/videos/assets/embed-BKNiLO-K.js:2
    <anonymous> https://video.selbstsein.events/client/standalone/videos/assets/embed-BKNiLO-K.js:2
jschannel-BWfacTIE.js:1:17930


