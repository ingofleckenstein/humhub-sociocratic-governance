# Sociocratic Governance for HumHub

Eine universell nutzbare Governance-Erweiterung für HumHub: Kreise sichtbar machen, Zusammenarbeit methodisch unterstützen und Menschen innerhalb klarer Mandate zum Handeln ermächtigen.

**Status: Version 0.1.0 – erste Implementierung der Ausbaustufe 1, bereit für den Installationstest.**
Arbeitskreis-Ansichten, Mandatspflege, vier manuell besetzbare Kreisrollen, Profilanzeige und Backend-Konfiguration sind implementiert. Die vollständige HumHub-/MySQL-Erprobung auf der Testinstallation steht noch aus.

- [Installation und Abnahme](docs/INSTALLATION.md)
- [Manuelles Deployment](docs/DEPLOYMENT.md)
- [Änderungen](docs/CHANGELOG.md)
- [Testumfang und Grenzen](tests/README.md)

Die folgenden Abschnitte beschreiben das gesamte Zielmodell. Funktionen der Stufen 2 und 3 sind weiterhin Planung.

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

Eine dauerhafte Mitgliedschaft ist von einer befristeten Rollenbesetzung zu unterscheiden. Sie verleiht nicht automatisch technische Administrationsrechte oder ein persönliches Vetorecht außerhalb des vereinbarten Konsentverfahrens. Die im Backend ausdrücklich bestimmte Admin-Sonderrolle entscheidet über dauerhafte Mitgliedschaften sowie Legitimation und Änderung des Mandats des obersten Kreises. Diskussionen, Konsultationen und Abstimmungen sind möglich, binden diese Entscheidung aber nicht. Diese Befugnis ist personenbezogen konfigurierbar und wird nicht pauschal allen technischen Administrator*innen zugewiesen.

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

### Standard-Zugriffsmodell

Die vorgesehene HumHub-Installation erlaubt **keine Gäste**. Zugriff setzt ein registriertes, angemeldetes Nutzerkonto voraus.

Für Arbeitskreise gelten standardmäßig:

- **Lesen:** Alle angemeldeten Nutzer*innen der Installation können die Kreis-Inhalte lesen.
- **Schreiben:** Nur Mitglieder des jeweiligen Kreises können dort Inhalte erstellen und bearbeiten. Detailrechte wie die Bearbeitung fremder Inhalte bleiben gesondert geregelt.
- **Konsent:** Schreibrecht allein erteilt kein Konsentrecht; Rollen und verfahrensbezogene Ausschlüsse gelten zusätzlich.
- **Ideen einreichen:** Die ausdrücklich vereinbarte organisationsweite Einreichung einer Idee an einen beliebigen Kreis bleibt eine gezielte Ausnahme. Nichtmitglieder erhalten dadurch keine allgemeinen Schreibrechte im Kreis.
- **Themensprecher*innen:** Ihre Zuordnung verleiht weiterhin keine zusätzlichen allgemeinen Schreibrechte.
- **Abweichungen:** Organisationsweite Lesbarkeit ist die Voreinstellung. Falls ein Kreis ausdrücklich eingeschränkte Sichtbarkeit erhält, müssen diese Rechte auch in Profilen, Historien und Benachrichtigungen gelten.

Das universelle Plugin beschreibt und unterstützt diese Voreinstellungen. Der Ausschluss von Gästen ist eine installationsweite HumHub-Einstellung; diese Dokumentation behauptet keine technische Prüfung oder Änderung der laufenden Installation.

## 2. Mandate und verteilte Entscheidungshoheit

Ein Mandat beschreibt mindestens:

- Zweck und angestrebte Wirkung;
- Zuständigkeits- und Entscheidungsbereich;
- Verantwortung, Befugnisse und Grenzen;
- gegebenenfalls Budget und Ressourcen;
- Wiederwahlrhythmus des Kreises, standardmäßig **sechs Monate**;
- gegebenenfalls Gültigkeit, Reviewtermine und Erfolgskriterien.

Der Oberkreis definiert das Mandat des Unterkreises. Änderungen benötigen Konsent im Oberkreis. Der betroffene Unterkreis wird konsultativ beteiligt. Die Entscheidung erfolgt im Oberkreis. Zwei Verfahrensmodi werden unterschieden: Bei einem regulären Antrag des betroffenen Kreises bleibt dessen delegierte Person konsentberechtigt. Bei einer Krisenintervention im Konfliktfall wird nur die delegierte Person des betroffenen Kreises ausgeschlossen, nicht die Delegierten anderer Kreise. Der Ausschluss gilt ausschließlich für dieses Verfahren. Für eine Kreisauflösung gilt die gesonderte weitergehende Ausschlussregel unten.

Bei einer Teilung beziehungsweise Unterkreisgründung geht ein Teil des Mandats an den neuen Kreis über. Damit gehen echte Entscheidungsbefugnisse innerhalb dieses Bereichs an den Unterkreis.

### Gültigkeit und Prüfung von Beschlüssen

- Ein Beschluss ist **ab Konsent gültig**.
- Er wird dem unmittelbar übergeordneten Kreis zur Mandatsprüfung vorgelegt.
- Der Oberkreis prüft ausschließlich, ob der Beschluss innerhalb des Mandats liegt.
- Beschließt der Oberkreis, dass eine Mandatsüberschreitung vorliegt, kann er den Beschluss revidieren.
- Betroffene Mandatsstelle, Begründung und Revisionsbeschluss sollen nachvollziehbar dokumentiert werden.

Dies ist keine allgemeine Vorabgenehmigung aller Unterkreisentscheidungen. Folgen einer Revision richten sich nach der Schwere: von keinen weiteren Maßnahmen bis zur Kreisauflösung. Für Korrektur, Übergabe und Folgen ist zuerst der verursachende Kreis verantwortlich; kann dieser die Verantwortung nicht übernehmen, übernimmt der Oberkreis. Grundsatz: Aus Macht folgt Verantwortung. Krisensitzungen behandeln die konkreten Folgen. Bei Handeln ohne Berechtigung kann auch der Ausschluss einzelner Mitglieder Gegenstand einer Entscheidung sein. Verfahren, Anhörung, Entscheidungsrechte und konkrete technische Gestaltung des Krisenwerkzeugs sind noch auszuarbeiten; es gibt keinen automatischen Personenausschluss.

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

Die Kreisleitung wird nach dem normalen Konsentprinzip legitimiert: Bei der Gründung wählt der Oberkreis die erste Kreisleitung. Besteht der Kreis bereits, wählt er seine Kreisleitung selbst. Die gewählte Person wird automatisch Mitglied des Oberkreises. Die Mitgliedschaft ist mit der Rollenbesetzung zu verknüpfen; beim Rollenende müssen zusätzliche unabhängige Mitgliedschaftsgründe berücksichtigt werden. Ist eine Amtszeit abgelaufen und die Wiederwahl nicht abgeschlossen, wird der Kreis aufgelöst und an den Oberkreis zurückgeführt; es gibt keine stillschweigende Amtszeitverlängerung. Die Rückführung umfasst Mandat und offene Verantwortung, nicht die Löschung der Historie. Für den obersten Kreis kann im Backend eine dauerhafte Kreisleitung ohne turnusmäßiges Amtszeitende eingerichtet werden. Sie bleibt bis zur Abgabe, zum Austritt oder einem anderen ausdrücklich geregelten Ende bestehen. Darüber kann eine Trägerorganisation mit verantwortlicher Leitung als organisatorische Instanz hinterlegt werden; diese muss kein weiterer HumHub-Arbeitskreis sein. Die Nachfolge bei Wegfall der dauerhaften Leitung ist noch zu regeln.

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

Themensprecher*innen nehmen an themenbezogenen Sitzungen teil und werden dem empfangenden Kreis mit klarer Rollenbenennung als temporär zugeordnet angezeigt. Daraus entstehen keine besonderen HumHub-Rechte. Erforderlicher Sitzungszugang ist gezielt zu ermöglichen, ohne pauschalen Zugriff auf interne Kreisinhalte. Ein eigenes Konsentrecht wird durch diese Rolle nicht gewährt.

## 4. Kreisgröße und Teilung

- Bei **mehr als sieben zählenden Mitgliedern**, also ab acht, wird eine Teilung vorgeschlagen.
- Ab **neun zählenden Mitgliedern** ist ein Teilungsprozess verpflichtend.
- Delegierte beziehungsweise Repräsentant*innen aus Unterkreisen werden bei dieser Größengrenze nicht mitgezählt.
- Ihre Nichtberücksichtigung bei der Größe nimmt ihnen keine Konsentrechte.
- Auch Unterkreise können weitere Unterkreise bilden.

Die Struktur soll tatsächliche Mitglieder und die für die Größengrenze relevante Zahl getrennt darstellen. Gezählt werden Personen mit einer eigenen Rolle oder Mitarbeit im betrachteten Kreis, einschließlich seiner Kreisleitung und seiner eigenen Delegiertenrolle. Auch Personen, die von diesem Kreis als Kreisleitung in Unterkreise gehen, zählen hier mit. Jede Person zählt einmal, unabhängig von der Zahl ihrer Rollen. Die bisherige Ausnahme für reine Delegierte aus Unterkreisen bleibt bestehen; eine eigene Rolle oder Mitarbeit im betrachteten Kreis führt zur Mitzählung.

Eine verpflichtende Teilung bedeutet keine willkürliche automatische Verteilung von Menschen oder Mandaten. Wie der Prozess ausgelöst, zeitlich begleitet und abgeschlossen wird, wird in Stufe 2 spezifiziert.

## 5. Lebenszyklus eines Kreises

### Entstehung

Ein Kreis entsteht durch Beschluss eines bestehenden Kreises, mit:

- Mandat und Verantwortung;
- einer Kreisleitung;
- mindestens einer weiteren Person;
- dokumentierter Beziehung zum Oberkreis.

Ausgangspunkt der Idee ist: Wenn eine Aufgabe, ein Projekt oder ein anderes Vorhaben über eine Person hinausgeht, soll ein Kreis entstehen.

Die Gründung ist ein Sonderfall des zentralen Aufgabensystems: **Idee → Aufgabe → Bearbeitung eines Gründungsvorschlags → Gründungsbeschluss → Kreis mit Mandat**. Ein Anlass führt nicht ohne Beschluss zur automatischen Kreisgründung.

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

Bei der Entscheidung über die Auflösung eines Kreises haben alle Personen aus diesem betroffenen Kreis kein Stimm- beziehungsweise Konsentrecht, ausdrücklich auch dessen Kreisleitung und Sprecher*innen. Dieser Ausschluss gilt verfahrensbezogen, nicht für andere Entscheidungen. Anhörung bleibt vom Konsentrecht getrennt. Das übrige Verfahren bei Interessenkonflikten, Notfällen oder einer nicht entscheidungsfähigen verbleibenden Gruppe ist noch zu konkretisieren.

## 6. Aufgaben als eigenständige Objekte

Das Vorhabensystem wird selbst entwickelt. Bestehende Aufgabenmodule werden nicht übernommen oder abgeändert.

- Aufgaben sind eigenständige Objekte.
- Sie können an Kreise delegiert werden.
- Sie können ohne Entscheidungsverfahren bestehen.
- Bei Bedarf wird ein Entscheidungsverfahren verknüpft.
- Ein Auftrag erweitert nicht automatisch das Mandat des empfangenden Kreises.

Der gewählte Nutzername lautet **„Vorhaben“**. Ein Vorhaben führt von der Idee über Mandatsprüfung, Aufgabe und Konsent zur Umsetzung und zum Review. „Aufgabe“ bleibt die Bezeichnung des konkreten Arbeitsauftrags innerhalb dieses Verlaufs. Das Vorhabensystem ist ein zentraler Bestandteil neben Kreisstruktur und Entscheidungsverfahren.

Technische Richtung: eine eigene Domäne innerhalb der Governance-Erweiterung auf Basis von HumHub/Yii2. Ein vorhandenes Aufgabenmodul wird nicht zur vorausgesetzten Grundlage, da Vorhaben kreisübergreifende Zuständigkeiten, Konsent und eine durchgehende Historie benötigen. Die genaue HumHub-Anbindung wird bei der Implementierung geprüft. Fremder Modul- oder Anwendungscode wird nicht übernommen; bestehende Lösungen können als konzeptionelle Inspiration dienen. Die vorgesehenen HumHub-/Yii2-Schnittstellen bleiben die technische Plattform.

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

Noch festzulegen: wer die Mandatsprüfung im Kreis dokumentiert, wie widersprüchliche Zuständigkeitsbewertungen und Weiterleitungsschleifen aufgelöst werden und wie die Entscheidung über das Gesamtmandat dokumentiert wird. Festgelegt ist: Ideen außerhalb des Gesamtmandats passen nicht zum Zweck der Organisation und können dort nicht bearbeitet werden. Sie erhalten einen begründeten Abschluss „Außerhalb des Gesamtmandats – nicht bearbeitbar“; Idee und Historie bleiben erhalten. Das ist eine Zuständigkeitsentscheidung, keine Sinnhaftigkeitsbewertung. Eine automatische Sinnhaftigkeitsprüfung oder automatische Ablehnung wird daraus nicht abgeleitet.

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

### Durchgehende Änderungshistorie (M2)

Alle Änderungen von der ursprünglichen Idee über die Aufgabe bis zum Konsent werden dokumentiert. Jede Änderung enthält:

- **Zeitpunkt:** Datum und Uhrzeit.
- **Space / Kreis:** Der Kontext, in dem die Änderung vorgenommen wurde.
- **Autor*in:** Die Person, die die Änderung vorgenommen hat.
- **Änderung:** Was geändert wurde, mit vorherigem und neuem Stand beziehungsweise einer nachvollziehbaren Änderungsbeschreibung.

Dies umfasst insbesondere Titel und Text, Zuständigkeit und Weiterleitung, Verantwortliche, Termine, Mandatsbezug, Status, Vorschlagsversionen sowie Einwände und deren Bearbeitung. Bei einem Kreiswechsel bleiben Ausgangs- und Zielkreis nachvollziehbar. Auch kleine Bearbeitungen werden erfasst.

Die Historie bleibt über die Umwandlung von Idee zu Aufgabe und bis zum Beschluss zusammenhängend erhalten. Spätere Bearbeitungen überschreiben frühere Einträge nicht stillschweigend. Historische Angaben zum damaligen Space und zur handelnden Person dürfen nicht durch spätere Zuordnungsänderungen verfälscht werden. Automatisierte Änderungen werden ausdrücklich als Systemaktion gekennzeichnet.

Die Änderungshistorie unterliegt den Zugriffsrechten der Inhalte und macht private Kreisinhalte nicht automatisch öffentlich. Aufbewahrung und datenschutzgerechte Löschung bleiben gesondert zu spezifizieren. Diese Historie ist verbindlicher Umfang von M2; damit wird keine technische Manipulationssicherheit behauptet.

### SMART als Hilfe zur Beschlussformulierung

Die Beschlussvorlage verbindet Wer, Was, Wann, Warum und Mandat mit **SMART**:

- **Spezifisch:** Was soll konkret erreicht oder ermöglicht werden? Zuständigkeit und Umfang benennen.
- **Messbar:** Woran erkennen wir das Ergebnis? Auch qualitative, überprüfbare Kriterien sind möglich.
- **Attraktiv / akzeptiert:** Warum ist das Vorhaben sinnvoll und für die Verantwortlichen tragbar? Zustimmung zur Verantwortungsübernahme klären.
- **Realistisch:** Sind Zeit, Fähigkeiten, Ressourcen und Mandatsbefugnisse vorhanden?
- **Terminiert:** Umsetzungstermin und passenden Reviewzeitpunkt festlegen.

SMART unterstützt verständliche und überprüfbare Beschlüsse. Es ersetzt keinen Konsent
und ist keine vorgelagerte Sinnhaftigkeitsbewertung eingereichter Ideen. Diese werden
zuerst ausschließlich auf Zuständigkeit geprüft.

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

### Datenvorgaben und installationsbezogene Konfiguration

Für die zunächst geplante Installation gilt als gewünschte Konfiguration:

- **Aufbewahrung:** unbefristet, zunächst keine automatische Löschfrist.
- **Lesbarkeit von Sitzungsprotokollen und Aufnahmen:** alle Mitglieder der Organisation. Dies bedeutet keine anonyme Veröffentlichung im Internet und keine allgemeinen Löschrechte für alle.
- **Externe KI-Verarbeitung:** grundsätzlich vorgesehen und vom Projektverantwortlichen gewünscht. Dies ersetzt weder die transparente Freigabe einer konkreten Aufnahme durch die Beteiligten noch die Auswahl und Konfiguration eines konkreten Dienstes.

Das universelle Plugin bildet diese Vorgaben als ausdrücklich wählbare Einstellungen ab. Bestehende private Space-Inhalte werden dadurch nicht stillschweigend öffentlich. Aufnahme, Protokoll, Änderungshistorie und allgemeine Space-Inhalte benötigen unterscheidbare Sichtbarkeitsregeln.

### Projektbezogene Entwicklungsumgebung

- Produktivinstallation: https://community.selbstsein.events
- Testinstallation: https://testcommunity.selbstsein.events
- Betreiberangabe: **HumHub Community Edition 1.18.5**. Ziel dieser Entwicklung; noch kein Nachweis eines bestandenen Plugin-Integrationstests.

Diese Adressen sind Betriebsangaben für das konkrete Entwicklungsprojekt, keine fest eingebauten Plugin-Adressen. Entwicklung und Tests erfolgen auf der Testinstallation; diese Dokumentationsänderung nimmt keine Änderung an den Installationen vor.

Backups und Testkonten sind laut Betreiber vorhanden. Das Deployment startet der Betreiber manuell per SSH; kein dauerhafter Entwicklungszugang ist erforderlich. Siehe [Deployment-Anleitung](docs/DEPLOYMENT.md) und [Bash-Skript](scripts/deploy-test.sh).

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

**Version 0.1.0, Entwicklungsziel HumHub Community Edition 1.18.5.**

Implementiert:

- Space-Modul mit Kennzeichnung „Arbeitskreis“, Navigationslink und Seitenleistenkarte.
- Kreisprofil mit Zweck, Mandat und Oberkreis; sichtbare Kreisübersicht und Unterkreislinks.
- Vier manuell besetzbare Rollen: Kreisleitung, Delegierte*r, Moderation und Dokumentation.
- Profileinträge für aktuelle Kreismitglieder; private Kreise und deaktivierte Rollen werden nicht offengelegt.
- Backend für Kernkreis, zuständige Admin-Sonderrolle, Trägerorganisation und dokumentierte dauerhafte Mitgliedschaften.
- Grafische Konsent-Anleitung, SMART-Beschlussvorlage und Erläuterung des Kreislebens.
- Eigene Datenbanktabellen; Schutz vor Kreiszyklen, unzulässiger Personalunion und veralteten Formularständen.

Bewusste Grenzen der ersten Fassung:

- Keine automatischen Wahlen, Amtszeitaktionen, Kriseneingriffe oder Kreisteilungen.
- Dauerhafte Mitgliedschaften werden dokumentiert, nicht technisch gegen Austritt erzwungen.
- Rollenänderungen erzeugen noch keine automatische Mitgliedschaft im Oberkreis.
- Keine Vorhabenobjekte, Sitzungsverwaltung oder vollständige Änderungshistorie; diese folgen in M2.
- Vorhandene Space-Sichtbarkeit bleibt erhalten. Organisationsweite Lesbarkeit muss im Space eingestellt sein.
- Die eigene Kreisübersicht kennzeichnet Arbeitskreise; das allgemeine HumHub-Space-Verzeichnis wird nicht ersetzt.
- Deutsche Oberfläche; weitere Sprachen folgen später.
- Bei Deaktivierung bleiben Modultabellen erhalten. Endgültiges Löschen eines HumHub-Spaces entfernt über Fremdschlüssel dessen Moduldatensätze; für Wissenserhalt archivieren statt löschen.

Lokal geprüft: PHP-Syntax, isolierte Yii-/SQLite-Komponententests, gerenderte Ansichten mit CSRF-Feldern sowie Klassen-/Eventkompatibilität gegen HumHub 1.18.5.
Noch ausstehend: vollständige Installation, MySQL-/MariaDB-Migration und Sperrverhalten, Layout im echten HumHub-Theme sowie End-to-End-Prüfung mit den vorhandenen Testkonten.
Siehe [Testbeschreibung](tests/README.md).

## 14. Entscheidungsstand und verbleibende Fragen

### Geklärte Grundentscheidungen

- Oberstes Mandat und dauerhafte Mitgliedschaften: abschließende Entscheidung der ausdrücklich konfigurierten Admin-Sonderrolle; Konsultationen sind möglich.
- Kreisleitung: normales Konsentverfahren.
- Abgelaufene Amtszeit ohne abgeschlossene Wiederwahl: Auflösung und Rückführung an den Oberkreis.
- Gründung: Sonderfall des Vorhabensystems, von Idee und Aufgabe zum Gründungsbeschluss.
- Mandatsänderung: regulärer Antrag des betroffenen Kreises mit Konsentrecht seiner delegierten Person; Krisenintervention im Konfliktfall unter Ausschluss nur dieser delegierten Person. Kreisauflösung hat eigene weitergehende Ausschlussregeln.
- Themensprecher*innen: sichtbare temporäre Zuordnung, Teilnahme an Themensitzungen, keine zusätzlichen allgemeinen HumHub-Rechte.
- Revisionsfolgen: nach Schwere bis zur Kreisauflösung; bei Auflösungsentscheidungen keine Konsentrechte für Personen des betroffenen Kreises.
- Zentraler Nutzerbegriff: **Vorhaben**; eigene fachliche Grundlage in der Governance-Erweiterung.
- Lizenz: GNU AGPL Version 3 (AGPL-3.0-only); Nutzung, Änderung und Weitergabe einschließlich kommerzieller Nutzung unter den Lizenzbedingungen.

### Ergänzende Festlegungen

- Mitgliederzählung: eigene Rollen und Mitarbeit zählen, einschließlich Kreisleitung, eigener Delegiertenrolle und in Unterkreise gehender Kreisleitungen. Personen werden einmal gezählt; reine Vertretung aus einem Unterkreis bleibt gemäß bisheriger Ausnahme ungezählt.
- Oberste Leitung: konfigurierbare dauerhafte Besetzung bis Abgabe, Austritt oder anderem geregeltem Ende; eine übergeordnete Trägerorganisation kann hinterlegt werden. Keine Person oder Firma wird fest eingebaut.
- Eigenentwicklung: keine Übernahme fremden Modul- oder Anwendungscodes. HumHub und seine vorgesehenen Schnittstellen bleiben die Plattform; andere Lösungen dürfen konzeptionell inspirieren.
- Ideen außerhalb des Gesamtmandats können nicht bearbeitet werden und werden mit Mandatsbegründung abgeschlossen.

### Noch zu entscheiden beziehungsweise zu präzisieren

1. Krisenwerkzeug: Wer setzt den Krisenmodus, wie werden Gründe, Anhörung und verfahrensbezogene Ausschlüsse dokumentiert?
2. Mitgliedschaft nach Rollenende: zusätzliche Mitgliedschaftsgründe und Zugriff beim Wechsel der Kreisleitung erhalten beziehungsweise beenden.
3. Notfälle und Interessenkonflikte: Der Bedarf einer Regel ist bestätigt. Wer entscheidet konkret, wenn nach Ausschluss betroffener Personen keine Konsentberechtigten verbleiben?
4. Krisensitzungen: konkrete Abläufe für Korrektur, Mitgliederausschluss und Übernahme durch den Oberkreis.
5. Integration auf der angegebenen HumHub Community Edition 1.18.5 testen; konkrete Sitzungs-, Wiki- und spätere KI-Anbindung festlegen.
6. Datenkonfiguration: technische Umsetzung organisationsweiter Lesbarkeit, gesonderte Löschrechte und Auswahl des externen KI-Dienstes. Die Aufbewahrung ist zunächst unbefristet.
7. Widersprüchliche Mandatsprüfungen: Wer löst den Fall, wenn Kreise eine Idee wiederholt zurückverweisen? Ideen außerhalb des Gesamtmandats sind bereits geregelt.
8. Nachfolge bei Abgabe oder Austritt der dauerhaften obersten Kreisleitung.
9. Praktischer Ablauf und Fristen einer verpflichtenden Kreisteilung.

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

Dieses Projekt steht unter der **GNU Affero General Public License, Version 3** (**SPDX: AGPL-3.0-only**). Der vollständige Text steht in [LICENSE](LICENSE).

Nutzung, Kopieren, Änderungen und Weitergabe sind unter den Bedingungen der Lizenz erlaubt, einschließlich kommerzieller Nutzung. Der frühere Wunsch nach einem Kommerzialisierungsverbot wurde durch diese Lizenzentscheidung ersetzt.

Bei Weitergabe gelten die Quellcode- und Lizenzpflichten der AGPL. Wer eine veränderte Version über ein Netzwerk interaktiv nutzbar macht, muss den betreffenden Nutzenden den entsprechenden Quellcode gemäß Abschnitt 13 anbieten.

Beiträge zu diesem Projekt erfolgen unter derselben Lizenz. Rechte und Lizenzbedingungen fremder Komponenten sind vor einer Übernahme gesondert zu prüfen.
