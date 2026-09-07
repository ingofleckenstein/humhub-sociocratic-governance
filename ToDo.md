# ToDo

## Prüfung auf der Testinstallation

- [ ] Arbeitskreis-Modul in einem Test-Space aktivieren und Sichtbarkeit,
  Beitritt per Einladung/Anfrage, Standard-Inhaltssichtbarkeit sowie die
  Mitgliedsrechte prüfen.
- [ ] Bubbleansicht mit Kernkreis, mindestens zwei Unterkreisen und mehreren
  Geschwisterkreisen prüfen: Lesbarkeit, Überlappungen, Zoom, Verschieben und
  Links.
- [ ] Virtual Card Popover 1.2.1 oder neuer aktivieren und Benutzer- sowie
  Arbeitskreis-Karten prüfen: Kreisrollen, Zweck und Mandat müssen erscheinen.
- [ ] Die Kartenansicht mit einem Konto testen, das Rollen in mehreren weit
  auseinanderliegenden Kreisen hat.
- [ ] Pflichtmodule auf Version und Aktivierung prüfen: Community-Mediathek
  2.8.6+, Share Content 1.2.1+ und Wiki 2.5.12+.

## Nächste Entwicklung

- [ ] Rechte der angebundenen Module (Mediathek, Share Content, Wiki, Kalender,
  Let's Meet) pro Arbeitskreis praktisch testen und nötige M2-Integrationen
  priorisieren.
- [ ] Erstes Vorhaben-Board auf HumHub/MySQL abnehmen: Einreichung, Kommentare,
  Annahme, Selbstzuweisung, direkte Delegation, Bearbeitung und Abnahme.
- [ ] Konsent-/Abstimmungsrunden, Sitzungszuordnung und Beschlussregister ergänzen.
- [ ] Neue Kreisvisualisierung im echten HumHub-Layout mit mehreren
  Hierarchieebenen und unterschiedlichen Rollenbesetzungen abnehmen.

## Aktuelle Antwort

Das erste Vorhaben-Board ist lokal implementiert:

- Ideen durch aktive Communitymitglieder mit Lesezugang, einschließlich Kommentaren.
- Annahme mit Mandatsbezug durch Kreisleitung oder Delegierte*n.
- Aufgaben übernehmen über „Mir zuweisen“, bearbeiten, zur Abnahme vorlegen oder
  begründet ablehnen. Derzeit bestätigen Kreisleitung und Delegierte*r beide.
- Delegation ausschließlich an den direkten Oberkreis oder direkte Unterkreise.
- Transaktionale Historie, Revisionsprüfung und Schutz früherer privater Inhalte.

Die doppelten VCard-Angaben werden feldweise unterdrückt. Fehlende Angaben ergänzt
weiterhin das Addon. Die funktionierende Pflichtmoduleinrichtung wurde durch deine
Rückmeldung bestätigt.

Für die Kreisgrafik empfehle ich ein hierarchisches Beziehungsdiagramm mit
aufklappbaren Mitgliedergruppen. [Vorschlag mit Diagramm](docs/KREISVISUALISIERUNG.md).
Die bisherige Bubble-Grafik ist durch eine datengetriebene JavaScript-Karte
ersetzt: Kreiskarten zeigen Mandatskurzform, aufklappbare Profil- und
Mitgliederbereiche, Doppelbindungen und barrierearme Zoom-/Zentriersteuerung.
Sie benötigt keinen externen Dienst und erhält ausschließlich die vom
Server-Sichtbarkeitsfilter erlaubten Daten.

Prüfung: 45 bestehende Komponentenprüfungen, 42 neue Vorhabenprüfungen und
11 VCard-Prüfungen bestanden; acht Ansichten gerendert, PHP-Syntax und
HumHub-1.18.5-/VCard-1.2.1-Kompatibilität geprüft. Nicht deployt.
Die neue Migration und die HTTP-/MySQL-/Theme-Abnahme stehen auf der Testinstanz aus.
Konsent-/Abstimmungsrunden sind noch nicht implementiert und bleiben der nächste
Ausbau. [Arbeitsstand und Grenzen](docs/VORHABEN-PLAN.md).

Parallel arbeiten: Im selben Projekt einen neuen Chat öffnen, unter dem Eingabefeld
„Worktree“ wählen, Ausgangsbranch auswählen und einen separaten Auftrag erteilen.
Für eine zweite Sitzung eignet sich der Kreisgrafik-Prototyp; das Board wurde hier
bearbeitet. Den benötigten Stand einschließlich neuer Dateien vor Arbeitsbeginn
prüfen. [Offizielle Anleitung](https://learn.chatgpt.com/docs/environments/git-worktrees).

Der Anweisungsbereich und die vorherige Antwort wurden durch diesen Stand ersetzt.


## Anweisungen

schau dir mal für das Grafikanzeige der Module das hier an. https://github.com/cytoscape/cytoscape.js-cxtmenu
So oder so ähnlich. möchte ich das es dargestellt wird. Ein kreis hat dann informationen die über ein Kontextmenu hoover angezeigt werden. Die informationen könnten bspw sein Mitglieder, Leitungsrollen, Zweck/Mandat. Unterkreise durch verbindungslinien darstellen.
