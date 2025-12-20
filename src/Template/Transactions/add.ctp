<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */
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
<script>
$(document).ready(function() {
    $("form#addtransaction").validate({
        lang: 'de'
    });
});

$('#requesttan').click(function () {
    var $form = $("form");
    // IBAN ohne Leerzeichen für Server-Validierung
    var $iban = $form.find('#empfaenger-iban').val().replace(/\s/g, '');
    var $checkiban = true;

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
    validator.form();

    if($checkiban == true) {
        if(validator.form()) {
            $('#addtransaction').validate();
            $('#taninput').show();
            $('#requesttan').hide();
            $('#transactionform').find(':input').prop('readonly', true);
        }
    } else {
        alert('Bitte IBAN überprüfen.');
    }
});

$('#cancel').click(function () {
    $('#taninput').hide();
    $('#requesttan').show();
    $('#tan').val('');
    $('#transactionform').find(':input').prop('readonly', false);
});

$('#tansubmit').click(function () {
    var tanval = $('#tan').val();
    var modulo = $('#tan').val() % 7;
    if(tanval == 0 || modulo > 0 || tanval < 10000 || tanval > 99999) {
        alert('Ungültige TAN');
    } else {
        // IBAN ohne Leerzeichen speichern
        var $iban = $('#empfaenger-iban');
        $iban.val($iban.val().replace(/\s/g, ''));
        this.form.submit();
    }
});

// Betrag-Formatierung
(function($, undefined) {
    "use strict";
    $(function() {
        var $form = $("form");
        var $input = $form.find("#betrag");
        $input.on("focusout", function(event) {
            var $this = $(this);
            if($this.val() == '') {
                $this.val = '0.00';
            }
            var input = $this.val();
            input = input.replace('.', '');
            input = input.replace(',', '.');
            input = parseFloat(input).toFixed(2);
            input = input.replace('.', ',');
            input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            $this.val(function() {
                return input;
            });
        });
    });

    // IBAN-Formatierung (Vierergruppen mit Leerzeichen)
    $(function() {
        var $ibanInput = $("#empfaenger-iban");

        function formatIBAN(value) {
            // Alle Leerzeichen und Nicht-Alphanumerischen Zeichen entfernen
            var cleaned = value.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
            // In Vierergruppen aufteilen
            var formatted = cleaned.match(/.{1,4}/g);
            return formatted ? formatted.join(' ') : '';
        }

        // Bei Eingabe und Einfügen formatieren
        $ibanInput.on("input paste", function(event) {
            var $this = $(this);
            // Timeout für paste-Event, damit der Wert verfügbar ist
            setTimeout(function() {
                var cursorPos = $this[0].selectionStart;
                var oldVal = $this.val();
                var newVal = formatIBAN(oldVal);
                $this.val(newVal);

                // Cursor-Position anpassen
                var diff = newVal.length - oldVal.length;
                $this[0].setSelectionRange(cursorPos + diff, cursorPos + diff);
            }, 0);
        });

        // Bei Verlassen des Feldes nochmal formatieren
        $ibanInput.on("focusout", function() {
            $(this).val(formatIBAN($(this).val()));
        });
    });
})(jQuery);
</script>
<?php $this->end(); ?>
