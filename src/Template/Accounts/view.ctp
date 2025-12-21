<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 *
 * ELBA-Style Layout: Dashboard mit Willkommensbereich, Schnellaktionen,
 * Kontoübersicht und Transaktionsliste
 */

// Berechne Einnahmen und Ausgaben für Statistik
$einnahmen = 0;
$ausgaben = 0;
if (!empty($account->transactions)) {
    foreach ($account->transactions as $t) {
        if ($t->account_id != $account->id) {
            $einnahmen += $t->betrag;
        } else {
            $ausgaben += $t->betrag;
        }
    }
}
?>

<?php if ($authuser['role'] != 'admin'): ?>
<!-- Übungsfirma-Ansicht: ELBA-Style Dashboard -->

<!-- Welcome Section -->
<div class="welcome-section">
    <h1 class="welcome-title">
        Willkommen, <?= h($account->user->name ?? $account->name) ?>!
    </h1>
    <p class="welcome-date">
        <i class="bi bi-calendar3 me-1"></i>
        <?= date('l, d. F Y') ?>
    </p>
</div>

<div class="row g-4">
    <!-- Linke Spalte: Konto + Schnellaktionen + Statistik -->
    <div class="col-lg-4">
        <!-- Kontokarte -->
        <div class="card account-card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Mein Konto</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div class="text-muted small">Kontobezeichnung</div>
                        <div class="fw-bold"><?= h($account->name) ?></div>
                        <div class="font-monospace small mt-2"><?= h($account->iban) ?></div>
                    </div>
                    <div class="account-balance-display">
                        <div class="balance-label">Verfügbar</div>
                        <div class="balance-amount <?= $account->balance >= 0 ? 'positive' : 'negative' ?>">
                            <?= $this->Number->currency($account->balance, 'EUR') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schnellaktionen -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Schnellaktionen</h5>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="/transactions/add" class="quick-action-btn">
                        <i class="bi bi-send"></i>
                        <span>Überweisen</span>
                    </a>
                    <a href="/accounts/history/<?= $account->id ?>" class="quick-action-btn">
                        <i class="bi bi-clock-history"></i>
                        <span>Aufträge</span>
                    </a>
                    <a href="/accounts/directory" class="quick-action-btn">
                        <i class="bi bi-people"></i>
                        <span>Firmen</span>
                    </a>
                    <a href="#" class="quick-action-btn" onclick="window.print();return false;">
                        <i class="bi bi-printer"></i>
                        <span>Drucken</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="row g-3">
            <div class="col-6">
                <div class="card">
                    <div class="stat-card">
                        <div class="stat-icon positive">
                            <i class="bi bi-arrow-down-left"></i>
                        </div>
                        <div class="stat-value text-success">
                            +<?= $this->Number->currency($einnahmen, 'EUR') ?>
                        </div>
                        <div class="stat-label">Einnahmen</div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card">
                    <div class="stat-card">
                        <div class="stat-icon negative">
                            <i class="bi bi-arrow-up-right"></i>
                        </div>
                        <div class="stat-value text-danger">
                            -<?= $this->Number->currency($ausgaben, 'EUR') ?>
                        </div>
                        <div class="stat-label">Ausgaben</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rechte Spalte: Umsätze -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Letzte Umsätze</h5>
                <a href="/accounts/history/<?= $account->id ?>" class="btn btn-sm btn-outline-primary">
                    Alle Aufträge <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($account->transactions) && $account->transactions->count() > 0): ?>
                    <?php
                    $count = 0;
                    foreach ($account->transactions as $transaction):
                        if ($count >= 8) break;
                        $count++;
                        $isIncoming = ($transaction->account_id != $account->id);
                    ?>
                        <div class="transaction-item">
                            <div class="transaction-info">
                                <div class="transaction-icon <?= $isIncoming ? 'credit' : 'debit' ?>">
                                    <i class="bi bi-arrow-<?= $isIncoming ? 'down-left' : 'up-right' ?>"></i>
                                </div>
                                <div class="transaction-details">
                                    <div class="transaction-name">
                                        <?php if ($isIncoming): ?>
                                            <?= h($transaction->account->user->name ?? 'Unbekannt') ?>
                                        <?php else: ?>
                                            <?= h($transaction->empfaenger_name) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="transaction-meta">
                                        <?= h($transaction->datum->format('d.m.Y')) ?>
                                        <?php if ($transaction->zahlungszweck): ?>
                                            · <?= h(\Cake\Utility\Text::truncate($transaction->zahlungszweck, 30)) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="transaction-amount <?= $isIncoming ? 'text-success' : 'text-danger' ?>">
                                <?= $isIncoming ? '+' : '-' ?><?= $this->Number->currency($transaction->betrag, 'EUR') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($account->transactions->count() > 8): ?>
                    <a href="/accounts/history/<?= $account->id ?>" class="view-all-link">
                        <i class="bi bi-arrow-right me-1"></i>Alle <?= $account->transactions->count() ?> Umsätze anzeigen
                    </a>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4"></i>
                        <p class="mt-3 mb-0">Noch keine Umsätze vorhanden</p>
                        <a href="/transactions/add" class="btn btn-primary mt-3">
                            <i class="bi bi-send me-1"></i>Erste Überweisung tätigen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php else: ?>
<!-- Admin-Ansicht: Detaillierte Ansicht mit Aktionen -->
<div class="row">
    <!-- Kontoinformationen -->
    <div class="col-lg-4 mb-4">
        <div class="card account-card">
            <div class="card-header bg-primary text-white">
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
