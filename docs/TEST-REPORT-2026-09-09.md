# Testbericht – 09.09.2026

## Ergebnis

Der vollständige Hauptablauf wurde in der lokalen HumHub-Testcommunity mit
getrennten Systemrollen erfolgreich durchgeführt. Die dabei erzeugten
Testdaten bleiben absichtlich sichtbar, damit sie im Stream und auf dem Board
nachvollzogen werden können.

## Getestete Rollen

- **Jonas Hart**: aktives, nicht dem Arbeitskreis angehörendes Mitglied und
  Einreicher.
- **Juno Adler**: Kreisleitung des Arbeitskreis Gesprächskreis.
- **Meera Falk**: reguläres Mitglied des Arbeitskreis Gesprächskreis.
- **Rafael Winter**: delegierte Person des Arbeitskreis Gesprächskreis.

## Durchgeführte Zustände

| Ablauf | Ergebnis |
| --- | --- |
| Neuer Vorschlag einreichen | Erfolgreich als Vorschlag #54 und #55 angelegt. Der Streambeitrag enthält einen funktionierenden Link zur Vorhabenskarte. |
| Meldung an Kreisleitung | Geprüft: „Jonas Hart hat eine neue Idee eingereicht: TEST – vollständiger Aufgabenablauf.“ |
| Zugriff ohne Entscheidungsrolle | Geprüft: Jonas sieht Erklärung und Diskussion, aber keine Annahme- oder Delegationsauswahl. |
| Delegation nach oben | Vorschlag #54 von Arbeitskreis Gesprächskreis an Kernkreis selbstsein übergeben. Die Zielauswahl erschien erst nach Wahl von „Delegieren“; im Zielkreis entstand ein neuer verlinkter Diskussionsbeitrag. |
| Annahme im Mandat | Vorschlag #55 durch Juno erfolgreich in eine offene Aufgabe überführt. |
| Ressourcenbedarf | Eine Stunde Arbeitszeit mit Pflichtangaben zu Form und zeitlichem Rahmen angelegt. |
| Ressourcenzusage | Meera sagte die volle Stunde zu; Fortschritt wechselte auf 100 %. |
| Aufgabenübernahme und Start | Meera übernahm die Aufgabe und setzte sie in Bearbeitung. |
| Ergebnis zur Abnahme | Meera legte das Ergebnis zur Abnahme vor; Status wechselte auf „Zur Abnahme“. |
| Zwei Rollen bestätigen | Juno bestätigte als Kreisleitung, Rafael als delegierte Person. Danach wechselte die Aufgabe auf „Abgeschlossen“. |
| Archivierung | Rafael archivierte die abgeschlossene Aufgabe; Kennzeichnung „Abgeschlossen · Archiviert“ sichtbar. |
| Gemeinsame Diskussion | Die Vorhabensansicht zeigt die gleiche HumHub-Kommentarleiste wie der zugehörige Streambeitrag, einschließlich Antworten. |

## Während des Tests gefundene und behobene Fehler

1. Die Benachrichtigung für einen neuen Vorschlag wurde nach dem Speichern nur
   als allgemeine Aktualisierung dargestellt. Der konkrete Ereignistyp wird
   jetzt im Benachrichtigungsdatensatz gespeichert.
2. Das Pflichtfeld für Mandatsbezug beziehungsweise Begründung lag fälschlich
   im nur bei Delegation sichtbaren Bereich. Annahmen konnten dadurch nicht
   dokumentiert werden. Das Feld ist jetzt für alle Schritte sichtbar.
3. Nach der ersten Abnahme blieb deren Schaltfläche sichtbar, obwohl diese
   Person nicht erneut bestätigen darf. Sie wird jetzt nach der eigenen
   Bestätigung ausgeblendet.

## Nicht in diesem Durchlauf abgedeckt

- Zurückgabe zur Nacharbeit und begründete Ablehnung wurden nicht als eigene
  Testdaten erzeugt.
- Die Datenschutzprüfung einer Übergabe von einem tatsächlich **privaten**
  Kreis in einen öffentlichen Zielkreis wurde nicht mit einem passenden
  vorhandenen Nachbarkreis durchgeführt. Die Zugriffstrennung ist
  implementiert und per Codeprüfung abgesichert, braucht aber noch einen
  getrennten Browser-Test mit dieser Kreisstruktur.
- Mobilansicht, Tastaturbedienung und separate Fehlermeldungs-Tests wurden
  auf Wunsch nicht geprüft.
