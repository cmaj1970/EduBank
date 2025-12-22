<?php
/**
 * FAQ - Häufige Fragen und Begriffserklärungen
 * Neutral formuliert für Schulen und Erwachsenenbildung
 */
?>

<style>
/* Scroll-Offset für sticky Navbar (ca. 70px) */
.accordion-item[id] {
    scroll-margin-top: 80px;
}
</style>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">

        <div class="mb-4">
            <h1><i class="bi bi-question-circle me-2"></i>Häufige Fragen & Begriffe</h1>
            <p class="lead text-muted">
                Erklärungen zu den wichtigsten Begriffen rund ums Banking.
            </p>
        </div>

        <!-- Schnellnavigation -->
        <div class="card mb-4">
            <div class="card-body">
                <strong>Springe zu:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <a href="#konto" class="btn btn-sm btn-outline-primary">Konto</a>
                    <a href="#iban" class="btn btn-sm btn-outline-primary">IBAN</a>
                    <a href="#bic" class="btn btn-sm btn-outline-primary">BIC</a>
                    <a href="#kontostand" class="btn btn-sm btn-outline-primary">Kontostand</a>
                    <a href="#ueberweisung" class="btn btn-sm btn-outline-primary">Überweisung</a>
                    <a href="#auftrag" class="btn btn-sm btn-outline-primary">Auftrag</a>
                    <a href="#umsaetze" class="btn btn-sm btn-outline-primary">Umsätze</a>
                    <a href="#kontoauszug" class="btn btn-sm btn-outline-primary">Kontoauszug</a>
                    <a href="#tan" class="btn btn-sm btn-outline-primary">TAN</a>
                    <a href="#verwendungszweck" class="btn btn-sm btn-outline-primary">Verwendungszweck</a>
                    <a href="#limit" class="btn btn-sm btn-outline-primary">Überweisungslimit</a>
                </div>
            </div>
        </div>

        <!-- FAQ Einträge -->
        <div class="accordion" id="faqAccordion">

            <!-- Konto -->
            <div class="accordion-item" id="konto">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-konto">
                        <i class="bi bi-wallet2 me-2 text-primary"></i>
                        <strong>Was ist ein Konto?</strong>
                    </button>
                </h2>
                <div id="collapse-konto" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Ein <strong>Konto</strong> ist wie ein digitaler Geldbeutel bei einer Bank. Dort wird
                            Geld sicher aufbewahrt, und der aktuelle Stand ist jederzeit einsehbar.
                        </p>
                        <p>
                            Anders als bei einem echten Geldbeutel lässt sich mit einem Konto Geld an andere
                            Personen oder Firmen schicken, ohne es persönlich zu übergeben. Das funktioniert
                            über sogenannte Überweisungen.
                        </p>
                        <p class="mb-0">
                            Jedes Konto hat eine eindeutige Nummer (die IBAN), damit Geld immer beim
                            richtigen Empfänger ankommt.
                        </p>
                    </div>
                </div>
            </div>

            <!-- IBAN -->
            <div class="accordion-item" id="iban">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-iban">
                        <i class="bi bi-upc me-2 text-primary"></i>
                        <strong>Was ist eine IBAN?</strong>
                    </button>
                </h2>
                <div id="collapse-iban" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Die <strong>IBAN</strong> (International Bank Account Number) ist die internationale
                            Kontonummer. Sie funktioniert wie eine Adresse für das Konto – damit weiß die Bank genau,
                            wohin das Geld geschickt werden soll.
                        </p>
                        <p>
                            Eine österreichische IBAN sieht zum Beispiel so aus:<br>
                            <code class="fs-6">AT12 3456 7890 1234 5678</code>
                        </p>
                        <ul class="mb-0">
                            <li><strong>AT</strong> steht für Österreich (jedes Land hat seinen eigenen Code)</li>
                            <li>Die <strong>Zahlen danach</strong> identifizieren die Bank und das jeweilige Konto</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- BIC -->
            <div class="accordion-item" id="bic">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-bic">
                        <i class="bi bi-building me-2 text-primary"></i>
                        <strong>Was ist ein BIC?</strong>
                    </button>
                </h2>
                <div id="collapse-bic" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Der <strong>BIC</strong> (Bank Identifier Code) ist eine Art Kurzname für eine Bank.
                            Er wird manchmal auch SWIFT-Code genannt.
                        </p>
                        <p>
                            Während die IBAN das jeweilige Konto identifiziert, gibt der BIC an, bei welcher
                            Bank dieses Konto geführt wird.
                        </p>
                        <p class="mb-0">
                            <strong>Beispiel:</strong> <code>EDUABORXXX</code><br>
                            Bei Überweisungen innerhalb Europas wird der BIC heute meist nicht mehr benötigt,
                            da er in der IBAN bereits enthalten ist.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kontostand -->
            <div class="accordion-item" id="kontostand">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-kontostand">
                        <i class="bi bi-cash-stack me-2 text-primary"></i>
                        <strong>Was ist der Kontostand?</strong>
                    </button>
                </h2>
                <div id="collapse-kontostand" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Der <strong>Kontostand</strong> (auch Saldo genannt) zeigt, wie viel Geld sich
                            gerade auf einem Konto befindet.
                        </p>
                        <p>
                            <span class="text-success"><i class="bi bi-plus-circle me-1"></i>Ein positiver Kontostand</span>
                            bedeutet vorhandenes Guthaben.
                        </p>
                        <p class="mb-0">
                            <span class="text-danger"><i class="bi bi-dash-circle me-1"></i>Ein negativer Kontostand</span>
                            bedeutet, dass mehr ausgegeben wurde als vorhanden war. Das nennt man auch
                            "im Minus sein" oder "überzogen". Das ist nur möglich, wenn die Bank
                            einen Überziehungsrahmen einräumt.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Überweisung -->
            <div class="accordion-item" id="ueberweisung">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-ueberweisung">
                        <i class="bi bi-send me-2 text-primary"></i>
                        <strong>Was ist eine Überweisung?</strong>
                    </button>
                </h2>
                <div id="collapse-ueberweisung" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Eine <strong>Überweisung</strong> ist das digitale Senden von Geld von einem Konto
                            auf ein anderes. Statt Bargeld zu übergeben, wird der Bank der Auftrag erteilt,
                            einen bestimmten Betrag an jemand anderen zu schicken.
                        </p>
                        <p>
                            Für eine Überweisung werden benötigt:
                        </p>
                        <ul>
                            <li>Der <strong>Name</strong> des Empfängers</li>
                            <li>Die <strong>IBAN</strong> des Empfängers</li>
                            <li>Der <strong>Betrag</strong></li>
                            <li>Ein <strong>Verwendungszweck</strong> (wofür das Geld ist)</li>
                        </ul>
                        <p class="mb-0">
                            Das Geld wird dann vom eigenen Konto abgebucht und dem Empfänger gutgeschrieben.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Auftrag -->
            <div class="accordion-item" id="auftrag">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-auftrag">
                        <i class="bi bi-clock-history me-2 text-primary"></i>
                        <strong>Was ist ein Auftrag?</strong>
                    </button>
                </h2>
                <div id="collapse-auftrag" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Ein <strong>Auftrag</strong> ist eine Anweisung an die Bank, eine bestimmte Aktion
                            durchzuführen – meistens eine Überweisung.
                        </p>
                        <p>
                            Bei jeder Überweisung wird ein Auftrag an die Bank erteilt. In der
                            <strong>Auftragshistorie</strong> sind alle erteilten Aufträge einsehbar –
                            sowohl die bereits durchgeführten als auch die noch offenen.
                        </p>
                        <p class="mb-0">
                            Der Status eines Auftrags zeigt, ob er noch in Bearbeitung ist, erfolgreich
                            durchgeführt wurde oder abgelehnt werden musste.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Umsätze -->
            <div class="accordion-item" id="umsaetze">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-umsaetze">
                        <i class="bi bi-list-ul me-2 text-primary"></i>
                        <strong>Was sind Umsätze?</strong>
                    </button>
                </h2>
                <div id="collapse-umsaetze" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            <strong>Umsätze</strong> sind alle Geldbewegungen auf einem Konto – also jede
                            Einzahlung und jede Auszahlung.
                        </p>
                        <p>
                            <span class="text-success"><i class="bi bi-arrow-down-left me-1"></i>Eingehende Umsätze</span>
                            (Gutschriften) erhöhen den Kontostand – zum Beispiel bei eingehenden Überweisungen.
                        </p>
                        <p class="mb-0">
                            <span class="text-danger"><i class="bi bi-arrow-up-right me-1"></i>Ausgehende Umsätze</span>
                            (Abbuchungen) verringern den Kontostand – zum Beispiel bei getätigten Überweisungen.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kontoauszug -->
            <div class="accordion-item" id="kontoauszug">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-kontoauszug">
                        <i class="bi bi-file-text me-2 text-primary"></i>
                        <strong>Was ist ein Kontoauszug?</strong>
                    </button>
                </h2>
                <div id="collapse-kontoauszug" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Ein <strong>Kontoauszug</strong> ist eine Übersicht über alle Geldbewegungen auf
                            einem Konto in einem bestimmten Zeitraum. Er ist wie ein Protokoll, das zeigt,
                            was auf dem Konto passiert ist.
                        </p>
                        <p>
                            Auf einem Kontoauszug finden sich:
                        </p>
                        <ul>
                            <li>Die <strong>Kontodaten</strong> (Name, IBAN, BIC)</li>
                            <li>Der <strong>Zeitraum</strong>, für den der Auszug gilt</li>
                            <li>Alle <strong>Umsätze</strong> (Einnahmen und Ausgaben)</li>
                            <li>Der <strong>Kontostand</strong> am Ende des Zeitraums</li>
                        </ul>
                        <p class="mb-0">
                            Im echten Banking werden Kontoauszüge regelmäßig von der Bank bereitgestellt –
                            entweder digital im Online-Banking oder auf Papier am Kontoauszugsdrucker.
                            Bei EduBank kann der Kontoauszug jederzeit über die Schaltfläche
                            "Kontoauszug" erstellt und ausgedruckt werden.
                        </p>
                    </div>
                </div>
            </div>

            <!-- TAN -->
            <div class="accordion-item" id="tan">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-tan">
                        <i class="bi bi-shield-lock me-2 text-primary"></i>
                        <strong>Was ist eine TAN?</strong>
                    </button>
                </h2>
                <div id="collapse-tan" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Eine <strong>TAN</strong> (Transaktionsnummer) ist ein einmaliger Sicherheitscode,
                            mit dem eine Überweisung bestätigt wird. Sie funktioniert wie eine zweite
                            Unterschrift.
                        </p>
                        <p>
                            Das Prinzip dahinter: Selbst wenn jemand das Passwort kennt, kann ohne
                            die TAN kein Geld überwiesen werden. Die TAN wird für jede einzelne Überweisung
                            neu generiert.
                        </p>
                        <p class="mb-0">
                            Im echten Banking wird die TAN zum Beispiel per SMS oder über eine
                            spezielle App zugestellt. Bei EduBank werden die TANs auf einer TAN-Liste
                            von der Schul-Admin ausgegeben.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Verwendungszweck -->
            <div class="accordion-item" id="verwendungszweck">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-verwendungszweck">
                        <i class="bi bi-chat-left-text me-2 text-primary"></i>
                        <strong>Was ist der Verwendungszweck?</strong>
                    </button>
                </h2>
                <div id="collapse-verwendungszweck" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Der <strong>Verwendungszweck</strong> ist eine kurze Notiz, die bei einer
                            Überweisung angegeben wird. Sie erklärt, wofür das Geld gedacht ist.
                        </p>
                        <p>
                            <strong>Beispiele:</strong>
                        </p>
                        <ul>
                            <li>"Rechnung Nr. 2024-001"</li>
                            <li>"Bestellung vom 15.03.2024"</li>
                            <li>"Mitgliedsbeitrag 2024"</li>
                        </ul>
                        <p class="mb-0">
                            Der Verwendungszweck hilft dem Empfänger zu verstehen, wofür die Zahlung
                            gedacht ist. Bei Rechnungen steht dort oft die Rechnungsnummer, damit der
                            Empfänger die Zahlung zuordnen kann.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Überweisungslimit -->
            <div class="accordion-item" id="limit">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-limit">
                        <i class="bi bi-speedometer2 me-2 text-primary"></i>
                        <strong>Was ist ein Überweisungslimit?</strong>
                    </button>
                </h2>
                <div id="collapse-limit" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <p>
                            Ein <strong>Überweisungslimit</strong> ist ein Höchstbetrag, der mit einer
                            einzelnen Überweisung gesendet werden kann.
                        </p>
                        <p>
                            Diese Begrenzung dient der Sicherheit: Falls jemand unberechtigt Zugang zum
                            Konto erlangt, kann nicht sofort das gesamte Geld überwiesen werden.
                        </p>
                        <p class="mb-0">
                            Bei EduBank wird das Überweisungslimit von der Schul-Admin festgelegt.
                            Für höhere Beträge kann die Schul-Admin kontaktiert werden.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Noch Fragen? -->
        <div class="card mt-4 border-primary">
            <div class="card-body text-center">
                <i class="bi bi-lightbulb text-primary fs-1 mb-3 d-block"></i>
                <h5>Noch Fragen?</h5>
                <p class="text-muted mb-0">
                    Bei weiteren Fragen steht die Schul-Admin gerne zur Verfügung.
                </p>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alle Sprunglinks abfangen
    document.querySelectorAll('a[href^="#"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href').substring(1);
            var target = document.getElementById(targetId);
            if (!target) return;

            e.preventDefault();

            // Accordion-Panel finden und öffnen
            var collapseId = 'collapse-' + targetId;
            var collapseEl = document.getElementById(collapseId);
            if (collapseEl && !collapseEl.classList.contains('show')) {
                var bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: true });
            }

            // Nach kurzem Delay scrollen (damit Accordion-Animation starten kann)
            setTimeout(function() {
                var navbarHeight = 80;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }, 100);
        });
    });

    // Falls Seite mit Hash geladen wird
    if (window.location.hash) {
        var targetId = window.location.hash.substring(1);
        var target = document.getElementById(targetId);
        if (target) {
            var collapseId = 'collapse-' + targetId;
            var collapseEl = document.getElementById(collapseId);
            if (collapseEl) {
                var bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: true });
            }
            setTimeout(function() {
                var navbarHeight = 80;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }, 300);
        }
    }
});
</script>
