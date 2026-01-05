<?php
/**
 * Account Sidebar Element
 * Zeigt Kontodaten in einer Card an (für view und history)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 * @var array $authuser
 * @var string $currentPage (optional) 'view' oder 'history'
 */
$currentPage = $currentPage ?? 'view';
?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Kontodaten</h5>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <label class="form-label text-muted small">Kontobezeichnung</label>
            <div class="fw-bold"><?= h($account->name) ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label text-muted small">IBAN</label>
            <div class="font-monospace">
                <?= h($account->iban) ?>
                <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                    <i class="bi bi-clipboard"></i>
                </button>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label text-muted small">BIC</label>
            <div class="font-monospace"><?= h($account->bic) ?></div>
        </div>
        <div class="mb-3">
            <label class="form-label text-muted small">Überziehungsrahmen</label>
            <div><?= $this->Number->currency($account->maxlimit, 'EUR') ?></div>
        </div>
        <hr>
        <div>
            <label class="form-label text-muted small">Aktueller Kontostand</label>
            <div class="h3 <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?> mb-0">
                <?= $this->Number->currency($account->balance, 'EUR') ?>
            </div>
        </div>
    </div>
    <div class="card-footer bg-transparent d-print-none">
        <?php if ($authuser['role'] == 'admin'): ?>
        <div class="d-flex gap-2 flex-wrap mb-2">
            <?= $this->Html->link('<i class="bi bi-arrow-left me-1"></i>Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Bearbeiten', ['action' => 'edit', $account->id], ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false]) ?>
        </div>
        <?php endif; ?>
        <div class="d-grid gap-2">
            <?php if ($currentPage === 'history'): ?>
            <?= $this->Html->link('<i class="bi bi-list-ul me-1"></i>Umsätze anzeigen', ['action' => 'view', $account->id], ['class' => 'btn btn-outline-secondary', 'escape' => false]) ?>
            <?php else: ?>
            <?= $this->Html->link('<i class="bi bi-clock-history me-1"></i>Auftragshistorie', ['action' => 'history', $account->id], ['class' => 'btn btn-outline-secondary', 'escape' => false]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="d-grid mt-3 d-print-none">
    <button class="btn btn-outline-secondary" onclick="window.print();return false;">
        <i class="bi bi-printer me-1"></i>Kontoauszug drucken
    </button>
</div>
