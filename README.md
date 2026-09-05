# Sociocratic Governance for HumHub

Eine universell nutzbare Governance-Erweiterung für HumHub: Kreise sichtbar machen, Zusammenarbeit methodisch unterstützen und Menschen innerhalb klarer Mandate zum Handeln ermächtigen.

**Status: Konzept und minimales Modulgerüst. Die hier beschriebenen Funktionen sind noch nicht implementiert.** Es gibt derzeit keine Governance-Oberfläche, Rollenverwaltung, Entscheidungslogik oder Datenbanktabellen. Dieses README dokumentiert den gemeinsam erarbeiteten Zielstand vom 6. September 2026.

## Warum dieses Plugin?

Das Plugin hat zwei vorrangige Aufgaben:

1. **Optische Erinnerungshilfe:** Menschen erkennen, wie das Kreismodell funktioniert, wo sie dazugehören, welche Zuständigkeiten bestehen und wo ein Verfahren gerade steht.
2. **Methodische Unterstützung:** Verständliche Beschreibungen, Vorlagen und später gezielte Werkzeuge helfen bei Aufgaben, Entscheidungen, Rollen und Zusammenarbeit.

Das Ziel ist verteilte Verantwortung mit tatsächlicher Entscheidungshoheit innerhalb eines Mandats. Nicht jede Handlung benötigt einen neuen Beschluss: Menschen sollen in einem klar vereinbarten Rahmen selbstständig arbeiten können.

## Universelles Modell und organisationsbezogene Einstellungen

Das Projekt ist für unterschiedliche Organisationen gedacht. Personen, Firmennamen und konkrete Gemeinschaften werden nicht als feste Sonderfälle in den Code eingebaut.

Im Backend soll konfiguriert werden können:

- Welcher Kreis der Ausgangs- beziehungsweise Kernkreis ist.
- Welche Personen in welchen Kreisen dauerhaft Mitglied sind.
- Welche organisatorische Sonderrolle oder Begründung zu einer dauerhaften Mitgliedschaft gehört.

Eine dauerhafte Mitgliedschaft ist von einer befristeten Rollenbesetzung zu unterscheiden. Sie verleiht nicht automatisch technische Administrationsrechte oder ein persönliches Vetorecht außerhalb des vereinbarten Konsentverfahrens. Das genaue Verfahren zur Änderung solcher Backend-Einstellungen und seine Dokumentation sind noch zu definieren.

Der Kernkreis trägt zu Beginn Gesamtmandat, Gesamtverantwortung und die entsprechenden organisatorischen Entscheidungsbefugnisse. Mit der Bildung von Unterkreisen werden Mandatsbereiche und Entscheidungshoheit übertragen.

Das Modell ist soziokratisch inspiriert. Organisationsspezifische Regeln wie die Größengrenzen, dauerhafte Mitgliedschaften und das Startverfahren kleiner Kreise sind ausdrücklich Projektregeln, keine Behauptung über einen universellen soziokratischen Standard.

## 1. Spaces und Arbeitskreise

Technisch bleiben sowohl allgemeine Spaces als auch Arbeitskreise originale HumHub-Spaces. Ein Arbeitskreis (AK) ist ein Space mit Kreisstatus, Mandat und organisatorischer Einbindung.

Die Oberfläche trennt beide optisch:

- Allgemeine Spaces dienen Austausch und Zusammenarbeit.
- Arbeitskreise besitzen eine erkennbare Kennzeichnung und kreisspezifische Navigation.
- Eine Kreisübersicht zeigt Oberkreis, Unterkreise und Verbindungsrollen.
- Ein als Kreis eingerichteter Space erhält die vorgesehenen Governance-Funktionen; deren automatische Bereitstellung gehört zum späteren Ausbau.

Die Kreisstruktur soll ohne Änderungen am HumHub-Core aufgebaut werden. Kommunikation, vorhandene Dateien und andere geeignete HumHub-Funktionen werden einbezogen. Welche bestehenden Module technisch integriert werden, ist noch zu prüfen.

## 2. Mandate und verteilte Entscheidungshoheit

Ein Mandat beschreibt mindestens:

- Zweck und angestrebte Wirkung;
- Zuständigkeits- und Entscheidungsbereich;
- Verantwortung, Befugnisse und Grenzen;
- gegebenenfalls Budget und Ressourcen;
- Wiederwahlrhythmus des Kreises, standardmäßig **sechs Monate**;
- gegebenenfalls Gültigkeit, Reviewtermine und Erfolgskriterien.

Der Oberkreis definiert das Mandat des Unterkreises. Änderungen benötigen Konsent im Oberkreis. Ob zusätzlich der Konsent des betroffenen Unterkreises erforderlich ist, ist noch offen.

Bei einer Teilung beziehungsweise Unterkreisgründung geht ein Teil des Mandats an den neuen Kreis über. Damit gehen echte Entscheidungsbefugnisse innerhalb dieses Bereichs an den Unterkreis.

### Gültigkeit und Prüfung von Beschlüssen

- Ein Beschluss ist **ab Konsent gültig**.
- Er wird dem unmittelbar übergeordneten Kreis zur Mandatsprüfung vorgelegt.
- Der Oberkreis prüft ausschließlich, ob der Beschluss innerhalb des Mandats liegt.
- Beschließt der Oberkreis, dass eine Mandatsüberschreitung vorliegt, kann er den Beschluss revidieren.
- Betroffene Mandatsstelle, Begründung und Revisionsbeschluss sollen nachvollziehbar dokumentiert werden.

Dies ist keine allgemeine Vorabgenehmigung aller Unterkreisentscheidungen. Wie bereits ausgeführte Maßnahmen bei einer Revision behandelt werden, ist noch zu spezifizieren.

### Schutz der Mandate von Unterkreisen

Auch ein Oberkreis darf durch eigene Arbeitsentscheidungen delegierte Mandatsbereiche nicht einfach übergehen.

Geplanter Ansatz:

1. Vorschläge benennen betroffene Zuständigkeiten und Kreise.
2. Vor dem Konsent wird geprüft, ob delegierte Mandatsbereiche berührt werden.
3. Arbeit innerhalb eines delegierten Bereichs wird an den zuständigen Kreis gegeben.
4. Eine Änderung des Entscheidungsraums muss ausdrücklich als Mandatsänderung behandelt werden.

In Ausbaustufe 1 wird diese Prüfung beschrieben und von Menschen vorgenommen. Später können strukturierte Mandate Hinweise ermöglichen; eine zuverlässige automatische Bewertung beliebiger Texte wird nicht vorausgesetzt.

## 3. Rollen und doppelte Verknüpfung

Wir verwenden die gebräuchlichen Bezeichnungen **Kreisleitung** und **Delegierte*r**.

| Rolle | Bedeutung |
|---|---|
| Kreisleitung | Verbindung vom Oberkreis in den Unterkreis; trägt Ausrichtung und Zusammenhang mit dem Gesamtziel und unterstützt die Umsetzung. Die Person ist Mitglied des Oberkreises und wirkt im Unterkreis mit. |
| Delegierte*r | Vertretung des Unterkreises im Oberkreis; bringt dessen Perspektive, Wissen und Rückmeldungen ein. |
| Moderation | Begleitet Sitzungen, Runden und Entscheidungsfindung. |
| Dokumentation / Protokollführung | Hält Beschlüsse, Protokolle und relevante Unterlagen nachvollziehbar fest und unterstützt Reviews. |
| Fachliche Rollen | Vom Kreis definierte Aufgaben, beispielsweise Technik, Finanzen oder Kommunikation. |

Kreisleitung und Delegierte*r verbinden beide Kreise. Die vorgesehenen Konsentrechte sind von technischen Space-Rechten getrennt zu behandeln. Eine organisatorische Rolle bedeutet nicht automatisch Administration.

### Kleine Kreise und Doppelrollen

**Kreisleitung und Delegierte*r dürfen niemals dieselbe Person sein, auch nicht in einem Kreis mit zwei oder drei Personen.**

- Ein Kreis startet mit einer Kreisleitung und mindestens einer weiteren Person.
- Im Zweipersonenkreis übernimmt die zweite Person zunächst die Delegiertenrolle.
- Mit dem Beitritt einer dritten Person wird die Delegiertenrolle erstmals durch Wahl besetzt.
- Weitere Beitritte lösen keine erneute Wahl aus. Danach gilt die Wahlfrist aus dem Mandat.
- Andere Doppelrollen sind in Zwei- und Dreipersonenkreisen möglich.
- Ab vier Personen werden andere Doppelrollen **kritisch mit rotem Text und zusätzlichem Warnsymbol** angezeigt. Die Warnung ist keine automatische Absetzung.

Welche Mitgliedschaften für diese Vierpersonengrenze zählen, ist noch abschließend festzulegen. Sie darf nicht unbemerkt mit der gesonderten Zählregel für Kreisteilungen vermischt werden.

### Wahlen und Amtszeiten

Rollenbesetzungen sollen durch ein Konsentverfahren legitimiert und mit Beginn, Ende, Wahlbeschluss und Review dokumentiert werden. Der Standard-Wiederwahlrhythmus beträgt sechs Monate und steht im Mandat.

Als Verfahrensentwurf für eine offene Wahl gilt:

Rollenprofil → begründete Nominierungen → Austausch und gegebenenfalls Anpassung → Wahlvorschlag → Zustimmung der kandidierenden Person → Konsent → Besetzung.

Die genaue Legitimation der Kreisleitung durch Ober- und Unterkreis sowie der Umgang mit überfälligen Wahlen sind noch zu definieren.

### Rollen im Personenprofil

Aktive Kreisrollen werden im Profil mit ihrem Kreisbezug angezeigt:

- **AK Beispiel – Kreisleitung**
- **AK Technik – Delegierte*r**
- **SPACE ABC – Dokumentation**

Der Kreisname ist anklickbar. Eine Person kann mehrere Einträge besitzen. Sichtbarkeit richtet sich nach den Zugriffsrechten; private Kreiszugehörigkeiten werden nicht öffentlich offengelegt.

In Stufe 1 können die Angaben manuell gepflegt werden. In Stufe 2 entstehen und enden sie automatisch mit der Rollenbesetzung.

### Temporäre Themensprecher*innen

Wenn ein Kreis Wissen, Perspektive oder Abstimmung mit einem anderen Kreis benötigt, kann er eine Anfrage stellen. Der angefragte Kreis benennt eine **Themensprecher*in**, die themenbezogen und vorübergehend an den betreffenden Sitzungen teilnimmt.

Die Rolle soll Thema, Auftrag, entsendenden und empfangenden Kreis, Dauer und erforderlichen Zugang abbilden. Sie ersetzt keine dauerhafte Verbindungsrolle.

Noch offener Vorschlag: beratende Teilnahme mit Rede- und Vorschlagsrecht, aber ohne automatisch entstehendes Konsentrecht. Rechte und Ende der Teilnahme müssen ausdrücklich geregelt werden.

## 4. Kreisgröße und Teilung

- Bei **mehr als sieben zählenden Mitgliedern**, also ab acht, wird eine Teilung vorgeschlagen.
- Ab **neun zählenden Mitgliedern** ist ein Teilungsprozess verpflichtend.
- Delegierte beziehungsweise Repräsentant*innen aus Unterkreisen werden bei dieser Größengrenze nicht mitgezählt.
- Ihre Nichtberücksichtigung bei der Größe nimmt ihnen keine Konsentrechte.
- Auch Unterkreise können weitere Unterkreise bilden.

Die Struktur soll tatsächliche Mitglieder und die für die Größengrenze relevante Zahl getrennt darstellen. Der Umgang mit Personen, die zugleich eine reguläre Funktion und eine Unterkreisvertretung innehaben, ist noch zu klären.

Eine verpflichtende Teilung bedeutet keine willkürliche automatische Verteilung von Menschen oder Mandaten. Wie der Prozess ausgelöst, zeitlich begleitet und abgeschlossen wird, wird in Stufe 2 spezifiziert.

## 5. Lebenszyklus eines Kreises

### Entstehung

Ein Kreis entsteht durch Beschluss eines bestehenden Kreises, mit:

- Mandat und Verantwortung;
- einer Kreisleitung;
- mindestens einer weiteren Person;
- dokumentierter Beziehung zum Oberkreis.

Ausgangspunkt der Idee ist: Wenn eine Aufgabe, ein Projekt oder ein anderes Vorhaben über eine Person hinausgeht, soll ein Kreis entstehen.

**Noch abzustimmender Umsetzungsvorschlag:** Die gemeinsame Aufgabenübernahme bereitet automatisch einen Gründungsvorgang vor; der Beschluss legitimiert Mandat und Kreis. Die genaue Verbindung zwischen automatischem Auslöser und erforderlichem Gründungsbeschluss ist offen.

### Arbeitsphase

Der Kreis bearbeitet sein Mandat in überschaubaren Arbeitszyklen. Er entscheidet, setzt um, prüft Ergebnisse und lernt daraus.

### Pause

Der Kreis besteht weiter, arbeitet aber für einen begrenzten Zeitraum nicht aktiv. Geplant sind Angaben zu Anlass, Wiederbeginn und Umgang mit offenen Verpflichtungen. Eine Pause darf Fristen, Mandate oder Verantwortung nicht stillschweigend verschwinden lassen.

### Auflösung und Archivierung

Anlässe können sein:

- Das Mandat ist erfüllt.
- Das Mandat kann nicht erreicht werden.
- Der Kreis wird organisatorisch neu geordnet.
- Ein anderer dokumentierter Grund führt zur Auflösung.

Der Kreis wird archiviert. Offene Aufgaben, Mandate, Verantwortung, Ressourcen und verbleibende Unterkreise müssen ausdrücklich übergeben werden. Empfängerkreise werden dokumentiert; nichts wird stillschweigend herrenlos. Rückgabe an den Oberkreis ist ein Vorschlag für den Standardfall, noch keine abschließend beschlossene Automatik.

In einer höheren Ausbaustufe soll KI das erarbeitete Wissen zusammenfassen und für das Wiki des nächsthöheren Kreises aufbereiten. Menschen prüfen die Zusammenfassung; Zugriffsrechte und Quellenbezüge bleiben zu berücksichtigen.

### Schließung durch den Oberkreis

Es muss ein Verfahren geben, mit dem ein Oberkreis einen Unterkreis beispielsweise bei Verfehlungen oder anderen begründeten Anlässen schließen kann.

**Verfahrensentwurf, noch nicht vollständig beschlossen:**

1. Antrag mit Anlass und Begründung.
2. Stellungnahme des betroffenen Kreises und gegebenenfalls Möglichkeit zur Abhilfe.
3. Konsententscheidung im Oberkreis über Auflagen, Aussetzung oder Mandatsentzug und Auflösung.
4. Geordnete Übergabe von Arbeit, Mandaten und Unterkreisen.
5. Archivierung mit nachvollziehbarem Verlauf.

Offen sind insbesondere der Umgang mit Einwänden betroffener Verbindungsrollen, Interessenkonflikten und möglichen Notfallmaßnahmen. Ein Ausschluss dieser Personen vom Konsent oder ein Sonderentscheidungsrecht wird nicht stillschweigend vorausgesetzt.

## 6. Aufgaben als eigenständige Objekte

Das Aufgabenmodul soll neu gebaut oder ein vorhandenes Modul entsprechend angepasst werden.

- Aufgaben sind eigenständige Objekte.
- Sie können an Kreise delegiert werden.
- Sie können ohne Entscheidungsverfahren bestehen.
- Bei Bedarf wird ein Entscheidungsverfahren verknüpft.
- Ein Auftrag erweitert nicht automatisch das Mandat des empfangenden Kreises.

**„Aufträge“ ist ein vorgeschlagener, noch nicht festgelegter Nutzername** für das neue Modul, damit es von gewöhnlichen Aufgabenlisten unterscheidbar ist.

Noch zu prüfen ist, ob eine Neuentwicklung oder Anpassung eines vorhandenen Moduls die bessere technische Grundlage bildet.

### Ideen → Mandatsprüfung → Aufgaben (verbindlicher Umfang von M2)

Alle angemeldeten Menschen der Organisation können eine Idee erstellen und einem beliebigen Kreis zuordnen; die Mitgliedschaft im adressierten Kreis ist dafür keine Voraussetzung. Einreichung verleiht keinen Zugriff auf dessen interne Inhalte. Die konkrete adressierbare Darstellung geschützter Kreise muss ohne Offenlegung vertraulicher Inhalte gestaltet werden.

Die erste Prüfung lautet ausschließlich: **Liegt die Idee innerhalb unseres Mandats?** In diesem Schritt wird ausdrücklich nicht bewertet, ob die Idee sinnvoll, attraktiv oder erwünscht erscheint.

- **Ja:** Aus der Idee wird eine Aufgabe des zuständigen Kreises.
- **Nein:** Die Idee wird mit dokumentierter Mandatsbegründung an den nächsthöheren Kreis weitergegeben.
- Dort wird dieselbe Zuständigkeitsprüfung wiederholt. Eine Idee kann so bis zum obersten Kreis wandern.
- Ein höherer Kreis kann die Idee passend an einen zuständigen Kreis verteilen. Bereits delegierte Mandatsbereiche bleiben dabei geschützt.
- Idee, Urheberschaft und Weiterleitungsverlauf bleiben erhalten. Eine Weiterleitung ist keine inhaltliche Ablehnung.

Aus Ideen entstandene Aufgaben bilden die Arbeitsaufträge für die nächsten Sitzungen. Der Kreis bearbeitet sie über Vorschlag, Verständnisfragen, Reaktionen, Einwände und Integration, bis Konsent besteht. Nicht abgeschlossene Beratungen werden mit ihrem Stand in die nächste Sitzung übernommen. Fehlender Konsent darf nicht durch Fristablauf ersetzt werden. Ein Konsentbeschluss und die anschließende praktische Umsetzung sind getrennte Fortschritte: „beschlossen“ bedeutet noch nicht „umgesetzt“.

Sichtbare Stationen sind **Idee**, **Mandatsprüfung**, gegebenenfalls **Weitergeleitet**, **Aufgabe / für Sitzung vorgesehen**, **In Bearbeitung** mit konkreter Verfahrensphase und **Konsent / beschlossen**. Bei jeder Weiterleitung beginnt die Mandatsprüfung im empfangenden Kreis erneut.

Dieser vollständige Ablauf einschließlich einfacher Sitzungszuordnung gehört bereits zu **M2 in Ausbaustufe 2**, nicht erst zu M3 oder zur KI-Stufe. In Stufe 1 wird er zunächst methodisch beschrieben.

Noch festzulegen: wer die Mandatsprüfung im Kreis dokumentiert, wie widersprüchliche Zuständigkeitsbewertungen und Weiterleitungsschleifen aufgelöst werden und was mit Ideen außerhalb des Gesamtmandats am obersten Kreis geschieht. Eine automatische Sinnhaftigkeitsprüfung oder automatische Ablehnung wird daraus nicht abgeleitet.

## 7. Entscheidungsfindung und Arbeitszyklus

Der methodische Kern lautet:

**Vorschlag → Verständnisfragen → Reaktionen → Einwände → Integration → Konsent**

Im Interface verwenden wir „Konsent“; englisch „consent“. Das Verfahren ist keine Mehrheitsabstimmung.

| Schritt | Unterstützung |
|---|---|
| Bedarf und Vorschlag | Problem, Ziel und konkreten Vorschlag formulieren. |
| Verständnisfragen | Unklarheiten klären. |
| Reaktionen | Perspektiven und Bedenken sammeln. |
| Einwände | Begründete Einwände zum Vorschlag sichtbar machen. |
| Integration | Einwände bearbeiten und Änderungen dokumentieren. |
| Konsent | Ergebnis zur konkreten Vorschlagsversion ausdrücklich festhalten. |
| Umsetzung | Verantwortliche handeln im beschlossenen Rahmen. |
| Review | Wirkung prüfen und gegebenenfalls einen neuen Vorschlag entwickeln. |

Integration ist erforderlich, wenn Einwände beziehungsweise Änderungen vorliegen. Bei geändertem Vorschlag wird erneut auf Konsent geprüft. Die Oberfläche darf kein starres Fortschrittsbild zeigen, das Rücksprünge oder Wiederholungen verschweigt.

### Inhalt jedes Beschlusses

Jeder Beschluss enthält:

- **WER** übernimmt Verantwortung?
- **WAS** wird getan oder ermöglicht?
- **Bis WANN** erfolgt Umsetzung beziehungsweise Überprüfung?
- **WARUM** dient dies dem Ziel?
- **MANDAT:** Welche Befugnisse und Grenzen gelten?

Zusätzlich vorgesehen: aktuelle Textversion, zuständiger Kreis, Verfahrensverlauf, Gültigkeit und gegebenenfalls Erfolgskriterien.

### Geplante Verfahrensregeln

- Fehlende Rückmeldung oder Fristablauf ist kein automatischer Konsent.
- Offene Einwände müssen bearbeitet werden.
- Rückmeldungen beziehen sich auf eine konkrete Vorschlagsversion.
- Textänderungen erfordern eine neue Konsentprüfung; alte Rückmeldungen werden nicht stillschweigend übernommen.
- Die entscheidungsberechtigte Gruppe und der Umgang mit Mitgliedschaftsänderungen müssen klar definiert sein.
- Moderation erhält keine Befugnis, Einwände einseitig verschwinden zu lassen.

Die genaue Teilnehmerregel und mögliche alternative Quoren sind noch zu beschließen. Das bisherige Konzept sieht explizite Rückmeldungen aller für die Runde festgelegten Berechtigten vor.

### Prozessvarianten

Geplant sind erkennbare Varianten für Sachentscheidungen, Rollenwahlen, Mandatsänderungen sowie Auflösungsverfahren. Die jeweiligen Schritte, Übergangsrechte und Abschlussbedingungen werden vor der Automatisierung spezifiziert.

Ein Community-Beitrag kann in M2 als Idee eingereicht werden und durchläuft den oben beschriebenen Weg über Mandatsprüfung, Aufgabe und Konsent.

## 8. Grafische Orientierung

Die grafische Darstellung ist eine Kernfunktion des Projekts.

| Ansicht | Leitfrage |
|---|---|
| Kreislandkarte | Welche Kreise gibt es, wie sind sie verbunden und welches Mandat besitzen sie? |
| Kreis-Startseite | Was ist unser Auftrag, wer trägt welche Rolle und was steht an? |
| Aufgabenübersicht | Was liegt bei welchem Kreis und wo fehlt eine Entscheidung? |
| Entscheidungsansicht | Wo stehen wir, was fehlt, wer handelt als Nächstes? |
| Rollen im Profil | Welche Verantwortung übernimmt diese Person in welchem Kreis? |

Eine Kreis-Startseite soll Mandat, Rollen, Ober- und Unterkreise, laufende Entscheidungen, offene Einwände, anstehende Reviews und Sitzungen zusammenführen.

Die Entscheidungsansicht zeigt mindestens:

- Verfahrensvariante und aktuelle Vorschlagsversion;
- aktuellen Schritt;
- offene Fragen und Einwände;
- nächste Handlung und zuständige Personen;
- bisherigen Verlauf einschließlich Wiederholungen.

In Stufe 1 handelt es sich überwiegend um feste Erklärungen und grafische Ablaufhilfen. Eine automatisch fortgeschriebene Live-Prozessanzeige gehört zu Stufe 2. Warnungen müssen neben Farben auch Text oder Symbole besitzen.

## 9. Sitzungen und Dokumentation

Vorgesehen sind:

- Online-Sitzungen innerhalb des internen Systems.
- Präsenzsitzungen mit einer Diktier-App als möglicher Dokumentationshilfe.
- Agenda, Runden, Protokolle, Beschlüsse und Umsetzungsaufgaben.
- Später KI-gestützte Protokollentwürfe.

Eine konkrete Konferenz-, Aufnahme- oder KI-Lösung ist noch nicht ausgewählt. Aufzeichnung und Verarbeitung müssen für Teilnehmende transparent und passend freigegeben sein. Ein Transkript ist nicht automatisch ein gültiger Beschluss; Beschlüsse werden ausdrücklich bestätigt.

## 10. Drei Ausbaustufen

### Stufe 1 — Orientierung und grundlegende Kreise

Zuerst wird das Modell sichtbar und verständlich. Keine komplexen Entscheidungswerkzeuge, Abhängigkeiten oder Governance-Automatismen.

Vorgesehener Umfang:

- optische Unterscheidung allgemeiner Spaces und Arbeitskreise;
- einfache, manuell gepflegte Kreisstruktur;
- Mandatsbeschreibung;
- feste Texte zu Rollen, Doppelrollen, Wahlen und Zusammenarbeit;
- grafische Beschreibung des Entscheidungsverfahrens;
- Vorlagen mit Wer, Was, Wann, Warum und Mandat;
- Anleitungen für Gründung, Teilung, Pause, Auflösung und Übergabe;
- manuell gepflegte Rollenangaben im Personenprofil.

Grundlegende Zugriffsrechte müssen trotzdem funktionieren. „Keine komplexe Logik“ bedeutet nicht, private Inhalte ungeschützt anzuzeigen.

**Erfolgskriterium:** Eine neue Person versteht, wie der Kreis arbeitet, wofür er zuständig ist und wie sie handeln kann.

### Stufe 2 — Gezielte Werkzeuge und Automatisierung

Die Reihenfolge ergibt sich aus Erfahrungen mit Stufe 1. Mögliche Bausteine:

- Ideen für alle, Mandatsprüfung, nachvollziehbare Weiterleitung und Umwandlung in eigenständige Aufgaben einschließlich Sitzungsbearbeitung bis Konsent (M2);
- strukturierte Rollen, Amtszeiten, Wahlen und automatische Profileinträge;
- Doppelrollenwarnungen und Größenhinweise;
- geführte Entscheidungsverfahren mit Versionen und Einwandintegration;
- Mandatsversionierung und Unterstützung bei Zuständigkeitsprüfungen;
- automatische Bereitstellung von Kreisfunktionen;
- Gründung, Teilung, Pause und Auflösung als begleitete Vorgänge;
- themenbezogene Sprecher*innen;
- Erinnerungen, Reviews, Beschlussregister und berechtigungsgeschützte Exporte.

**Erfolgskriterium:** Werkzeuge erleichtern nachweislich wiederkehrende Arbeit und bilden die vereinbarten Regeln zuverlässig ab.

### Stufe 3 — KI-Unterstützung

Mögliche Funktionen:

- Protokollentwürfe aus Online- oder Präsenzsitzungen;
- Zusammenfassung und Übertragung von Wissen bei Archivierung;
- Unterstützung beim Formulieren und Strukturieren von Vorschlägen;
- Hinweise auf mögliche Mandatskonflikte;
- Unterstützung beim Wiederfinden vorhandener Beschlüsse.

KI unterstützt. Sie erteilt keinen Konsent, entscheidet nicht selbstständig über Mandatsverletzungen und veröffentlicht keine ungeprüften Beschlüsse.

## 11. Technische Leitplanken

- Eigenständiges HumHub-Modul auf PHP / Yii2, ohne Core-Patches.
- Space-Zugriff und organisatorische Entscheidungsrechte werden getrennt geprüft.
- Organisatorische Gesamtverantwortung ist nicht gleich technischer Vollzugriff.
- Keine fest eingebauten Personen oder organisationsspezifischen Sonderkonten.
- Serverseitige Berechtigungsprüfung, CSRF-Schutz und sichere Textausgabe.
- Später versionierte Datenbankmigrationen und transaktionaler Beschlussabschluss.
- Historie von Beschlüssen und Mandaten; keine behauptete Manipulationssicherheit ohne entsprechende Maßnahmen.
- Deaktivierung darf Governance-Daten nicht unbeabsichtigt löschen.
- Archivierung, endgültige Löschung und Aufbewahrung sind unterschiedliche Vorgänge.

## 12. Plugin-Versionierung

Wir verwenden **a.b.ccc**, technisch beispielsweise **1.4.127**:

| Teil | Bedeutung |
|---|---|
| a | Hauptversion: große Änderungen, insbesondere Brüche der Datenkompatibilität |
| b | Neue grundlegende Funktionen |
| ccc | Revisionen und Korrekturen im Entwicklungsprozess |

In der Datei `module.json` steht die Version ohne „V“. Eine Anzeige beziehungsweise ein Git-Tag kann das Präfix verwenden.

Revisionen dürfen Nummern überspringen, müssen für aufeinanderfolgende Veröffentlichungen aber eindeutig und aufsteigend sein. Führende Nullen werden vermieden. Beim Erhöhen einer höheren Stelle können untergeordnete Stellen zurückgesetzt werden.

Versionsnummern ersetzen keine Datenbankmigrationen und keine dokumentierten Upgradepfade. Dieses Schema ist unsere Projektkonvention innerhalb des von HumHub dokumentierten Formats X.Y.Z.

## 13. Aktueller Repository- und Teststand

Vorhanden sind `Module.php`, `config.php`, `module.json` und Konzeptdokumente. Die Metadaten nennen Version **0.0.1** und HumHub **1.18** als Entwicklungsbasis. Die Kompatibilität wurde noch nicht in einer HumHub-Installation geprüft.

Für eine spätere Testinstallation liegt das Modulverzeichnis als `sociocratic-governance` in einem konfigurierten HumHub-Modulpfad. Das vorhandene Gerüst registriert lediglich das Modul und ist keine einsatzfähige Governance-Anwendung.

Bisher geprüft: PHP-Syntax von `Module.php` und `config.php`. Ausstehend: HumHub-Integration, Installation, Migrationen, Oberflächen und funktionale Tests.

Spätere Prüfungen müssen insbesondere abdecken:

- Schutz zwischen Kreisen und Sichtbarkeit im Profil;
- verbotene Personalunion von Kreisleitung und Delegiertenrolle;
- Fristen, Mitgliedschafts- und Versionswechsel;
- offene Einwände und fehlende Rückmeldungen;
- konkurrierende Beschlussabschlüsse;
- Mandatsübergaben und Archivierung;
- Installation, Updates sowie Backup und Wiederherstellung.

## 14. Offene Entscheidungen

1. Legitimation und Änderung des Mandats des obersten Kreises.
2. Berechtigungen und Verfahren für dauerhafte Mitgliedschaften im Backend.
3. Genaue Wahl- und Bestätigungsregeln für die Kreisleitung.
4. Umgang mit abgelaufenen Amtszeiten und nicht abgeschlossenen Wiederwahlen.
5. Zusammenspiel von automatischem Gründungsanlass und Gründungsbeschluss.
6. Detailregeln zur Mitgliederzählung und verpflichtenden Teilung.
7. Beteiligung des Unterkreises an Änderungen seines Mandats.
8. Rechte der temporären Themensprecher*innen.
9. Auflösungsverfahren bei Einwänden, Interessenkonflikten und Notfällen.
10. Folgen einer Revision für bereits ausgeführte Beschlüsse.
11. Konkrete HumHub-Version, Testinstanz und technische Integrationen.
12. Endgültiger Name und technische Grundlage des neuen Aufgabenmoduls.
13. Aufbewahrung, Löschung, Aufnahmen und KI-Verarbeitung.
14. Open-Source-Lizenz vor dem ersten Release.

## 15. Dokumentation und Mitarbeit

Dieses README ist der aktuelle zusammengeführte Konzeptstand. Die älteren Detaildokumente sind Startentwürfe und noch nicht mit allen späteren Festlegungen synchronisiert. Bei Widersprüchen gilt der hier ausdrücklich als festgelegt beschriebene Stand; offene Vorschläge bleiben offen.

- [Älterer Fachkonzeptentwurf](docs/GOVERNANCE.md)
- [Architekturentwurf](docs/ARCHITECTURE.md)
- [Frühere technische Roadmap](docs/ROADMAP.md) — künftig den drei Ausbaustufen zuzuordnen
- [Mitwirken](CONTRIBUTING.md)

Beiträge sollen konkrete Praxisprobleme lösen, universell formulierbar sein und den Unterschied zwischen beschlossener Regel, Vorschlag und implementierter Funktion erhalten.

## Referenzen

- [HumHub-Modulentwicklung und Versionsformat](https://docs.humhub.org/docs/develop/modules/)
- [Offizielles HumHub-Beispielmodul](https://github.com/humhub/example-basic)
- [Doppelte Verknüpfung — Soziokratie Zentrum](https://soziokratiezentrum.org/ueber-soziokratie/grundlagen-der-soziokratie-4-basisprinzipien/doppelte-verknupfung/)
- [Kreisrollen — Sociocracy For All](https://www.sociocracyforall.org/process-roles/)
- [Double linking — Sociocracy For All](https://www.sociocracyforall.org/double-linking/)

## Lizenz

Das Repository ist öffentlich. Eine konkrete Open-Source-Lizenz wurde noch nicht festgelegt. Öffentliche Sichtbarkeit allein erteilt keine allgemeine Nutzungslizenz. Lizenzwahl und vollständiger Lizenztext müssen vor dem ersten Release ergänzt werden.
