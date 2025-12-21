<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>

<?php if ($authuser['role'] != 'admin'): ?>
<!-- Übungsfirma-Ansicht -->
<div class="mb-4">
    <h3 class="text-success mb-0"><?= h($account->name) ?></h3>
</div>

<div class="row">
    <div class="col-12">

        <!-- Kontodaten -->
        <table class="table table-sm mb-4" style="max-width: 600px;">
            <tr>
                <td class="text-warning fw-bold">Bezeichnung</td>
                <td class="text-end"><?= h($account->name) ?></td>
            </tr>
            <tr>
                <td class="text-warning fw-bold">IBAN</td>
                <td class="text-end font-monospace">
                    <?= h($account->iban) ?>
                    <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td class="text-warning fw-bold">BIC</td>
                <td class="text-end font-monospace"><?= h($account->bic) ?></td>
            </tr>
            <tr>
                <td class="text-warning fw-bold">Überziehungsrahmen</td>
                <td class="text-end"><?= $this->Number->currency($account->maxlimit, 'EUR') ?></td>
            </tr>
            <tr>
                <td class="text-warning fw-bold">Kontostand</td>
                <td class="text-end fw-bold"><?= $this->Number->currency($account->balance, 'EUR') ?></td>
            </tr>
        </table>

        <!-- Umsätze -->
        <h4 class="mb-3">Umsätze</h4>

        <?php if (!empty($account->transactions) && $account->transactions->count() > 0): ?>
            <?php foreach ($account->transactions as $transaction): ?>
                <?php
                # Eingehende Überweisung (von anderem Konto an dieses)
                $isIncoming = ($transaction->account_id != $account->id);
                ?>
                <div class="border-bottom py-3">
                    <div class="row">
                        <div class="col-md-8">
                            <?php if ($isIncoming): ?>
                                <strong><?= h($transaction->account->user->name) ?></strong>
                                <?php if (isset($transaction->account->user->adresse)): ?>
                                    <?= h($transaction->account->user->adresse) ?>
                                <?php endif; ?>
                                <br>
                                <small><strong>IBAN:</strong> <?= h($transaction->account->iban) ?> | <strong>BIC:</strong> <?= h($transaction->account->bic) ?></small><br>
                            <?php else: ?>
                                <strong><?= h($transaction->empfaenger_name) ?></strong>
                                <?php if ($transaction->empfaenger_adresse): ?>
                                    <?= h($transaction->empfaenger_adresse) ?>
                                <?php endif; ?>
                                <br>
                                <small><strong>IBAN:</strong> <?= h($transaction->empfaenger_iban) ?>
                                <?php if ($transaction->empfaenger_bic): ?>
                                    | <strong>BIC:</strong> <?= h($transaction->empfaenger_bic) ?>
                                <?php endif; ?>
                                </small><br>
                            <?php endif; ?>
                            <small><strong>Verwendungszweck:</strong> <?= h($transaction->zahlungszweck) ?></small><br>
                            <small class="text-muted"><?= h($transaction->datum->format('d.m.Y')) ?></small>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($isIncoming): ?>
                                <span class="text-success" style="font-size: 1.5rem;"><?= $this->Number->format($transaction->betrag, ['places' => 2]) ?></span><small class="text-success"> EUR</small>
                            <?php else: ?>
                                <span class="text-danger" style="font-size: 1.5rem;">-<?= $this->Number->format($transaction->betrag, ['places' => 2]) ?></span><small class="text-danger"> EUR</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="mt-4 d-print-none">
                <button class="btn btn-primary" onclick="window.print();return false;">
                    <i class="bi bi-printer me-1"></i>Drucken
                </button>
            </div>
        <?php else: ?>
            <p class="text-muted">Es sind noch keine Umsätze vorhanden</p>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- Admin-Ansicht: Detaillierte Ansicht mit Aktionen -->
<div class="row">
    <!-- Kontoinformationen -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i><?= h($account->name) ?></h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('Übungsfirma') ?></label>
                    <div><?= $account->has('user') ? $this->Html->link($account->user->name, ['controller' => 'Users', 'action' => 'view', $account->user->id]) : '-' ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('IBAN') ?></label>
                    <div class="font-monospace">
                        <?= h($account->iban) ?>
                        <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('BIC') ?></label>
                    <div class="font-monospace"><?= h($account->bic) ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('Überweisungslimit') ?></label>
                    <div><?= $this->Number->currency($account->maxlimit, 'EUR') ?></div>
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('Aktueller Kontostand') ?></label>
                    <div class="h3 <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $this->Number->currency($account->balance, 'EUR') ?>
                    </div>
                </div>
            </div>

            <!-- Aktionen -->
            <div class="card-footer bg-transparent">
                <div class="d-flex gap-2 flex-wrap">
                    <?= $this->Html->link('<i class="bi bi-arrow-left me-1"></i>Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Bearbeiten', ['action' => 'edit', $account->id], ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false]) ?>
                    <?= $this->Form->postLink('<i class="bi bi-trash me-1"></i>Löschen', ['action' => 'delete', $account->id], ['class' => 'btn btn-sm btn-outline-danger', 'escape' => false, 'confirm' => __('Konto wirklich löschen?')]) ?>
                </div>
            </div>
        </div>

        <!-- Drucken-Button -->
        <div class="d-grid mt-3 d-print-none">
            <button class="btn btn-outline-secondary" onclick="window.print();return false;">
                <i class="bi bi-printer me-1"></i>Kontoauszug drucken
            </button>
        </div>
    </div>

    <!-- Umsätze -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i><?= __('Umsätze') ?></h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($account->transactions) && $account->transactions->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Datum</th>
                                    <th>Empfänger/Auftraggeber</th>
                                    <th>Verwendungszweck</th>
                                    <th class="text-end">Betrag</th>
                                    <th class="text-center">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($account->transactions as $transaction): ?>
                                    <?php
                                    $isIncoming = ($transaction->account_id != $account->id);
                                    ?>
                                    <tr>
                                        <td class="text-nowrap">
                                            <?= h($transaction->datum->format('d.m.Y')) ?>
                                        </td>
                                        <td>
                                            <?php if ($isIncoming): ?>
                                                <strong><?= h($transaction->account->user->name) ?></strong><br>
                                                <small class="text-muted font-monospace"><?= h($transaction->account->iban) ?></small>
                                            <?php else: ?>
                                                <strong><?= h($transaction->empfaenger_name) ?></strong><br>
                                                <small class="text-muted font-monospace"><?= h($transaction->empfaenger_iban) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= h($transaction->zahlungszweck) ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <?php if ($isIncoming): ?>
                                                <span class="text-success fw-bold">
                                                    +<?= $this->Number->currency($transaction->betrag, 'EUR') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-danger fw-bold">
                                                    -<?= $this->Number->currency($transaction->betrag, 'EUR') ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <?= $this->Html->link('<i class="bi bi-eye"></i>', ['controller' => 'Transactions', 'action' => 'view', $transaction->id], ['class' => 'btn btn-outline-secondary', 'escape' => false, 'title' => 'Anzeigen']) ?>
                                                <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['controller' => 'Transactions', 'action' => 'edit', $transaction->id], ['class' => 'btn btn-outline-secondary', 'escape' => false, 'title' => 'Bearbeiten']) ?>
                                                <?= $this->Form->postLink('<i class="bi bi-trash"></i>', ['controller' => 'Transactions', 'action' => 'delete', $transaction->id], ['class' => 'btn btn-outline-danger', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('Transaktion wirklich löschen?')]) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4"></i>
                        <p class="mt-3">Es sind noch keine Umsätze vorhanden</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-iban').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var iban = this.getAttribute('data-iban');
            navigator.clipboard.writeText(iban).then(function() {
                btn.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(function() {
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);
            });
        });
    });
});
</script>
