<?php
/**
 * Transaktionen-Übersicht
 * - Schuladmin: Live-Feed mit Auto-Refresh
 * - Superadmin: Tabelle mit CRUD
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction[]|\Cake\Collection\CollectionInterface $transactions
 */

# Schuladmin-Check
$isSchoolAdmin = isset($loggedinschool);

# iframe-Modus: Minimales Layout
$isIframe = $this->request->getQuery('iframe') === '1';
?>

<?php if ($isSchoolAdmin): ?>
<!-- ===================== SCHULADMIN: LIVE-FEED ===================== -->

<?php if (!$isIframe): ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-activity me-2"></i>Live-Transaktionen
    </h4>
    <button class="btn btn-outline-secondary btn-sm" id="refreshFeed" title="Aktualisieren">
        <i class="bi bi-arrow-clockwise"></i>
    </button>
</div>
<?php endif; ?>

<div class="card" <?= $this->HelpText->attr('schuladmin', 'transaktionen') ?>>
    <?php if (!$isIframe): ?>
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-2"></i>Letzte Überweisungen</span>
        <small class="text-muted" id="txCount"><?= count($transactions) ?> Einträge</small>
    </div>
    <?php endif; ?>

    <div class="transaction-feed-wrapper">
        <div class="list-group list-group-flush" id="transactionFeed">
            <?php if (empty($transactions) || $transactions->isEmpty()): ?>
            <div class="list-group-item text-center text-muted py-4">
                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">Keine Transaktionen</p>
            </div>
            <?php else: ?>
                <?php foreach ($transactions as $tx): ?>
                <?php
                    $senderName = $tx->account_id ? ($tx->account->user->name ?? 'Unbekannt') : $tx->empfaenger_name;
                    $senderId = $tx->account->user->id ?? 0;
                    $senderSchoolId = $tx->account->user->school_id ?? 0;

                    # Empfänger-Schule ermitteln
                    $recipientSchool = null;
                    if (isset($recipientAccounts[$tx->empfaenger_iban])) {
                        $recipientAcc = $recipientAccounts[$tx->empfaenger_iban];
                        if (!empty($recipientAcc->user->school) && $recipientAcc->user->school_id != $senderSchoolId) {
                            $recipientSchool = $recipientAcc->user->school->name;
                        }
                    }
                ?>
                <div class="list-group-item py-2 transaction-item" data-tx-id="<?= $tx->id ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="small">
                            <span class="text-muted"><?= $tx->created->format('d.m.Y H:i') ?></span>
                            <br>
                            <strong><?= h($senderName) ?></strong>
                            <i class="bi bi-arrow-right mx-1 text-muted"></i>
                            <?= $this->Number->currency($tx->betrag, 'EUR') ?>
                            an <strong><?= h($tx->empfaenger_name) ?></strong>
                            <?php if ($recipientSchool): ?>
                            <span class="text-muted">(<?= h($recipientSchool) ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($tx->zahlungszweck)): ?>
                    <small class="text-muted d-block mt-1">
                        <i class="bi bi-chat-left-text me-1"></i><?= h($tx->zahlungszweck) ?>
                    </small>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Transaktions-Items hover */
.transaction-item:hover {
    background-color: #f8f9fa;
}

/* Neue Transaktionen hervorheben */
.transaction-item.new-transaction {
    background-color: #d4edda;
    transition: background-color 0.5s ease;
}

/* Spin-Animation für Refresh */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.spin {
    animation: spin 0.5s linear infinite;
}

<?php if ($isIframe): ?>
/* iframe-Modus: Kein Padding */
body {
    padding: 0 !important;
}
.container-fluid {
    padding: 0.5rem !important;
}
.card {
    border: none !important;
    box-shadow: none !important;
}
<?php endif; ?>
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var transactionFeed = document.getElementById('transactionFeed');
    var refreshBtn = document.getElementById('refreshFeed');
    var txCount = document.getElementById('txCount');

    // Bekannte Transaktions-IDs
    var knownTransactionIds = {};
    document.querySelectorAll('.transaction-item').forEach(function(item) {
        var id = item.getAttribute('data-tx-id');
        if (id) knownTransactionIds[id] = true;
    });

    // Währung formatieren
    function formatCurrency(amount) {
        return new Intl.NumberFormat('de-AT', { style: 'currency', currency: 'EUR' }).format(amount);
    }

    // Transaktion als HTML
    function renderTransaction(tx, isNew) {
        var schoolInfo = tx.recipient_school ? ' <span class="text-muted">(' + tx.recipient_school + ')</span>' : '';
        var purposeHtml = tx.purpose ? '<small class="text-muted d-block mt-1"><i class="bi bi-chat-left-text me-1"></i>' + tx.purpose + '</small>' : '';
        var newClass = isNew ? ' new-transaction' : '';

        return '<div class="list-group-item py-2 transaction-item' + newClass + '" data-tx-id="' + tx.id + '">' +
            '<div class="d-flex justify-content-between align-items-start">' +
            '<div class="small">' +
            '<span class="text-muted">' + tx.created + '</span><br>' +
            '<strong>' + tx.sender_name + '</strong> ' +
            '<i class="bi bi-arrow-right mx-1 text-muted"></i> ' +
            formatCurrency(tx.amount) + ' an <strong>' + tx.recipient_name + '</strong>' + schoolInfo +
            '</div></div>' + purposeHtml + '</div>';
    }

    // Feed via AJAX aktualisieren
    function refreshTransactions() {
        var icon = refreshBtn ? refreshBtn.querySelector('i') : null;
        if (icon) icon.classList.add('spin');

        fetch('/users/ajax-transactions')
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.transactions && transactionFeed) {
                    var html = '';
                    var newIds = [];

                    data.transactions.forEach(function(tx) {
                        var isNew = !knownTransactionIds[tx.id];
                        if (isNew) newIds.push(tx.id);
                        html += renderTransaction(tx, isNew);
                    });
                    transactionFeed.innerHTML = html;

                    // Neue IDs merken
                    newIds.forEach(function(id) {
                        knownTransactionIds[id] = true;
                    });

                    // Highlight nach 60 Sekunden entfernen
                    if (newIds.length > 0) {
                        setTimeout(function() {
                            document.querySelectorAll('.transaction-item.new-transaction').forEach(function(item) {
                                item.classList.remove('new-transaction');
                            });
                        }, 60000);
                    }

                    // Anzahl aktualisieren
                    if (txCount) txCount.textContent = data.count + ' Einträge';
                }
            })
            .catch(function(err) { console.error('Feed refresh failed:', err); })
            .finally(function() {
                if (icon) icon.classList.remove('spin');
            });
    }

    // Manueller Refresh
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            refreshTransactions();
        });
    }

    // Auto-Refresh alle 10 Sekunden
    setInterval(refreshTransactions, 10000);
});
</script>

<?php else: ?>
<!-- ===================== SUPERADMIN: TABELLE ===================== -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i><?= __('Transaktionen') ?></h3>
</div>

<?php if ($transactions->isEmpty()): ?>
<!-- Empty State -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
            <i class="bi bi-arrow-left-right text-warning" style="font-size: 3rem;"></i>
        </div>
        <h4 class="mb-3">Keine Transaktionen vorhanden</h4>
        <p class="text-muted mb-0" style="max-width: 400px; margin: 0 auto;">
            Es wurden noch keine Überweisungen getätigt.
        </p>
    </div>
</div>

<?php else: ?>
<!-- Transactions Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th><?= $this->Paginator->sort('account_id', 'Konto') ?></th>
                    <th><?= $this->Paginator->sort('empfaenger_name', 'Empfänger') ?></th>
                    <th><?= $this->Paginator->sort('empfaenger_iban', 'IBAN') ?></th>
                    <th class="text-end"><?= $this->Paginator->sort('betrag', 'Betrag') ?></th>
                    <th><?= $this->Paginator->sort('datum', 'Datum') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Erstellt') ?></th>
                    <th class="text-end"><?= __('Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td>
                        <?= $transaction->has('account') ? $this->Html->link($transaction->account->name, ['controller' => 'Accounts', 'action' => 'view', $transaction->account->id]) : '-' ?>
                    </td>
                    <td>
                        <strong><?= h($transaction->empfaenger_name) ?></strong>
                        <?php if ($transaction->empfaenger_adresse): ?>
                        <br><small class="text-muted"><?= h($transaction->empfaenger_adresse) ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <code><?= h($transaction->empfaenger_iban) ?></code>
                        <?php if ($transaction->empfaenger_bic): ?>
                        <br><small class="text-muted">BIC: <?= h($transaction->empfaenger_bic) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <span class="text-danger fw-bold">
                            -<?= $this->Number->currency($transaction->betrag, 'EUR') ?>
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <?= h($transaction->datum->format('d.m.Y')) ?>
                    </td>
                    <td class="text-nowrap">
                        <small><?= h($transaction->created->format('d.m.Y H:i')) ?></small>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?= $this->Html->link('<i class="bi bi-eye"></i>', ['action' => 'view', $transaction->id], ['class' => 'btn btn-outline-primary', 'escape' => false, 'title' => 'Ansehen']) ?>
                            <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $transaction->id], ['class' => 'btn btn-outline-secondary', 'escape' => false, 'title' => 'Bearbeiten']) ?>
                            <?= $this->Form->postLink('<i class="bi bi-trash"></i>', ['action' => 'delete', $transaction->id], ['class' => 'btn btn-outline-danger', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('Transaktion wirklich löschen?')]) ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">
            <?= $this->Paginator->counter('Zeige {{start}}-{{end}} von {{count}} Transaktionen') ?>
        </small>
        <?php if ($this->Paginator->total() > 1): ?>
        <?php
        $this->Paginator->setTemplates([
            'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="page-item active"><span class="page-link">{{text}}</span></li>',
            'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}">‹</a></li>',
            'prevDisabled' => '<li class="page-item disabled"><span class="page-link">‹</span></li>',
            'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}">›</a></li>',
            'nextDisabled' => '<li class="page-item disabled"><span class="page-link">›</span></li>',
        ]);
        ?>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?= $this->Paginator->prev('‹', ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['modulus' => 3]) ?>
                <?= $this->Paginator->next('›', ['escape' => false]) ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>
