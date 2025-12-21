<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-clock-history me-2"></i><?= h($account->name) ?> - Auftragshistorie</h3>
    <a href="/transactions/add/<?= $account->id ?>" class="btn btn-primary">
        <i class="bi bi-arrow-right-circle me-1"></i><?= __('Neue Überweisung') ?>
    </a>
</div>

<div class="row">
    <!-- Kontodaten Card -->
    <div class="col-lg-4 mb-4">
        <?= $this->element('account_sidebar', ['account' => $account, 'authuser' => $authuser, 'currentPage' => 'history']) ?>
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
