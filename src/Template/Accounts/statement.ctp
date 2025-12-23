<?php
/**
 * Kontoauszug - Druckbare Ansicht im Bankstil
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>

<style>
@media print {
    @page {
        size: A4;
        margin: 10mm;
    }
    *, *::before, *::after {
        box-sizing: border-box !important;
    }
    html, body {
        font-size: 10px !important;
        margin: 0 !important;
        padding: 0 !important;
        min-height: 0 !important;
        height: auto !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    body.d-flex {
        display: block !important;
    }
    .min-vh-100 {
        min-height: 0 !important;
    }
    .flex-grow-1 {
        flex-grow: 0 !important;
    }
    main, main > .container-fluid {
        padding: 0 !important;
        margin: 0 !important;
    }
    .container, .container-fluid {
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .row {
        margin: 0 !important;
    }
    .col-lg-10, .col-xl-8, [class*="col-"] {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
    }
    .py-4, .py-3, .py-2 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
    }
    .card {
        border: 1px solid #ccc !important;
        box-shadow: none !important;
        margin: 0 !important;
    }
    .card-body {
        padding: 8px !important;
    }
    .fs-3 {
        font-size: 1rem !important;
    }
    .fs-5 {
        font-size: 0.8rem !important;
    }
    .mb-4, .mb-3, .mb-2, .mb-1 {
        margin-bottom: 0.25rem !important;
    }
    .pb-3, .pb-2 {
        padding-bottom: 0.25rem !important;
    }
    .mt-4, .mt-3 {
        margin-top: 0.25rem !important;
    }
    .pt-3 {
        padding-top: 0.25rem !important;
    }
    .g-4 {
        gap: 0 !important;
    }
    .table {
        font-size: 9px !important;
        margin-bottom: 0 !important;
    }
    .table th, .table td {
        padding: 2px 4px !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
    h4.text-primary {
        font-size: 0.95rem !important;
        margin-bottom: 0.15rem !important;
    }
    h6.fw-bold {
        font-size: 0.75rem !important;
        margin-bottom: 0 !important;
        padding-bottom: 0.15rem !important;
    }
    .small, small {
        font-size: 7px !important;
    }
    .font-monospace {
        font-size: 8px !important;
    }
    .border-bottom, .border-top {
        border-width: 1px !important;
        margin: 0.25rem 0 !important;
        padding: 0.15rem 0 !important;
    }
    .text-muted.small.text-center {
        font-size: 7px !important;
        margin-top: 5px !important;
        padding-top: 5px !important;
    }
}
</style>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">

        <!-- Zurück-Button (nicht drucken) -->
        <div class="mb-3 d-print-none">
            <a href="/accounts/view/<?= $account->id ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Zurück zur Kontoübersicht
            </a>
        </div>

        <!-- Kontoauszug -->
        <div class="card" <?= $this->HelpText->attr('statement', 'card') ?>>
            <div class="card-body p-4">

                <!-- Kopfbereich -->
                <div class="d-flex justify-content-between align-items-start mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="mb-1 text-primary fw-bold">EduBank</h4>
                        <small class="text-muted">Banking Simulation für Schulen</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold fs-5">Kontoauszug</div>
                        <div class="text-muted small">Erstellt am <?= date('d.m.Y') ?></div>
                    </div>
                </div>

                <!-- Kontodaten -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <span class="text-muted small">Kontoinhaber</span><br>
                            <strong class="fs-5"><?= h($account->user->name ?? $account->name) ?></strong>
                        </div>
                        <div class="mb-2" <?= $this->HelpText->attr('statement', 'iban') ?>>
                            <span class="text-muted small">IBAN</span><br>
                            <span class="font-monospace text-nowrap"><?= h($account->iban) ?></span>
                            <a href="#" class="text-muted ms-1 d-print-none" onclick="navigator.clipboard.writeText('<?= h($account->iban) ?>');this.innerHTML='<i class=\'bi bi-check\'></i>';setTimeout(()=>this.innerHTML='<i class=\'bi bi-clipboard\'></i>',1500);return false;" title="IBAN kopieren"><i class="bi bi-clipboard"></i></a>
                        </div>
                        <div <?= $this->HelpText->attr('statement', 'bic') ?>>
                            <span class="text-muted small">BIC</span><br>
                            <span class="font-monospace"><?= h($account->bic) ?></span>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="mb-2" <?= $this->HelpText->attr('statement', 'balance') ?>>
                            <span class="text-muted small">Aktueller Kontostand</span><br>
                            <span class="fs-3 fw-bold <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $this->Number->currency($account->balance, 'EUR') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Umsatzliste -->
                <h6 class="border-bottom pb-2 mb-0 fw-bold" <?= $this->HelpText->attr('statement', 'transactions') ?>>Umsätze</h6>
                <?php if (!empty($account->transactions) && $account->transactions->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 100px;">Datum</th>
                                <th>Auftraggeber/Empfänger</th>
                                <th>Verwendungszweck</th>
                                <th class="text-end" style="width: 130px;">Betrag</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($account->transactions as $transaction):
                                $isIncoming = ($transaction->account_id != $account->id);
                            ?>
                            <tr>
                                <td class="text-nowrap"><?= h($transaction->datum->format('d.m.Y')) ?></td>
                                <td>
                                    <?php if ($isIncoming): ?>
                                        <?= h($transaction->account->user->name ?? 'Unbekannt') ?>
                                        <br><small class="text-muted font-monospace"><?= h($transaction->account->iban) ?></small>
                                    <?php else: ?>
                                        <?= h($transaction->empfaenger_name) ?>
                                        <br><small class="text-muted font-monospace"><?= h($transaction->empfaenger_iban) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted"><?= h($transaction->zahlungszweck) ?></td>
                                <td class="text-end text-nowrap fw-bold <?= $isIncoming ? 'text-success' : 'text-danger' ?>">
                                    <?= $isIncoming ? '+' : '-' ?><?= $this->Number->currency($transaction->betrag, 'EUR') ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <p class="mb-0">Keine Umsätze vorhanden</p>
                </div>
                <?php endif; ?>

                <!-- Fußbereich -->
                <div class="mt-4 pt-3 border-top text-muted small text-center">
                    Dies ist ein Übungsdokument der EduBank-Simulation und kein offizieller Kontoauszug.
                </div>

            </div>
        </div>

        <!-- Drucken-Button (nicht drucken) -->
        <div class="d-grid mt-3 d-print-none">
            <button class="btn btn-primary btn-lg" onclick="window.print();return false;">
                <i class="bi bi-printer me-2"></i>Kontoauszug drucken
            </button>
        </div>

    </div>
</div>
