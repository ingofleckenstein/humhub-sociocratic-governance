<?php
use yii\helpers\Html;
\humhub\modules\sociocraticGovernance\assets\GovernanceAsset::register($this);
?>
<div class="sg">
<header class="sg-hero"><span class="sg-eyebrow">Methodische Unterstützung</span><h1>So arbeiten wir im Kreis</h1>
<p>Ein klarer Prozess hilft uns, Verantwortung zu teilen und gemeinsam ins Handeln zu kommen.</p>
<?= Html::a('Zurück zum Kreis', $space->createUrl('/sociocratic-governance/circle/index')) ?></header>
<section class="sg-card"><h2>Von der Idee zum Vorhaben</h2>
<p><strong>Idee → Mandatsprüfung → Aufgabe → Beratung → Konsent → Umsetzung → Review</strong></p>
<p>Alle registrierten Menschen dürfen Ideen an beliebige Kreise geben. Zuerst prüfen wir nur: Sind wir zuständig? Innerhalb unseres Mandats wird die Idee zur Aufgabe für kommende Sitzungen. Andernfalls geben wir sie begründet an den Oberkreis weiter. Ideen außerhalb des Gesamtmandats können hier nicht bearbeitet werden.</p>
<div class="sg-note">Diese Seite ist eine Ablaufhilfe, keine laufende Abstimmung. Vorhaben, Sitzungszuordnung und Änderungshistorie folgen in M2.</div>
<h2>Schritt für Schritt zum Konsent</h2>
<ol class="sg-process">
<li><strong>Vorschlag</strong>Was wollen wir ermöglichen, warum und innerhalb welchen Mandats?</li>
<li><strong>Verständnisfragen</strong>Was ist noch unklar? Erst verstehen, dann bewerten.</li>
<li><strong>Reaktionen</strong>Welche Perspektiven, Bedenken und Verbesserungen gibt es?</li>
<li><strong>Einwände</strong>Welche begründeten Risiken sprechen gegen diesen Vorschlag?</li>
<li><strong>Integration</strong>Einwände verstehen und einarbeiten. Nach Änderungen erneut auf Konsent prüfen.</li>
<li><strong>Konsent</strong>Den aktuellen Vorschlag ausdrücklich bestätigen und den Beschluss dokumentieren.</li>
</ol>
<p>Ohne Einwand kann die Integrationsrunde entfallen. Schweigen oder Fristablauf ist kein Konsent. Offene Punkte gehen mit ihrem Stand in die nächste Sitzung. Ein Beschluss gilt ab Konsent; der Oberkreis prüft seine Mandatskonformität.</p></section>
<section class="sg-card"><h2>Beschlussvorlage</h2>
<p>Übernehmt diese Struktur in euer Protokoll. Ein Beschluss ermöglicht eigenständiges Handeln innerhalb klarer Grenzen.</p>
<table>
<tr><th scope="row">Wer?</th><td>Wer übernimmt Verantwortung und stimmt dieser Übernahme zu?</td></tr>
<tr><th scope="row">Was?</th><td>Welches konkrete Ergebnis oder welcher Handlungsspielraum wird vereinbart?</td></tr>
<tr><th scope="row">Bis wann?</th><td>Umsetzungstermin und Zeitpunkt für den Review.</td></tr>
<tr><th scope="row">Warum?</th><td>Welchem Ziel und welchem Bedarf dient das Vorhaben?</td></tr>
<tr><th scope="row">Mandat?</th><td>Befugnisse, Grenzen, Budget und zuständiger Kreis.</td></tr>
</table>
<h3>SMART als Formulierungshilfe</h3>
<p><strong>Spezifisch:</strong> konkret beschreiben. <strong>Messbar:</strong> Ergebnis überprüfbar machen, auch qualitativ.
<strong>Attraktiv / akzeptiert:</strong> Zweck und Verantwortungsübernahme klären.
<strong>Realistisch:</strong> Ressourcen und Befugnisse prüfen.
<strong>Terminiert:</strong> Umsetzung und Review verabreden.</p>
<p>SMART ist keine Eingangshürde für Ideen. Nach dem Beschluss folgen Umsetzung und Review; „beschlossen“ heißt nicht „erledigt“.</p></section>
<section class="sg-card"><h2>Rollen und Kreisleben</h2>
<details open><summary>Die vier Kreisrollen</summary>
<p><strong>Kreisleitung:</strong> Ausrichtung und Umsetzung, Verbindung zum Oberkreis.
<strong>Delegierte*r:</strong> Perspektive des Unterkreises im Oberkreis.
<strong>Moderation:</strong> Runden begleiten.
<strong>Dokumentation:</strong> Beschlüsse, Protokolle und Reviews festhalten.</p>
<p>Kreisleitung und Delegierte*r dürfen niemals dieselbe Person sein. Andere Doppelrollen sind bei zwei oder drei Personen möglich; ab vier Personen sollen sie kritisch überprüft werden. Die automatische Warnung folgt in Stufe 2.</p>
<p>Die zweite Person eines neuen Kreises übernimmt zunächst die Delegiertenrolle. Beim dritten Mitglied wird diese Rolle gewählt; weitere Beitritte lösen keine Neuwahl aus. Der Standard-Wahlrhythmus beträgt sechs Monate und steht im Mandat.</p></details>
<details><summary>Gründen, wachsen, pausieren und auflösen</summary>
<p>Ein Gründungsvorhaben führt zu einem Beschluss mit Mandat, Kreisleitung und mindestens einer weiteren Person. Bei der Gründung wählt der Oberkreis die Leitung, später der Kreis selbst.</p>
<p>Ab acht zählenden Mitgliedern wird Teilung vorgeschlagen, ab neun ist ein Teilungsprozess erforderlich. Reine Vertretung aus Unterkreisen zählt nicht mit; eigene Mitarbeit oder Rollen zählen, jede Person einmal.</p>
<p>Eine Pause ist zeitlich begrenzt. Bei Auflösung werden Mandate, offene Arbeit und Verantwortung übergeben; Wissen und Beschlüsse werden archiviert. Abgelaufene Amtszeiten ohne Wiederwahl führen nach dem vereinbarten Modell zur Rückführung an den Oberkreis. Eine konfigurierte dauerhafte oberste Leitung ist davon ausgenommen. Diese Schritte erfolgen in Stufe 1 manuell.</p></details>
<details><summary>Mandat und Krisen</summary>
<p>Übertragene Mandate schützen die Entscheidungshoheit des Unterkreises. Mandatsänderungen beschließt der Oberkreis; reguläre Anträge und Kriseninterventionen haben unterschiedliche Teilnahmebedingungen.</p>
<p>Bei Krisenintervention ist nur die Delegation des betroffenen Kreises vom entsprechenden Konsent ausgeschlossen. Bei dessen Auflösung sind alle Personen aus diesem Kreis ausgeschlossen. Der verursachende Kreis trägt die Folgen, andernfalls der Oberkreis. Ein Krisenwerkzeug und die noch offenen Verfahrensdetails folgen später.</p></details>
<details><summary>Themensprecher*innen und Wissen</summary>
<p>Ein anderer Kreis kann eine Person zur zeitweiligen Beratung eines Themas entsenden. Die Rolle gibt keine allgemeinen Schreib- oder Konsentrechte. Sitzungsaufzeichnungen und spätere KI-Protokolle brauchen transparente Vereinbarungen; KI entscheidet nicht.</p></details>
</section></div>
