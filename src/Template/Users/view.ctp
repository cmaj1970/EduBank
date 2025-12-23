<?php
/**
 * Übungsfirma-Detailansicht für Schuladmins
 * Firmendaten + Konten-Tabelle
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */

# Gesamtguthaben berechnen
$totalBalance = 0;
if (!empty($user->accounts)) {
    foreach ($user->accounts as $account) {
        $totalBalance += $account->balance;
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">

        <!-- Zurück-Link -->
        <div class="mb-4">
            <?= $this->Html->link(
                '<i class="bi bi-arrow-left me-1"></i> Zurück zur Liste',
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary btn-sm', 'escape' => false]
            ) ?>
        </div>

        <!-- Block 1: Firmendaten -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Firmendaten</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="row flex-grow-1">
                        <?php if ($user->school_id && $user->has('school')): ?>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="text-muted small">Zugehörige Schule</div>
                            <div class="fw-semibold"><?= h($user->school->name) ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="text-muted small">Firmenname</div>
                            <div class="fw-semibold"><?= h($user->name) ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Login-Name</div>
                            <div><code class="fs-6"><?= h($user->username) ?></code></div>
                        </div>
                    </div>
                    <div class="ms-3 d-flex gap-2 flex-shrink-0">
                        <?= $this->Html->link(
                            '<i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als',
                            ['action' => 'impersonate', $user->id],
                            ['class' => 'btn btn-success btn-sm', 'escape' => false]
                        ) ?>
                        <?= $this->Html->link(
                            '<i class="bi bi-pencil me-1"></i>Bearbeiten',
                            ['action' => 'edit', $user->id],
                            ['class' => 'btn btn-outline-primary btn-sm', 'escape' => false]
                        ) ?>
                        <?= $this->Form->postLink(
                            '<i class="bi bi-trash me-1"></i>Löschen',
                            ['action' => 'delete', $user->id],
                            ['class' => 'btn btn-outline-danger btn-sm', 'escape' => false, 'confirm' => __('Übungsfirma "{0}" wirklich löschen?', $user->name)]
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Block 2: Konten -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Konten dieser Übungsfirma</h5>
                <?= $this->Html->link(
                    '<i class="bi bi-plus-lg me-1"></i> Konto hinzufügen',
                    ['controller' => 'Accounts', 'action' => 'add', '?' => ['user_id' => $user->id]],
                    ['class' => 'btn btn-primary btn-sm', 'escape' => false]
                ) ?>
            </div>

            <?php if (!empty($user->accounts)): ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kontoname</th>
                                <th>IBAN</th>
                                <th class="text-end">Überziehungsrahmen</th>
                                <th class="text-end">Kontostand</th>
                                <th style="width: 140px" class="text-end">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user->accounts as $account): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-credit-card text-primary me-2"></i>
                                    <strong><?= h($account->name ?: 'Girokonto') ?></strong>
                                </td>
                                <td>
                                    <code class="font-monospace"><?= h($account->iban) ?></code>
                                    <a href="#" class="text-muted ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                                        <i class="bi bi-clipboard"></i>
                                    </a>
                                </td>
                                <td class="text-end">
                                    <?= $this->Number->currency($account->maxlimit ?? 0, 'EUR') ?>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $this->Number->currency($account->balance, 'EUR') ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <?= $this->Html->link(
                                        '<i class="bi bi-pencil"></i>',
                                        ['controller' => 'Accounts', 'action' => 'edit', $account->id, '?' => ['redirect_user_id' => $user->id]],
                                        ['class' => 'btn btn-outline-primary btn-sm me-1', 'escape' => false, 'title' => 'Bearbeiten']
                                    ) ?>
                                    <?= $this->Form->postLink(
                                        '<i class="bi bi-trash"></i>',
                                        ['controller' => 'Accounts', 'action' => 'delete', $account->id, '?' => ['redirect_user_id' => $user->id]],
                                        ['class' => 'btn btn-outline-danger btn-sm', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('Konto "{0}" wirklich löschen?', $account->name ?: 'Girokonto')]
                                    ) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        <?= count($user->accounts) ?> <?= count($user->accounts) == 1 ? 'Konto' : 'Konten' ?> insgesamt
                    </span>
                    <span class="fw-semibold">
                        Gesamtguthaben:
                        <span class="<?= $totalBalance >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $this->Number->currency($totalBalance, 'EUR') ?>
                        </span>
                    </span>
                </div>
            </div>

            <?php else: ?>
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-wallet2 display-4"></i>
                <p class="mt-3 mb-0">Keine Konten vorhanden</p>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-iban').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var iban = this.getAttribute('data-iban');
            var icon = this.querySelector('i');
            navigator.clipboard.writeText(iban).then(function() {
                icon.className = 'bi bi-check text-success';
                setTimeout(function() {
                    icon.className = 'bi bi-clipboard';
                }, 1500);
            });
        });
    });
});
</script>
