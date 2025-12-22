<?php
/**
 * Zentrale Hilfetexte für EduBank
 *
 * Diese Datei enthält alle Hilfetexte für:
 * - Inline-Hilfe (data-help Attribute)
 * - FAQ-Seite
 *
 * Kann später einfach auf Datenbank umgestellt werden.
 */

return [
    /**
     * Inline-Hilfe Texte (für data-help Attribute)
     * Organisiert nach Seite/Bereich
     */
    'inline' => [
        // Kontoübersicht (view.ctp)
        'account' => [
            'card' => 'Hier sind die wichtigsten Kontodaten auf einen Blick: Name, IBAN und aktueller Kontostand.',
            'iban' => 'Die IBAN ist die internationale Kontonummer. Sie wird für Überweisungen benötigt.',
            'balance' => 'Der Kontostand zeigt das aktuell verfügbare Guthaben auf diesem Konto.',
            'quick_actions' => 'Schnellzugriff auf die wichtigsten Funktionen: Geld überweisen, Aufträge ansehen oder Kontoauszug erstellen.',
            'btn_transfer' => 'Hier kann Geld an andere Konten überwiesen werden.',
            'btn_history' => 'Zeigt alle bisherigen Überweisungen, die von diesem Konto getätigt wurden.',
            'btn_statement' => 'Erstellt einen druckbaren Kontoauszug mit allen Kontobewegungen.',
            'income' => 'Summe aller eingegangenen Zahlungen (Gutschriften) auf diesem Konto.',
            'expenses' => 'Summe aller ausgehenden Zahlungen (Überweisungen) von diesem Konto.',
            'transactions' => 'Liste aller Kontobewegungen: Grün = eingegangenes Geld, Rot = ausgehendes Geld.',
        ],

        // Auftragshistorie (history.ctp)
        'history' => [
            'card' => 'Übersicht aller Überweisungen, die von diesem Konto getätigt wurden. Geplante Aufträge werden erst zum angegebenen Datum ausgeführt.',
            'recipient' => 'Name der Person oder Firma, an die überwiesen wurde.',
            'sent_date' => 'Zeitpunkt, an dem der Auftrag erstellt wurde.',
            'status' => 'Durchgeführt = Geld wurde überwiesen. Geplant = Überweisung erfolgt erst zum angegebenen Datum.',
            'amount' => 'Der überwiesene Betrag in Euro.',
        ],

        // Kontoauszug (statement.ctp)
        'statement' => [
            'card' => 'Der Kontoauszug ist ein offizielles Dokument, das alle Kontobewegungen auflistet. Kann ausgedruckt oder als Nachweis verwendet werden.',
            'iban' => 'Die IBAN ist die internationale Kontonummer. Sie identifiziert das Konto eindeutig.',
            'bic' => 'Der BIC (Bank Identifier Code) identifiziert die Bank. Bei SEPA-Überweisungen oft nicht mehr nötig.',
            'balance' => 'Das aktuell verfügbare Guthaben auf dem Konto. Grün = positiv, Rot = negativ.',
            'transactions' => 'Chronologische Auflistung aller Kontobewegungen: Eingänge (grün) und Ausgänge (rot).',
        ],

        // Überweisung (transactions/add.ctp)
        'transfer' => [
            'progress_steps' => 'Die drei Schritte einer Überweisung: 1) Daten eingeben, 2) Angaben prüfen, 3) Mit TAN bestätigen.',
            'source_account' => 'Das Konto, von dem das Geld abgebucht wird. Der verfügbare Betrag zeigt, wie viel überwiesen werden kann.',
            'recipient_search' => 'Hier nach bekannten Empfängern suchen. Beim Auswählen werden die Felder automatisch ausgefüllt.',
            'recipient_card' => 'Angaben zur Person oder Firma, die das Geld erhalten soll. Name und IBAN sind Pflichtfelder.',
            'recipient_name' => 'Name der Person oder Firma, die das Geld erhalten soll.',
            'recipient_iban' => 'Die internationale Kontonummer des Empfängers. Beginnt mit einem Ländercode (z.B. AT für Österreich).',
            'recipient_bic' => 'Bankleitzahl des Empfängers. Wird bei SEPA-Überweisungen meist nicht benötigt.',
            'payment_details' => 'Hier werden Betrag, Datum und Verwendungszweck der Überweisung angegeben.',
            'amount' => 'Der zu überweisende Geldbetrag in Euro. Das Maximum ist durch das Überweisungslimit begrenzt.',
            'date' => 'Das Datum, an dem die Überweisung ausgeführt werden soll. Für spätere Ausführung ein Datum in der Zukunft wählen.',
            'purpose' => 'Ein kurzer Text, der dem Empfänger mitteilt, wofür die Zahlung ist (z.B. Rechnungsnummer).',
            'tan' => 'Die TAN (Transaktionsnummer) ist ein Sicherheitscode, der jede Überweisung absichert. Die TAN-Liste wird von der Schul-Admin ausgegeben.',
        ],
    ],

    /**
     * FAQ-Texte für die Hilfeseite
     * Jeder Eintrag hat: title, content (als Array von Absätzen)
     */
    'faq' => [
        'konto' => [
            'title' => 'Was ist ein Konto?',
            'content' => [
                'Ein <strong>Konto</strong> ist wie ein digitales Sparschwein bei einer Bank. Darauf wird Geld aufbewahrt und es können Überweisungen getätigt werden.',
                'Jedes Konto hat eine eindeutige Nummer (die IBAN) und zeigt immer den aktuellen <strong>Kontostand</strong> an – also wie viel Geld gerade verfügbar ist.',
                'Bei EduBank hat jede Übungsfirma ein eigenes Konto, um den Zahlungsverkehr zu üben.',
            ],
        ],
        'iban' => [
            'title' => 'Was ist eine IBAN?',
            'content' => [
                'Die <strong>IBAN</strong> (International Bank Account Number) ist die internationale Kontonummer. Sie identifiziert ein Bankkonto eindeutig in ganz Europa.',
                'Eine österreichische IBAN sieht zum Beispiel so aus: <code>AT61 1904 3002 3457 3201</code>',
                [
                    'type' => 'list',
                    'items' => [
                        '<strong>AT</strong> = Ländercode (Österreich)',
                        '<strong>61</strong> = Prüfziffer',
                        '<strong>19043</strong> = Bankleitzahl',
                        '<strong>00234573201</strong> = Kontonummer',
                    ],
                ],
                'Für eine Überweisung wird immer die IBAN des Empfängers benötigt.',
            ],
        ],
        'bic' => [
            'title' => 'Was ist ein BIC?',
            'content' => [
                'Der <strong>BIC</strong> (Bank Identifier Code) ist ein internationaler Code, der eine Bank eindeutig identifiziert. Er wird auch SWIFT-Code genannt.',
                'Ein BIC sieht zum Beispiel so aus: <code>RZBAATWW</code>',
                'Bei Überweisungen innerhalb des SEPA-Raums (Europa) ist der BIC meistens nicht mehr erforderlich – die IBAN reicht aus.',
            ],
        ],
        'kontostand' => [
            'title' => 'Was ist der Kontostand?',
            'content' => [
                'Der <strong>Kontostand</strong> zeigt an, wie viel Geld aktuell auf dem Konto verfügbar ist.',
                [
                    'type' => 'list',
                    'items' => [
                        '<strong class="text-success">Positiver Kontostand</strong> (grün): Es ist Geld verfügbar',
                        '<strong class="text-danger">Negativer Kontostand</strong> (rot): Das Konto ist überzogen (Schulden)',
                    ],
                ],
                'Der Kontostand ändert sich bei jeder Einzahlung (wird mehr) oder Auszahlung/Überweisung (wird weniger).',
            ],
        ],
        'ueberweisung' => [
            'title' => 'Was ist eine Überweisung?',
            'content' => [
                'Eine <strong>Überweisung</strong> ist eine bargeldlose Zahlung von einem Konto auf ein anderes.',
                'Für eine Überweisung werden folgende Angaben benötigt:',
                [
                    'type' => 'list',
                    'items' => [
                        'Name des Empfängers',
                        'IBAN des Empfängers',
                        'Betrag in Euro',
                        'Verwendungszweck (wofür ist die Zahlung)',
                    ],
                ],
                'Bei EduBank muss jede Überweisung zusätzlich mit einer TAN bestätigt werden.',
            ],
        ],
        'auftrag' => [
            'title' => 'Was ist ein Auftrag?',
            'content' => [
                'Ein <strong>Auftrag</strong> ist eine Überweisung, die erstellt aber noch nicht ausgeführt wurde.',
                'Aufträge können für ein bestimmtes Datum in der Zukunft geplant werden. Erst an diesem Tag wird das Geld tatsächlich überwiesen.',
                [
                    'type' => 'list',
                    'items' => [
                        '<strong>Geplant</strong>: Der Auftrag wartet noch auf die Ausführung',
                        '<strong>Durchgeführt</strong>: Das Geld wurde bereits überwiesen',
                    ],
                ],
            ],
        ],
        'umsaetze' => [
            'title' => 'Was sind Umsätze?',
            'content' => [
                '<strong>Umsätze</strong> sind alle Geldbewegungen auf einem Konto – also alle Ein- und Auszahlungen.',
                [
                    'type' => 'list',
                    'items' => [
                        '<strong class="text-success">Eingehende Umsätze</strong> (grün, +): Geld, das auf das Konto eingezahlt wurde',
                        '<strong class="text-danger">Ausgehende Umsätze</strong> (rot, -): Geld, das vom Konto abgebucht wurde',
                    ],
                ],
                'In der Umsatzliste ist ersichtlich, wann welcher Betrag von wem oder an wen geflossen ist.',
            ],
        ],
        'kontoauszug' => [
            'title' => 'Was ist ein Kontoauszug?',
            'content' => [
                'Ein <strong>Kontoauszug</strong> ist ein offizielles Dokument, das alle Kontobewegungen für einen bestimmten Zeitraum auflistet.',
                'Der Kontoauszug enthält:',
                [
                    'type' => 'list',
                    'items' => [
                        'Kontodaten (Name, IBAN)',
                        'Aktueller Kontostand',
                        'Liste aller Umsätze mit Datum, Empfänger/Auftraggeber und Betrag',
                    ],
                ],
                'Kontoauszüge dienen als Nachweis für Zahlungen und können ausgedruckt oder digital aufbewahrt werden.',
            ],
        ],
        'tan' => [
            'title' => 'Was ist eine TAN?',
            'content' => [
                'Eine <strong>TAN</strong> (Transaktionsnummer) ist ein Einmal-Passwort, das zur Bestätigung einer Überweisung verwendet wird.',
                'Das Prinzip dahinter: Selbst wenn jemand das Passwort kennt, kann ohne die TAN kein Geld überwiesen werden. Die TAN wird für jede einzelne Überweisung neu generiert.',
                'Im echten Banking wird die TAN zum Beispiel per SMS oder über eine spezielle App zugestellt. Bei EduBank werden die TANs auf einer TAN-Liste von der Schul-Admin ausgegeben.',
            ],
        ],
        'verwendungszweck' => [
            'title' => 'Was ist der Verwendungszweck?',
            'content' => [
                'Der <strong>Verwendungszweck</strong> ist ein kurzer Text, der bei einer Überweisung mitgeschickt wird. Er erklärt dem Empfänger, wofür die Zahlung gedacht ist.',
                'Typische Verwendungszwecke sind:',
                [
                    'type' => 'list',
                    'items' => [
                        'Rechnungsnummer (z.B. "Rechnung Nr. 2024-001")',
                        'Bestellnummer',
                        'Kundennummer',
                        'Kurze Beschreibung (z.B. "Warenlieferung Oktober")',
                    ],
                ],
                'Ein guter Verwendungszweck hilft beiden Seiten, die Zahlung später zuordnen zu können.',
            ],
        ],
        'limit' => [
            'title' => 'Was ist ein Überweisungslimit?',
            'content' => [
                'Ein <strong>Überweisungslimit</strong> ist ein Höchstbetrag, der mit einer einzelnen Überweisung gesendet werden kann.',
                'Diese Begrenzung dient der Sicherheit: Falls jemand unberechtigt Zugang zum Konto erlangt, kann nicht sofort das gesamte Geld überwiesen werden.',
                'Bei EduBank wird das Überweisungslimit von der Schul-Admin festgelegt. Für höhere Beträge kann die Schul-Admin kontaktiert werden.',
            ],
        ],
    ],

    /**
     * Allgemeine Texte
     */
    'general' => [
        'faq_title' => 'Häufige Fragen & Begriffe',
        'faq_subtitle' => 'Hier werden die wichtigsten Begriffe rund ums Banking einfach erklärt.',
        'faq_more_questions' => 'Noch Fragen?',
        'faq_contact' => 'Bei weiteren Fragen steht die Schul-Admin gerne zur Verfügung.',
    ],
];
