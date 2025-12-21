<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */

# Select2 CSS im Head einbinden
$this->Html->css('https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', ['block' => true]);
$this->Html->css('https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css', ['block' => true]);
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-send me-2"></i><?= __('Neue Überweisung') ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zum Konto', ['controller' => 'Accounts', 'action' => 'view', $account->id], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <?= $this->Form->create($transaction, ['id' => 'addtransaction']) ?>

                <div id="transactionform">
                    <div class="mb-3">
                        <label for="account-id" class="form-label"><?= __('Auftraggeber-Konto') ?></label>
                        <?= $this->Form->select('account_id', $accounts, [
                            'class' => 'form-select',
                            'id' => 'account-id',
                            'empty' => false
                        ]) ?>
                    </div>

                    <fieldset class="mb-4">
                        <legend class="h6 text-muted"><?= __('Empfänger') ?></legend>

                        <!-- Empfänger-Suche mit Select2 -->
                        <div class="mb-3">
                            <label for="recipient-search" class="form-label">
                                <i class="bi bi-search me-1"></i><?= __('Empfänger suchen') ?>
                            </label>
                            <select id="recipient-search" class="form-select">
                                <option value=""><?= __('Name oder IBAN eingeben...') ?></option>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Wählen Sie eine Übungsfirma aus dem System oder geben Sie die Daten manuell ein.
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label for="empfaenger-name" class="form-label"><?= __('Name') ?> <span class="text-danger">*</span></label>
                            <?= $this->Form->text('empfaenger_name', [
                                'class' => 'form-control',
                                'id' => 'empfaenger-name',
                                'required' => true
                            ]) ?>
                        </div>

                        <div class="mb-3">
                            <label for="empfaenger-adresse" class="form-label"><?= __('Adresse') ?></label>
                            <?= $this->Form->text('empfaenger_adresse', [
                                'class' => 'form-control',
                                'id' => 'empfaenger-adresse'
                            ]) ?>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="empfaenger-iban" class="form-label"><?= __('IBAN') ?> <span class="text-danger">*</span></label>
                                <?= $this->Form->text('empfaenger_iban', [
                                    'class' => 'form-control',
                                    'id' => 'empfaenger-iban',
                                    'required' => true,
                                    'placeholder' => 'AT00 0000 0000 0000 0000'
                                ]) ?>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="empfaenger-bic" class="form-label"><?= __('BIC') ?></label>
                                <?= $this->Form->text('empfaenger_bic', [
                                    'class' => 'form-control',
                                    'id' => 'empfaenger-bic'
                                ]) ?>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="mb-4">
                        <legend class="h6 text-muted"><?= __('Zahlungsdetails') ?></legend>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="betrag" class="form-label"><?= __('Betrag') ?> <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <?= $this->Form->text('betrag', [
                                        'class' => 'form-control',
                                        'id' => 'betrag',
                                        'required' => true,
                                        'placeholder' => '0,00'
                                    ]) ?>
                                    <span class="input-group-text">€</span>
                                </div>
                                <div class="form-text">Max. <?= $this->Number->currency($max_betrag, 'EUR') ?></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="datum" class="form-label"><?= __('Ausführungsdatum') ?></label>
                                <input type="date" name="datum" id="datum" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="zahlungszweck" class="form-label"><?= __('Verwendungszweck') ?></label>
                            <?= $this->Form->text('zahlungszweck', [
                                'class' => 'form-control',
                                'id' => 'zahlungszweck'
                            ]) ?>
                        </div>
                    </fieldset>
                </div>

                <div id="taninput" style="display:none;" class="alert alert-warning">
                    <h6><i class="bi bi-shield-lock me-2"></i>TAN-Bestätigung</h6>
                    <p class="small mb-3">Bitte geben Sie eine gültige TAN ein, um die Überweisung zu bestätigen.</p>
                    <div class="mb-3">
                        <label for="tan" class="form-label"><?= __('TAN') ?></label>
                        <?= $this->Form->text('tan', [
                            'class' => 'form-control',
                            'id' => 'tan',
                            'placeholder' => '5-stellige TAN'
                        ]) ?>
                    </div>
                    <div class="d-flex gap-2">
                        <?= $this->Form->button(__('Abbrechen'), ['type' => 'button', 'id' => 'cancel', 'class' => 'btn btn-secondary']) ?>
                        <?= $this->Form->button(__('Überweisung ausführen'), ['type' => 'button', 'id' => 'tansubmit', 'class' => 'btn btn-success']) ?>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <?= $this->Form->button('<i class="bi bi-shield-check me-1"></i> Mit TAN zeichnen', ['type' => 'button', 'id' => 'requesttan', 'class' => 'btn btn-primary', 'escape' => false]) ?>
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
    // IBAN-Formatierung (Vierergruppen mit Leerzeichen)
    function formatIBAN(value) {
        // Alle Leerzeichen und Nicht-Alphanumerischen Zeichen entfernen
        var cleaned = value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
        // In Vierergruppen aufteilen
        var formatted = cleaned.match(/.{1,4}/g);
        return formatted ? formatted.join(' ') : '';
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
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        },
        templateResult: function(item) {
            if (item.loading) {
                return 'Suche...';
            }
            // Formatierung: Schule | Firma – IBAN
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
            if (!item.id) {
                return item.text;
            }
            return item.name + ' – ' + item.iban;
        }
    });

    // Bei Auswahl eines Empfängers die Felder befüllen
    $('#recipient-search').on('select2:select', function(e) {
        var data = e.params.data;
        $('#empfaenger-name').val(data.name);
        $('#empfaenger-iban').val(formatIBAN(data.iban));
        $('#empfaenger-bic').val(data.bic);
        // Fokus auf nächstes Feld setzen
        $('#betrag').focus();
    });

    // Bei Löschen der Auswahl die Felder leeren
    $('#recipient-search').on('select2:clear', function() {
        $('#empfaenger-name').val('');
        $('#empfaenger-iban').val('');
        $('#empfaenger-bic').val('');
    });

    // Form Validation initialisieren
    $("form#addtransaction").validate({
        lang: 'de'
    });

    // TAN-Request Button (für iOS: touchend + click)
    $('#requesttan').on('click touchend', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $form = $("form");
        // IBAN ohne Leerzeichen für Server-Validierung
        var $iban = $form.find('#empfaenger-iban').val().replace(/\s/g, '');

        $.ajax({
            url: '/transactions/checkiban',
            data: $iban,
            success: function (response) {
                console.log(response);
                if(response != "true") {
                    alert("Bitte die IBAN überprüfen.");
                }
            },
            error: function() {
                alert('ajax error');
            }
        });

        var validator = $("#addtransaction").validate({
            lang: 'de'
        });

        if(validator.form()) {
            $('#taninput').show();
            $('#requesttan').hide();
            $('#transactionform').find(':input').prop('readonly', true);
        }
    });

    // Abbrechen Button
    $('#cancel').on('click touchend', function(e) {
        e.preventDefault();
        $('#taninput').hide();
        $('#requesttan').show();
        $('#tan').val('');
        $('#transactionform').find(':input').prop('readonly', false);
    });

    // TAN Submit Button
    $('#tansubmit').on('click touchend', function(e) {
        e.preventDefault();
        var tanval = $('#tan').val();
        var modulo = tanval % 7;
        if(tanval == 0 || modulo > 0 || tanval < 10000 || tanval > 99999) {
            alert('Ungültige TAN');
        } else {
            // IBAN ohne Leerzeichen speichern
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
        input = input.replace('.', '');
        input = input.replace(',', '.');
        input = parseFloat(input).toFixed(2);
        input = input.replace('.', ',');
        input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        $this.val(input);
    });

    // Nur bei Verlassen des Feldes formatieren (stabiler)
    $("#empfaenger-iban").on("blur", function() {
        // Nicht formatieren wenn readonly (nach TAN-Request)
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
