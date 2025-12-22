<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 *
 * ELBA-Style Layout: Überweisungsformular mit Progress-Steps,
 * Source-Account-Box und strukturierten Abschnitten
 */

# Select2 CSS im Head einbinden
$this->Html->css('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', ['block' => true]);
$this->Html->css('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css', ['block' => true]);
?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><i class="bi bi-send me-2"></i><?= __('Neue Überweisung') ?></h3>
        <p class="text-muted mb-0">SEPA-Überweisung</p>
    </div>
    <?= $this->Html->link('<i class="bi bi-arrow-left me-1"></i>Zurück zum Konto', ['controller' => 'Accounts', 'action' => 'view', $account->id], ['class' => 'btn btn-outline-secondary', 'escape' => false]) ?>
</div>

<!-- Progress Steps -->
<div class="progress-steps mb-4" data-help="Die drei Schritte einer Überweisung: 1) Daten eingeben, 2) Angaben prüfen, 3) Mit TAN bestätigen.">
    <div class="progress-step active" id="step1-indicator">
        <span class="step-number">1</span>
        <span class="step-label">Erfassen</span>
    </div>
    <div class="step-connector"></div>
    <div class="progress-step" id="step2-indicator">
        <span class="step-number">2</span>
        <span class="step-label">Prüfen</span>
    </div>
    <div class="step-connector"></div>
    <div class="progress-step" id="step3-indicator">
        <span class="step-number">3</span>
        <span class="step-label">Bestätigen</span>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Überweisungsdetails</h5>
            </div>
            <div class="card-body">
                <?= $this->Form->create($transaction, ['id' => 'addtransaction']) ?>

                <div id="transactionform">
                    <!-- Source Account Box -->
                    <div class="source-account d-flex justify-content-between align-items-center flex-wrap gap-2" data-help="Das Konto, von dem das Geld abgebucht wird. Der verfügbare Betrag zeigt, wie viel überwiesen werden kann.">
                        <div>
                            <div class="source-account-label">Abbuchungskonto</div>
                            <div class="source-account-name"><?= h($account->name) ?></div>
                            <div class="source-account-details"><?= h($account->iban) ?></div>
                        </div>
                        <div class="text-end">
                            <div class="source-account-label">Verfügbar</div>
                            <div class="source-account-balance"><?= $this->Number->currency($account->balance, 'EUR') ?></div>
                        </div>
                    </div>

                    <!-- Hidden Account Select (wird durch Source Account Box ersetzt für User) -->
                    <?php if ($authuser['role'] == 'admin'): ?>
                    <div class="mb-3">
                        <label for="account-id" class="form-label"><?= __('Auftraggeber-Konto') ?></label>
                        <?= $this->Form->select('account_id', $accounts, [
                            'class' => 'form-select',
                            'id' => 'account-id',
                            'empty' => false
                        ]) ?>
                    </div>
                    <?php else: ?>
                    <?= $this->Form->hidden('account_id', ['value' => $account->id, 'id' => 'account-id']) ?>
                    <?php endif; ?>

                    <div class="section-divider">
                        <span><i class="bi bi-arrow-down"></i></span>
                    </div>

                    <!-- Empfänger-Suche -->
                    <div class="mb-4" data-help="Hier nach bekannten Empfängern suchen. Beim Auswählen werden die Felder automatisch ausgefüllt.">
                        <label for="recipient-search" class="form-label">
                            <i class="bi bi-search me-1"></i><?= __('Empfänger suchen') ?>
                        </label>
                        <select id="recipient-search" class="form-select">
                            <option value=""><?= __('Name oder IBAN eingeben...') ?></option>
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>Überweisungen sind nur an Konten von teilnehmenden Schulen im EduBank-System möglich.
                        </div>
                    </div>

                    <!-- Empfänger-Details -->
                    <div class="card bg-light border-0 mb-4" data-help="Angaben zur Person oder Firma, die das Geld erhalten soll. Name und IBAN sind Pflichtfelder.">
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-3">
                                <i class="bi bi-person me-1"></i>Empfänger
                            </h6>

                            <div class="mb-3">
                                <label for="empfaenger-name" class="form-label"><?= __('Name') ?> <span class="text-danger">*</span></label>
                                <?= $this->Form->text('empfaenger_name', [
                                    'class' => 'form-control',
                                    'id' => 'empfaenger-name',
                                    'required' => true,
                                    'placeholder' => 'Vorname Nachname oder Firmenname',
                                    'data-help' => 'Name der Person oder Firma, die das Geld erhalten soll.'
                                ]) ?>
                            </div>

                            <div class="mb-3">
                                <label for="empfaenger-adresse" class="form-label"><?= __('Adresse') ?> <span class="text-muted">(optional)</span></label>
                                <?= $this->Form->text('empfaenger_adresse', [
                                    'class' => 'form-control',
                                    'id' => 'empfaenger-adresse',
                                    'placeholder' => 'Straße, PLZ Ort'
                                ]) ?>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="empfaenger-iban" class="form-label"><?= __('IBAN') ?> <span class="text-danger">*</span></label>
                                    <?= $this->Form->text('empfaenger_iban', [
                                        'class' => 'form-control font-monospace',
                                        'id' => 'empfaenger-iban',
                                        'required' => true,
                                        'placeholder' => 'AT00 0000 0000 0000 0000',
                                        'style' => 'letter-spacing: 1px;',
                                        'data-help' => 'Die internationale Kontonummer des Empfängers. Beginnt mit einem Ländercode (z.B. AT für Österreich).'
                                    ]) ?>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="empfaenger-bic" class="form-label"><?= __('BIC') ?> <span class="text-muted">(optional)</span></label>
                                    <?= $this->Form->text('empfaenger_bic', [
                                        'class' => 'form-control font-monospace',
                                        'id' => 'empfaenger-bic',
                                        'placeholder' => 'RZBAATWW',
                                        'data-help' => 'Bankleitzahl des Empfängers. Wird bei SEPA-Überweisungen meist nicht benötigt.'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span><i class="bi bi-currency-euro"></i></span>
                    </div>

                    <!-- Zahlungsdetails -->
                    <div class="card bg-light border-0 mb-4" data-help="Hier werden Betrag, Datum und Verwendungszweck der Überweisung angegeben.">
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-3">
                                <i class="bi bi-cash-stack me-1"></i>Zahlungsdetails
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3" data-help="Der zu überweisende Geldbetrag in Euro. Das Maximum ist durch das Überweisungslimit begrenzt.">
                                    <label for="betrag" class="form-label"><?= __('Betrag') ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <?= $this->Form->text('betrag', [
                                            'class' => 'form-control fs-5 fw-bold text-end',
                                            'id' => 'betrag',
                                            'required' => true,
                                            'placeholder' => '0,00'
                                        ]) ?>
                                        <span class="input-group-text fs-5 fw-bold">€</span>
                                    </div>
                                    <div class="form-text">Maximal <?= $this->Number->currency($max_betrag, 'EUR') ?></div>
                                </div>
                                <div class="col-md-6 mb-3" data-help="Das Datum, an dem die Überweisung ausgeführt werden soll. Für spätere Ausführung ein Datum in der Zukunft wählen.">
                                    <label for="datum" class="form-label"><?= __('Ausführungsdatum') ?></label>
                                    <input type="date" name="datum" id="datum" class="form-control" value="<?= date('Y-m-d') ?>">
                                    <div class="form-text">Leer lassen für sofortige Ausführung</div>
                                </div>
                            </div>

                            <div class="mb-3" data-help="Ein kurzer Text, der dem Empfänger mitteilt, wofür die Zahlung ist (z.B. Rechnungsnummer).">
                                <label for="zahlungszweck" class="form-label"><?= __('Verwendungszweck') ?> <span class="text-danger">*</span></label>
                                <?= $this->Form->text('zahlungszweck', [
                                    'class' => 'form-control',
                                    'id' => 'zahlungszweck',
                                    'placeholder' => 'z.B. Rechnung Nr. 12345',
                                    'maxlength' => 140,
                                    'required' => true
                                ]) ?>
                                <div class="form-text">Maximal 140 Zeichen</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empfänger-Warnung (Name stimmt nicht überein) -->
                <div id="recipient-warning" style="display:none;" class="alert alert-warning mb-3">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 me-3"></i>
                        <div>
                            <strong>Empfängerüberprüfung</strong>
                            <p class="mb-2" id="recipient-warning-text"></p>
                            <small class="text-muted">Hinterlegter Name: <strong id="recipient-actual-name"></strong></small>
                            <p class="mb-0 mt-2 small">Sie können die Überweisung trotzdem durchführen. Bitte prüfen Sie jedoch, ob Sie den richtigen Empfänger angegeben haben.</p>
                        </div>
                    </div>
                </div>

                <!-- TAN-Eingabe (wird nach Validierung angezeigt) -->
                <div id="taninput" style="display:none;" class="alert alert-info" data-help="Die TAN (Transaktionsnummer) ist ein Sicherheitscode, der jede Überweisung absichert. Die TAN-Liste wird von der Schul-Admin ausgegeben.">
                    <h6><i class="bi bi-shield-lock me-2"></i>TAN-Bestätigung</h6>
                    <p class="small mb-3">
                        Bitte eine gültige TAN eingeben, um die Überweisung zu bestätigen.
                        <i class="bi bi-question-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="Die TAN muss 5-stellig und durch 7 teilbar sein." style="cursor: help;"></i>
                    </p>
                    <div class="mb-3">
                        <label for="tan" class="form-label"><?= __('TAN') ?></label>
                        <?= $this->Form->text('tan', [
                            'class' => 'form-control',
                            'id' => 'tan',
                            'placeholder' => '5-stellige TAN',
                            'maxlength' => 5,
                            'pattern' => '[0-9]{5}',
                            'style' => 'width: 120px;'
                        ]) ?>
                    </div>
                    <div class="d-flex gap-2">
                        <?= $this->Form->button(__('Abbrechen'), ['type' => 'button', 'id' => 'cancel', 'class' => 'btn btn-secondary']) ?>
                        <?= $this->Form->button('<i class="bi bi-check-lg me-1"></i>' . __('Überweisung ausführen'), ['type' => 'button', 'id' => 'tansubmit', 'class' => 'btn btn-success', 'escape' => false]) ?>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex gap-2 justify-content-between" id="formactions">
                    <a href="/accounts/view/<?= $account->id ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i>Abbrechen
                    </a>
                    <?= $this->Form->button('<i class="bi bi-shield-check me-1"></i>Weiter zur Bestätigung', ['type' => 'button', 'id' => 'requesttan', 'class' => 'btn btn-primary', 'escape' => false]) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->start('script'); ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/de.js"></script>
<script>
$(document).ready(function() {
    // Bootstrap Tooltips initialisieren
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

    // IBAN-Formatierung (Vierergruppen mit Leerzeichen)
    function formatIBAN(value) {
        var cleaned = value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        var formatted = cleaned.match(/.{1,4}/g);
        return formatted ? formatted.join(' ') : '';
    }

    // Progress Steps aktualisieren
    function updateProgressSteps(step) {
        $('.progress-step').removeClass('active completed');
        if (step >= 1) $('#step1-indicator').addClass(step > 1 ? 'completed' : 'active');
        if (step >= 2) $('#step2-indicator').addClass(step > 2 ? 'completed' : 'active');
        if (step >= 3) $('#step3-indicator').addClass('active');
    }

    // Select2 für Empfänger-Suche initialisieren
    $('#recipient-search').select2({
        theme: 'bootstrap-5',
        language: 'de',
        placeholder: 'Empfänger auswählen oder suchen...',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
            url: '/transactions/searchRecipients',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        },
        templateResult: function(item) {
            if (item.loading) return 'Suche...';
            var $container = $('<div class="select2-result-recipient">' +
                '<div class="recipient-name"><strong>' + item.name + '</strong></div>' +
                '<div class="recipient-details text-muted small">' +
                    '<span class="recipient-school">' + item.school + '</span> · ' +
                    '<span class="recipient-iban font-monospace">' + item.iban + '</span>' +
                '</div>' +
            '</div>');
            return $container;
        },
        templateSelection: function(item) {
            if (!item.id) return item.text;
            return item.name + ' – ' + item.iban;
        }
    });

    // Bei Auswahl eines Empfängers die Felder befüllen
    $('#recipient-search').on('select2:select', function(e) {
        var data = e.params.data;
        $('#empfaenger-name').val(data.name);
        $('#empfaenger-iban').val(formatIBAN(data.iban));
        $('#empfaenger-bic').val(data.bic);
        $('#betrag').focus();
    });

    // Bei Löschen der Auswahl die Felder leeren
    $('#recipient-search').on('select2:clear', function() {
        $('#empfaenger-name').val('');
        $('#empfaenger-iban').val('');
        $('#empfaenger-bic').val('');
    });

    // Form Validation initialisieren
    $("form#addtransaction").validate({ lang: 'de' });

    // TAN-Request Button
    $('#requesttan').on('click touchend', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var validator = $("#addtransaction").validate({ lang: 'de' });
        if (!validator.form()) {
            return;
        }

        var iban = $('#empfaenger-iban').val().replace(/\s/g, '');
        var name = $('#empfaenger-name').val();

        // IBAN und Empfängername prüfen
        $.ajax({
            url: '/transactions/checkiban',
            data: { iban: iban, name: name },
            dataType: 'json',
            success: function(response) {
                // IBAN nicht gültig
                if (!response.valid) {
                    alert(response.message || 'Die IBAN ist nicht gültig.');
                    return;
                }

                // Empfänger-Warnung anzeigen wenn Name nicht übereinstimmt
                $('#recipient-warning').hide();
                if (response.nameMatch === 'none') {
                    $('#recipient-warning-text').text(response.message);
                    $('#recipient-actual-name').text(response.actualName);
                    $('#recipient-warning').show();
                }

                // Weiter zur TAN-Eingabe
                updateProgressSteps(2);
                $('#taninput').show();
                $('#formactions').hide();
                $('#transactionform').find(':input').prop('readonly', true);
                $('#tan').focus();
            },
            error: function() {
                alert('Fehler bei der Empfängerprüfung. Bitte versuchen Sie es erneut.');
            }
        });
    });

    // Abbrechen Button
    $('#cancel').on('click touchend', function(e) {
        e.preventDefault();
        updateProgressSteps(1);
        $('#taninput').hide();
        $('#recipient-warning').hide();
        $('#formactions').show();
        $('#tan').val('');
        $('#transactionform').find(':input').prop('readonly', false);
    });

    // TAN Submit Button
    $('#tansubmit').on('click touchend', function(e) {
        e.preventDefault();
        var tanval = $('#tan').val();
        var modulo = tanval % 7;
        if(tanval == 0 || modulo > 0 || tanval < 10000 || tanval > 99999) {
            alert('Ungültige TAN. Die TAN muss 5-stellig und durch 7 teilbar sein.');
        } else {
            updateProgressSteps(3);
            var $iban = $('#empfaenger-iban');
            $iban.val($iban.val().replace(/\s/g, ''));
            $('#addtransaction').submit();
        }
    });

    // Betrag-Formatierung
    $("#betrag").on("blur", function() {
        var $this = $(this);
        if($this.val() == '') {
            $this.val('0,00');
            return;
        }
        var input = $this.val();
        // Nur Ziffern, Komma und Punkt erlauben
        input = input.replace(/[^\d,\.]/g, '');
        input = input.replace('.', '');
        input = input.replace(',', '.');
        var parsed = parseFloat(input);
        // Bei ungültiger Eingabe auf 0 setzen
        if (isNaN(parsed)) {
            $this.val('0,00');
            return;
        }
        input = parsed.toFixed(2);
        input = input.replace('.', ',');
        input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        $this.val(input);
    });

    // IBAN-Formatierung bei Blur
    $("#empfaenger-iban").on("blur", function() {
        if (!$(this).prop('readonly')) {
            $(this).val(formatIBAN($(this).val()));
        }
    });

    // Bei Paste auch formatieren
    $("#empfaenger-iban").on("paste", function() {
        var $this = $(this);
        setTimeout(function() {
            if (!$this.prop('readonly')) {
                $this.val(formatIBAN($this.val()));
            }
        }, 10);
    });

}); // Ende document.ready
</script>
<?php $this->end(); ?>
