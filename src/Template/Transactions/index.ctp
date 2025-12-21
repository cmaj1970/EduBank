<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction[]|\Cake\Collection\CollectionInterface $transactions
 */
?>

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
            Es wurden noch keine Überweisungen getätigt. Sobald Übungsfirmen Transaktionen durchführen, werden diese hier angezeigt.
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
                        <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($transaction->empfaenger_iban) ?>" title="IBAN kopieren">
                            <i class="bi bi-clipboard"></i>
                        </button>
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
                        <div class="d-flex flex-column gap-1">
                            <?= $this->Html->link(
                                'Ansehen',
                                ['action' => 'view', $transaction->id],
                                ['class' => 'btn btn-sm btn-outline-primary']
                            ) ?>
                            <?= $this->Html->link(
                                'Bearbeiten',
                                ['action' => 'edit', $transaction->id],
                                ['class' => 'btn btn-sm btn-outline-secondary']
                            ) ?>
                            <?= $this->Form->postLink(
                                'Löschen',
                                ['action' => 'delete', $transaction->id],
                                ['class' => 'btn btn-sm btn-outline-danger', 'confirm' => __('Transaktion wirklich löschen?')]
                            ) ?>
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
        # Bootstrap 5 Pagination Templates
        $this->Paginator->setTemplates([
            'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'current' => '<li class="page-item active"><span class="page-link">{{text}}</span></li>',
            'first' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'last' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'prevDisabled' => '<li class="page-item disabled"><span class="page-link">{{text}}</span></li>',
            'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
            'nextDisabled' => '<li class="page-item disabled"><span class="page-link">{{text}}</span></li>',
        ]);
        ?>
        <nav>
            <ul class="pagination pagination-sm mb-0">
                <?= $this->Paginator->first('«', ['escape' => false]) ?>
                <?= $this->Paginator->prev('‹', ['escape' => false]) ?>
                <?= $this->Paginator->numbers(['modulus' => 3]) ?>
                <?= $this->Paginator->next('›', ['escape' => false]) ?>
                <?= $this->Paginator->last('»', ['escape' => false]) ?>
            </ul>
        </nav>
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
<?php endif; ?>
