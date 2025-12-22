<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<!-- Welcome Section -->
<div class="welcome-section">
    <h1 class="welcome-title"><?= h($user->name) ?></h1>
    <p class="welcome-date">
        <i class="bi bi-building me-1"></i>Übungsfirma
    </p>
</div>

<div class="row g-4">
    <!-- Linke Spalte: Firmendaten -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Firmendaten</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">Firmenname</div>
                    <div class="fw-bold"><?= h($user->name) ?></div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Benutzername</div>
                    <code><?= h($user->username) ?></code>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">Status</div>
                    <span class="badge <?= $user->active ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $user->active ? 'Aktiv' : 'Inaktiv' ?>
                    </span>
                </div>
                <?php if ($user->school_id && $user->has('school')): ?>
                <div class="mb-3">
                    <div class="text-muted small">Schule</div>
                    <div><?= h($user->school->name) ?></div>
                </div>
                <?php endif; ?>
                <div>
                    <div class="text-muted small">Erstellt am</div>
                    <div><?= $user->created->format('d.m.Y H:i') ?></div>
                </div>
            </div>
        </div>

        <!-- Aktionen -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Aktionen</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Bearbeiten', ['action' => 'edit', $user->id], ['class' => 'btn btn-outline-primary', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="bi bi-arrow-left me-1"></i>Zurück zur Liste', ['action' => 'index'], ['class' => 'btn btn-outline-secondary', 'escape' => false]) ?>
                    <?= $this->Form->postLink('<i class="bi bi-trash me-1"></i>Löschen', ['action' => 'delete', $user->id], ['class' => 'btn btn-outline-danger', 'escape' => false, 'confirm' => __('Übungsfirma "{0}" wirklich löschen?', $user->name)]) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Rechte Spalte: Konten -->
    <div class="col-lg-8">
        <h5 class="mb-3"><i class="bi bi-wallet2 me-2"></i>Konten</h5>

        <?php if (!empty($user->accounts)): ?>
            <?php foreach ($user->accounts as $account): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1"><?= h($account->name) ?></h6>
                            <div class="font-monospace small text-muted">
                                <?= h($account->iban) ?>
                                <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fs-5 fw-bold <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $this->Number->currency($account->balance, 'EUR') ?>
                            </div>
                            <a href="<?= $this->Url->build(['controller' => 'Accounts', 'action' => 'view', $account->id]) ?>" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="bi bi-eye me-1"></i>Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-wallet2 display-4"></i>
                    <p class="mt-3 mb-0">Keine Konten vorhanden</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

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
