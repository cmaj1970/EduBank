<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>

<div class="row">
    <!-- Kontoinformationen -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i><?= h($account->name) ?></h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('IBAN') ?></label>
                    <div class="font-monospace"><?= h($account->iban) ?></div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('BIC') ?></label>
                    <div class="font-monospace"><?= h($account->bic) ?></div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="card-footer bg-transparent">
                <?php if ($authuser['role'] == 'admin'): ?>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <?= $this->Html->link('<i class="bi bi-arrow-left me-1"></i>Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
                        <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Bearbeiten', ['action' => 'edit', $account->id], ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false]) ?>
                    </div>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <?= $this->Html->link('<i class="bi bi-list-ul me-1"></i>Umsätze', ['action' => 'view', $account->id], ['class' => 'btn btn-outline-secondary', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="bi bi-send me-1"></i>Neue Überweisung', ['controller' => 'Transactions', 'action' => 'add', $account->id], ['class' => 'btn btn-primary', 'escape' => false]) ?>
                </div>
            </div>
        </div>

        <!-- Drucken-Button -->
        <div class="d-grid mt-3 d-print-none">
            <button class="btn btn-outline-secondary" onclick="window.print();return false;">
                <i class="bi bi-printer me-1"></i>Drucken
            </button>
        </div>
    </div>

    <!-- Auftragshistorie -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i><?= __('Auftragshistorie') ?></h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($account->transactions) && $account->transactions->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Empfänger</th>
                                    <th>Gesendet am</th>
                                    <th>Auftragsstatus</th>
                                    <th class="text-end">Betrag</th>
                                    <th class="text-center">Aktion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($account->transactions as $transaction): ?>
                                    <?php
                                    $isExecuted = ($transaction->datum <= new \DateTime());
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= h($transaction->empfaenger_name) ?></strong>
                                        </td>
                                        <td class="text-nowrap">
                                            <?= h($transaction->created->format('d.m.Y H:i')) ?>
                                        </td>
                                        <td class="text-nowrap">
                                            <?php if ($isExecuted): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Durchgeführt am <?= h($transaction->datum->format('d.m.Y')) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i>Geplant für <?= h($transaction->datum->format('d.m.Y')) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <span class="text-danger fw-bold">
                                                -<?= $this->Number->currency($transaction->betrag, 'EUR') ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if (!$isExecuted): ?>
                                                <?= $this->Form->postLink(
                                                    '<i class="bi bi-x-circle me-1"></i>Stornieren',
                                                    ['controller' => 'Transactions', 'action' => 'storno', $transaction->id],
                                                    [
                                                        'class' => 'btn btn-sm btn-outline-danger',
                                                        'escape' => false,
                                                        'confirm' => __('Sind Sie sicher, dass Sie den Auftrag stornieren möchten?')
                                                    ]
                                                ) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4"></i>
                        <p class="mt-3">Noch keine Aufträge vorhanden</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
