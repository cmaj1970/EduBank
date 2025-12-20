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
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i><?= __('Details Überweisung') ?></h5>
                <a href="javascript:history.back();" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Zurück
                </a>
            </div>
            <div class="card-body">
                <?php if ($authuser['role'] == 'admin'): ?>
                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('Konto') ?></label>
                    <div>
                        <?= $transaction->has('account') ? $this->Html->link($transaction->account->name, ['controller' => 'Accounts', 'action' => 'view', $transaction->account->id]) : '-' ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Empfänger Name') ?></label>
                        <div><strong><?= h($transaction->empfaenger_name) ?></strong></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Empfänger Adresse') ?></label>
                        <div><?= h($transaction->empfaenger_adresse) ?: '-' ?></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label text-muted small"><?= __('Empfänger IBAN') ?></label>
                        <div class="font-monospace"><?= h($transaction->empfaenger_iban) ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small"><?= __('Empfänger BIC') ?></label>
                        <div class="font-monospace"><?= h($transaction->empfaenger_bic) ?: '-' ?></div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small"><?= __('Betrag') ?></label>
                        <div class="h4 text-danger">-<?= $this->Number->currency($transaction->betrag, 'EUR') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small"><?= __('Ausführungsdatum') ?></label>
                        <div><?= h($transaction->datum->format('d.m.Y')) ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small"><?= __('Erstellt am') ?></label>
                        <div><?= h($transaction->created->format('d.m.Y H:i')) ?></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small"><?= __('Verwendungszweck') ?></label>
                    <div><?= h($transaction->zahlungszweck) ?: '-' ?></div>
                </div>
            </div>

            <?php if ($authuser['role'] == 'admin'): ?>
            <div class="card-footer bg-transparent">
                <div class="d-flex gap-2">
                    <?= $this->Html->link('<i class="bi bi-arrow-left me-1"></i>Zur Liste', ['action' => 'index'], ['class' => 'btn btn-outline-secondary', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Bearbeiten', ['action' => 'edit', $transaction->id], ['class' => 'btn btn-outline-primary', 'escape' => false]) ?>
                    <?= $this->Form->postLink('<i class="bi bi-trash me-1"></i>Löschen', ['action' => 'delete', $transaction->id], ['class' => 'btn btn-outline-danger', 'escape' => false, 'confirm' => __('Transaktion #{0} wirklich löschen?', $transaction->id)]) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
