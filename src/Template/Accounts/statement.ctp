<?php
/**
 * Kontoauszug - Druckbare Ansicht im Bankstil
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>

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
                            <span class="font-monospace"><?= h($account->iban) ?></span>
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
                                    <?php else: ?>
                                        <?= h($transaction->empfaenger_name) ?>
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
