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
<div class="progress-steps mb-4" <?= $this->HelpText->attr('transfer', 'progress_steps') ?>>
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
                    <div class="source-account d-flex justify-content-between align-items-center flex-wrap gap-2" <?= $this->HelpText->attr('transfer', 'source_account') ?>>
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
                    <div class="mb-4" <?= $this->HelpText->attr('transfer', 'recipient_search') ?>>
                        <label for="recipient-search" class="form-label">
                            <i class="bi bi-search me-1"></i><?= __('Empfänger suchen') ?>
                        </label>
                        <select id="recipient-search" class="form-select">
                            <option value=""><?= __('Name oder IBAN eingeben...') ?></option>
                        </select>
                    </div>

                    <!-- Empfänger-Details -->
                    <div class="card bg-light border-0 mb-4" <?= $this->HelpText->attr('transfer', 'recipient_card') ?>>
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
                                    'placeholder' => 'Vorname Nachname oder Firmenname'
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
                                        'style' => 'letter-spacing: 1px;'
                                    ]) ?>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>Überweisungen sind an Partnerunternehmen und Übungsfirmen im EduBank-System möglich.
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="empfaenger-bic" class="form-label"><?= __('BIC') ?> <span class="text-muted">(optional)</span></label>
                                    <?= $this->Form->text('empfaenger_bic', [
                                        'class' => 'form-control font-monospace',
                                        'id' => 'empfaenger-bic',
                                        'placeholder' => 'RZBAATWW'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-divider">
                        <span><i class="bi bi-currency-euro"></i></span>
                    </div>

                    <!-- Zahlungsdetails -->
                    <div class="card bg-light border-0 mb-4" <?= $this->HelpText->attr('transfer', 'payment_details') ?>>
                        <div class="card-body">
                            <h6 class="card-title text-muted mb-3">
                                <i class="bi bi-cash-stack me-1"></i>Zahlungsdetails
                            </h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
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
                                <div class="col-md-6 mb-3">
                                    <label for="datum" class="form-label"><?= __('Ausführungsdatum') ?></label>
                                    <input type="date" name="datum" id="datum" class="form-control" value="<?= date('Y-m-d') ?>">
                                    <div class="form-text">Leer lassen für sofortige Ausführung</div>
                                </div>
                            </div>

                            <div class="mb-3">
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

                <!-- Validierungsfehler (IBAN nicht gefunden etc.) -->
                <div id="validation-error" style="display:none;" class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <span id="validation-error-text"></span>
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

                <!-- Mobile Bestätigung (Info-Box, wird nach Validierung angezeigt) -->
                <div id="mobileconfirminfo" style="display:none;" class="alert alert-info" <?= $this->HelpText->attr('transfer', 'tan') ?>>
                    <h6><i class="bi bi-phone me-2"></i>Bestätigung am Smartphone</h6>
                    <p class="small mb-3">
                        Bitte bestätigen Sie die Überweisung in der EduBank-App auf Ihrem Smartphone.
                    </p>
                    <div class="d-flex gap-2">
                        <?= $this->Form->button(__('Abbrechen'), ['type' => 'button', 'id' => 'cancel', 'class' => 'btn btn-secondary']) ?>
                        <?= $this->Form->button('<i class="bi bi-phone me-1"></i>' . __('Am Smartphone bestätigen'), ['type' => 'button', 'id' => 'openMobileConfirm', 'class' => 'btn btn-primary', 'escape' => false, 'data-bs-toggle' => 'modal', 'data-bs-target' => '#mobileConfirmModal']) ?>
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

<!-- ============================================== -->
<!-- MOBILE CONFIRMATION MODAL                      -->
<!-- ============================================== -->

<!-- Custom styles for modal - iOS Safari compatible -->
<style>
:root {
    --real-vh: 1vh;
}

/* iOS body scroll lock */
body.modal-scroll-lock {
    position: fixed;
    width: 100%;
    overflow: hidden;
    touch-action: none;
}

#mobileConfirmModal {
    background: rgba(0, 0, 0, 0.6) !important;
}

#mobileConfirmModal .modal-dialog {
    margin: 0 !important;
    max-width: 100% !important;
    width: 100% !important;
    height: 100dvh !important;
    height: calc(var(--real-vh, 1vh) * 100) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transform: none !important;
}

#mobileConfirmModal .modal-content {
    background: transparent;
    border: none;
    width: auto;
}

/* Mobile: Show as full-screen app without phone frame */
@media (max-width: 576px) {
    #mobileConfirmModal {
        background: linear-gradient(180deg, #1a365d 0%, #2d4a7c 100%) !important;
    }

    #mobileConfirmModal .modal-dialog {
        height: 100%;
        align-items: flex-start;
        padding-top: env(safe-area-inset-top, 20px);
    }

    #mobileConfirmModal .modal-content {
        width: 100%;
        height: 100%;
    }

    #mobileConfirmModal .phone-container {
        width: 100% !important;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    /* Hide phone frame elements */
    #mobileConfirmModal .phone-frame {
        background: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    #mobileConfirmModal .phone-inner {
        background: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    #mobileConfirmModal .phone-notch,
    #mobileConfirmModal .phone-home-indicator,
    #mobileConfirmModal .phone-status-bar {
        display: none !important;
    }

    #mobileConfirmModal #phoneScreen {
        border-radius: 0 !important;
        aspect-ratio: auto !important;
        flex: 1 !important;
        height: auto !important;
        background: transparent !important;
        display: flex;
        flex-direction: column;
    }

    /* Larger content on mobile */
    #mobileConfirmModal #confirmContent {
        margin: 16px !important;
        padding: 16px !important;
        font-size: 1rem !important;
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }

    #mobileConfirmModal .app-header {
        padding: 20px 0 !important;
        flex-shrink: 0;
    }

    #mobileConfirmModal .app-header i {
        font-size: 2.5rem !important;
    }

    #mobileConfirmModal .app-header .app-title {
        font-size: 1.2rem !important;
    }

    #mobileConfirmModal .app-header .app-subtitle {
        font-size: 0.9rem !important;
    }
}
</style>

<div class="modal fade" id="mobileConfirmModal" tabindex="-1" data-bs-backdrop="false" aria-labelledby="mobileConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Smartphone Frame (iPhone 13 Pro: 390x844 CSS pixels, ratio 19.5:9) -->
            <div class="phone-container" style="width: 280px;">

                <!-- Phone Outer Frame -->
                <div class="phone-frame" style="background: linear-gradient(145deg, #2d2d2d, #1a1a1a); border-radius: 36px; padding: 10px; box-shadow: 0 25px 50px rgba(0,0,0,0.5);">

                    <!-- Phone Inner Frame -->
                    <div class="phone-inner" style="background: #000; border-radius: 28px; padding: 6px; position: relative;">

                        <!-- Notch (Dynamic Island style) -->
                        <div class="phone-notch" style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%); width: 80px; height: 22px; background: #000; border-radius: 11px; z-index: 10;"></div>

                        <!-- Screen (iPhone 13 Pro aspect ratio: 390/844) -->
                        <div id="phoneScreen" style="background: linear-gradient(180deg, #1a365d 0%, #2d4a7c 100%); border-radius: 22px; aspect-ratio: 390/844; overflow: hidden; display: flex; flex-direction: column;">

                            <!-- Status Bar (hidden on real mobile) -->
                            <div class="phone-status-bar d-flex justify-content-between align-items-center px-3 pt-3 pb-1 text-white" style="font-size: 11px; flex-shrink: 0;">
                                <span id="phoneTime">9:41</span>
                                <div class="d-flex gap-1 align-items-center">
                                    <i class="bi bi-reception-4"></i>
                                    <i class="bi bi-wifi"></i>
                                    <i class="bi bi-battery-full"></i>
                                </div>
                            </div>

                            <!-- App Header -->
                            <div class="app-header text-center text-white py-2" style="flex-shrink: 0;">
                                <i class="bi bi-bank2 mb-1 d-block opacity-75" style="font-size: 1.5rem;"></i>
                                <div class="app-title fw-bold" style="font-size: 0.85rem;">EduBank</div>
                                <small class="app-subtitle opacity-75" style="font-size: 0.7rem;">Überweisung bestätigen</small>
                            </div>

                            <!-- Content Area -->
                            <div id="confirmContent" class="bg-white mx-2 rounded-3 p-2 shadow" style="flex: 1; overflow: auto; margin-bottom: 8px;">

                                <!-- Initial State: Confirmation Details -->
                                <div id="stateInitial">
                                    <div class="text-center mb-2">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-1" style="width: 36px; height: 36px;">
                                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1rem;"></i>
                                        </div>
                                        <div class="fw-bold" style="font-size: 0.8rem;">Überweisung prüfen</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">Bitte kontrollieren Sie die Daten</small>
                                    </div>

                                    <div class="border rounded-2 p-2 mb-2" style="background: #f8f9fa; font-size: 0.75rem;">
                                        <div class="mb-1">
                                            <small class="text-muted d-block" style="font-size: 0.6rem;">Empfänger</small>
                                            <strong id="modal-empfaenger">-</strong>
                                        </div>
                                        <div class="mb-1">
                                            <small class="text-muted d-block" style="font-size: 0.6rem;">IBAN</small>
                                            <span class="font-monospace" style="font-size: 0.65rem;" id="modal-iban">-</span>
                                        </div>
                                        <div class="mb-1">
                                            <small class="text-muted d-block" style="font-size: 0.6rem;">Verwendungszweck</small>
                                            <span id="modal-zweck">-</span>
                                        </div>
                                        <div class="mb-1">
                                            <small class="text-muted d-block" style="font-size: 0.6rem;">Ausführungsdatum</small>
                                            <span id="modal-datum">-</span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Betrag</small>
                                            <strong class="text-primary" style="font-size: 1rem;" id="modal-betrag">-</strong>
                                        </div>
                                    </div>

                                    <button id="btnMobileConfirm" class="btn btn-success w-100 py-1" style="font-size: 0.8rem;">
                                        <i class="bi bi-check-lg me-1"></i>Bestätigen
                                    </button>

                                    <button class="btn btn-secondary w-100 py-1 mt-2" style="font-size: 0.8rem;" data-bs-dismiss="modal">
                                        <i class="bi bi-x-lg me-1"></i>Abbrechen
                                    </button>
                                </div>

                                <!-- Processing State -->
                                <div id="stateProcessing" class="text-center py-3" style="display: none;">
                                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2.5rem; height: 2.5rem;">
                                        <span class="visually-hidden">Wird verarbeitet...</span>
                                    </div>
                                    <div class="fw-bold" style="font-size: 0.8rem;">Wird verarbeitet...</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Bitte warten</small>
                                </div>

                                <!-- Success State -->
                                <div id="stateSuccess" class="text-center py-3" style="display: none;">
                                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="bi bi-check-lg text-success" style="font-size: 1.8rem;"></i>
                                    </div>
                                    <div class="text-success fw-bold mb-1" style="font-size: 0.9rem;">Erfolgreich!</div>
                                    <p class="text-muted mb-2" style="font-size: 0.7rem;">
                                        Überweisung über<br>
                                        <strong class="text-dark" style="font-size: 1rem;" id="modal-betrag-success">-</strong><br>
                                        wurde ausgeführt.
                                    </p>
                                    <button id="btnMobileClose" class="btn btn-outline-success btn-sm" style="font-size: 0.7rem;">
                                        <i class="bi bi-x-lg me-1"></i>Schließen
                                    </button>
                                </div>

                            </div>

                        </div>

                        <!-- Home Indicator -->
                        <div class="phone-home-indicator mx-auto mt-1" style="width: 90px; height: 4px; background: #555; border-radius: 2px;"></div>

                    </div>
                </div>

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
                    '<span class="recipient-iban font-monospace">' + item.iban + '</span>' +
                    (item.branch ? ' · <span class="recipient-branch">' + item.branch + '</span>' : '') +
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

    // Form Validation mit Bootstrap 5 Styling
    $.validator.setDefaults({
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorElement: 'div',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function(element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid');
        }
    });

    // Custom IBAN-Validator (prüft Länge ohne Leerzeichen)
    $.validator.addMethod('validIban', function(value, element) {
        var cleaned = value.replace(/\s/g, '');
        return this.optional(element) || cleaned.length >= 20;
    }, 'IBAN muss mindestens 20 Zeichen haben');

    // Custom Betrag-Validator (muss > 0 sein)
    $.validator.addMethod('validBetrag', function(value, element) {
        var cleaned = value.replace(/\./g, '').replace(',', '.');
        var amount = parseFloat(cleaned);
        return !isNaN(amount) && amount > 0;
    }, 'Bitte einen gültigen Betrag größer als 0 eingeben');

    // Hilfsfunktion für Fehlermeldungen
    function showValidationError(message) {
        $('#validation-error-text').text(message);
        $('#validation-error').show();
        $('html, body').animate({ scrollTop: $('#validation-error').offset().top - 100 }, 300);
    }

    function hideValidationError() {
        $('#validation-error').hide();
    }

    var formValidator = $("form#addtransaction").validate({
        lang: 'de',
        rules: {
            empfaenger_name: { required: true },
            empfaenger_iban: { required: true, validIban: true },
            betrag: { required: true, validBetrag: true },
            zahlungszweck: { required: true }
        },
        messages: {
            empfaenger_name: { required: 'Bitte Empfängernamen eingeben' },
            empfaenger_iban: { required: 'Bitte IBAN eingeben' },
            betrag: { required: 'Bitte Betrag eingeben' },
            zahlungszweck: { required: 'Bitte Verwendungszweck eingeben' }
        }
    });

    // Mobile Confirmation - Helper functions
    function updatePhoneTime() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes().toString().padStart(2, '0');
        $('#phoneTime').text(hours + ':' + minutes);
    }

    function resetMobileModal() {
        $('#stateInitial').show();
        $('#stateProcessing').hide();
        $('#stateSuccess').hide();
    }

    function populateModalData() {
        var empfaenger = $('#empfaenger-name').val();
        var iban = $('#empfaenger-iban').val();
        var betrag = $('#betrag').val() + ' €';
        var zweck = $('#zahlungszweck').val();
        var datumRaw = $('#datum').val();

        // Format date to German format (DD.MM.YYYY)
        var datum = 'Sofort';
        if (datumRaw) {
            var parts = datumRaw.split('-');
            if (parts.length === 3) {
                datum = parts[2] + '.' + parts[1] + '.' + parts[0];
            }
        }

        $('#modal-empfaenger').text(empfaenger);
        $('#modal-iban').text(iban);
        $('#modal-betrag').text(betrag);
        $('#modal-betrag-success').text(betrag);
        $('#modal-zweck').text(zweck);
        $('#modal-datum').text(datum);
    }

    // Request Button - Validierung und dann Mobile-Bestätigung anzeigen
    $('#requesttan').on('click touchend', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Bestehenden Validator verwenden (nicht neu erstellen!)
        if (!formValidator.form()) {
            return;
        }

        hideValidationError();
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
                    showValidationError(response.message || 'Die IBAN ist nicht gültig.');
                    $('#empfaenger-iban').addClass('is-invalid').focus();
                    return;
                }

                // Empfänger-Warnung anzeigen wenn Name nicht übereinstimmt
                $('#recipient-warning').hide();
                if (response.nameMatch === 'none') {
                    $('#recipient-warning-text').text(response.message);
                    $('#recipient-actual-name').text(response.actualName);
                    $('#recipient-warning').show();
                }

                // Weiter zur Mobile-Bestätigung
                updateProgressSteps(2);
                $('#mobileconfirminfo').show();
                $('#formactions').hide();
                $('#transactionform').find(':input').prop('readonly', true);
            },
            error: function() {
                showValidationError('Fehler bei der Empfängerprüfung. Bitte versuchen Sie es erneut.');
            }
        });
    });

    // Abbrechen Button
    $('#cancel').on('click touchend', function(e) {
        e.preventDefault();
        updateProgressSteps(1);
        $('#mobileconfirminfo').hide();
        $('#recipient-warning').hide();
        hideValidationError();
        $('#formactions').show();
        $('#transactionform').find(':input').prop('readonly', false);
    });

    // iOS Safari viewport height fix
    function syncViewportHeight() {
        var vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--real-vh', vh + 'px');
    }

    // Initial call and event listeners
    syncViewportHeight();
    $(window).on('resize orientationchange', syncViewportHeight);

    // Scroll position tracking for iOS
    var savedScrollPosition = 0;

    function lockBodyScroll() {
        savedScrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        $('body').addClass('modal-scroll-lock');
        $('body').css('top', -savedScrollPosition + 'px');
    }

    function unlockBodyScroll() {
        $('body').removeClass('modal-scroll-lock');
        $('body').css('top', '');
        window.scrollTo(0, savedScrollPosition);
    }

    // Modal öffnen - Daten befüllen und Zeit aktualisieren
    $('#mobileConfirmModal').on('show.bs.modal', function() {
        syncViewportHeight();
        lockBodyScroll();
        updatePhoneTime();
        populateModalData();
        resetMobileModal();
        // Reset any internal scroll positions
        $('#confirmContent').scrollTop(0);
    });

    // Modal schließen - zurücksetzen
    $('#mobileConfirmModal').on('hidden.bs.modal', function() {
        unlockBodyScroll();
        resetMobileModal();
    });

    // Mobile Bestätigung - Bestätigen Button im Handy
    $('#btnMobileConfirm').on('click', function(e) {
        e.preventDefault();

        // Zeige Processing State
        $('#stateInitial').hide();
        $('#stateProcessing').show();

        // Nach 3 Sekunden: Success State zeigen
        setTimeout(function() {
            $('#stateProcessing').hide();
            $('#stateSuccess').show();
            updateProgressSteps(3);
        }, 3000);
    });

    // Mobile Bestätigung - Schließen Button (nach Erfolg) → Formular abschicken
    $('#btnMobileClose').on('click', function(e) {
        e.preventDefault();

        // Modal schließen
        $('#mobileConfirmModal').modal('hide');

        // IBAN ohne Leerzeichen setzen
        var $iban = $('#empfaenger-iban');
        $iban.val($iban.val().replace(/\s/g, ''));

        // Formular abschicken
        $('#addtransaction').submit();
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
