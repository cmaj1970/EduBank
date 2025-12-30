<?php
/**
 * Übungsfirma-Detailansicht für Schuladmins
 * Zusammengeführte Ansicht: Firma + Konto + Transaktionen
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var array $transactions
 */

$account = !empty($user->accounts) ? $user->accounts[0] : null;
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">

        <!-- Zurück-Link -->
        <div class="mb-4">
            <?= $this->Html->link(
                '<i class="bi bi-arrow-left me-1"></i> Zurück zur Liste',
                ['action' => 'index'],
                ['class' => 'btn btn-outline-secondary btn-sm', 'escape' => false]
            ) ?>
        </div>

        <?php if (!empty($isSchoolAdmin)): ?>
        <!-- Kennwort-Hinweis für Schuladmin -->
        <div class="alert alert-info border-info mb-4" <?= $this->HelpText->attr('schuladmin', 'password_info') ?>>
            <div class="d-flex align-items-center">
                <i class="bi bi-key me-2 fs-5"></i>
                <div>
                    <strong>Kennwort für alle Übungsfirmen:</strong>
                    <code class="ms-2 fs-6"><?= h($defaultPassword ?? 'Schueler2024') ?></code>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Zusammengeführte Ansicht: Übungsfirma + Konto -->
        <div class="card mb-4" <?= $this->HelpText->attr('firma_detail', 'firmendaten') ?>>
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i><?= h($user->name) ?></h5>
                <div class="d-flex gap-2">
                    <span <?= $this->HelpText->attr('schuladmin', 'btn_impersonate') ?>><?= $this->Html->link(
                        '<i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als',
                        ['action' => 'impersonate', $user->id],
                        ['class' => 'btn btn-light btn-sm', 'escape' => false]
                    ) ?></span>
                    <?= $this->Html->link(
                        '<i class="bi bi-pencil"></i>',
                        ['action' => 'edit', $user->id],
                        ['class' => 'btn btn-outline-light btn-sm', 'escape' => false, 'title' => 'Bearbeiten']
                    ) ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-trash"></i>',
                        ['action' => 'delete', $user->id],
                        ['class' => 'btn btn-outline-light btn-sm', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('Übungsfirma "{0}" wirklich löschen?', $user->name)]
                    ) ?>
                </div>
            </div>

            <div class="card-body">
                <!-- Firmendaten -->
                <div class="row mb-4">
                    <?php if ($user->school_id && $user->has('school')): ?>
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="text-muted small">Schule</div>
                        <div class="fw-semibold"><?= h($user->school->name) ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-6">
                        <div class="text-muted small">Benutzername</div>
                        <div><code class="fs-6"><?= h($user->username) ?></code></div>
                    </div>
                </div>

                <?php if ($account): ?>
                <hr class="my-4">

                <!-- Kontodaten -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="text-muted small">IBAN</div>
                        <div class="d-flex align-items-center">
                            <code class="fs-5 me-2"><?= h($account->iban) ?></code>
                            <a href="#" class="text-muted copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                                <i class="bi bi-clipboard"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="text-muted small">Kontostand</div>
                        <div class="fs-4 fw-bold <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $this->Number->currency($account->balance, 'EUR') ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="text-muted small">Kontoname</div>
                        <div><?= h($account->name ?: 'Girokonto') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Überziehungsrahmen</div>
                        <div><?= $this->Number->currency($account->maxlimit ?? 0, 'EUR') ?></div>
                    </div>
                </div>

                <?php else: ?>
                <hr class="my-4">
                <div class="text-center text-muted py-3">
                    <i class="bi bi-wallet2 fs-1 d-block mb-2"></i>
                    <p class="mb-3">Noch kein Konto vorhanden</p>
                    <?= $this->Html->link(
                        '<i class="bi bi-plus-lg me-1"></i> Konto anlegen',
                        ['controller' => 'Accounts', 'action' => 'add', '?' => ['user_id' => $user->id]],
                        ['class' => 'btn btn-primary', 'escape' => false]
                    ) ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($account): ?>
            <div class="card-footer bg-light">
                <?= $this->Html->link(
                    '<i class="bi bi-pencil me-1"></i>Konto bearbeiten',
                    ['controller' => 'Accounts', 'action' => 'edit', $account->id, '?' => ['redirect_user_id' => $user->id]],
                    ['class' => 'btn btn-outline-primary btn-sm', 'escape' => false]
                ) ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if ($account): ?>
        <!-- Transaktionen -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-arrow-left-right me-2"></i>Transaktionen
                    <span class="badge bg-secondary ms-2"><?= count($transactions) ?></span>
                </h6>
            </div>
            <?php if (!empty($transactions)): ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Datum</th>
                                <th>Empfänger / Auftraggeber</th>
                                <th>Verwendungszweck</th>
                                <th class="text-end">Betrag</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td class="text-nowrap">
                                    <small><?= $tx->datum->format('d.m.Y H:i') ?></small>
                                </td>
                                <td>
                                    <?= h($tx->empfaenger_name) ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= h($tx->zahlungszweck) ?></small>
                                </td>
                                <td class="text-end text-nowrap">
                                    <span class="<?= $tx->betrag >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $tx->betrag >= 0 ? '+' : '' ?><?= $this->Number->currency($tx->betrag, 'EUR') ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="card-body text-center text-muted py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                <p class="mb-0">Noch keine Transaktionen vorhanden</p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

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
