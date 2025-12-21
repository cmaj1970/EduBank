<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account[]|\Cake\Collection\CollectionInterface $accounts
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-wallet2 me-2"></i><?= __('Konten') ?></h3>
    <?php if($authuser['role'] == 'admin'): ?>
    <a href="/accounts/add" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i><?= __('Neues Konto') ?>
    </a>
    <?php endif; ?>
</div>

<?php if ($accounts->isEmpty()): ?>
<!-- Empty State -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
            <i class="bi bi-wallet2 text-success" style="font-size: 3rem;"></i>
        </div>
        <h4 class="mb-3">Noch keine Konten vorhanden</h4>
        <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
            Jede Übungsfirma benötigt ein Bankkonto, um Überweisungen tätigen zu können.
            Konten werden automatisch beim Erstellen einer Übungsfirma angelegt.
        </p>

        <div class="card bg-light border-0 mb-4 mx-auto" style="max-width: 450px;">
            <div class="card-body text-start">
                <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>So erhalten Übungsfirmen Konten:</h6>
                <ol class="mb-0 text-muted small">
                    <li>Erstellen Sie zunächst eine Übungsfirma</li>
                    <li>Ein Konto wird automatisch mit angelegt</li>
                    <li>Das Konto erhält eine eigene IBAN und BIC</li>
                </ol>
            </div>
        </div>

        <?php if($authuser['role'] == 'admin'): ?>
        <a href="/users/add" class="btn btn-primary btn-lg">
            <i class="bi bi-people me-2"></i>Übungsfirma erstellen
        </a>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- Accounts Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <th><?= $this->Paginator->sort('user_id', 'Übungsfirma') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('name', 'Kontoname') ?></th>
                    <th><?= $this->Paginator->sort('iban', 'IBAN') ?></th>
                    <th class="text-end"><?= __('Kontostand') ?></th>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <th><?= $this->Paginator->sort('created', 'Erstellt') ?></th>
                    <?php endif; ?>
                    <th class="text-end"><?= __('Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <td><?= $account->has('user') ? h($account->user->name) : '-' ?></td>
                    <?php endif; ?>
                    <td>
                        <strong><?= h($account->name) ?></strong>
                    </td>
                    <td>
                        <code class="iban-display"><?= h($account->iban) ?></code>
                        <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                            <i class="bi bi-clipboard"></i>
                        </button>
                        <br><small class="text-muted">BIC: <?= h($account->bic) ?></small>
                    </td>
                    <td class="text-end">
                        <?php
                        $balanceClass = $account->balance >= 0 ? 'text-success' : 'text-danger';
                        ?>
                        <span class="<?= $balanceClass ?> fw-bold">
                            <?= $this->Number->currency($account->balance, 'EUR') ?>
                        </span>
                        <br><small class="text-muted">Limit: <?= $this->Number->currency($account->maxlimit, 'EUR') ?></small>
                    </td>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <td>
                        <small><?= h($account->created->format('d.m.Y')) ?></small>
                    </td>
                    <?php endif; ?>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $account->id],
                                ['class' => 'btn btn-outline-primary', 'escape' => false, 'title' => 'Anzeigen']
                            ) ?>
                            <?php if($authuser['role'] == 'admin'): ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $account->id],
                                ['class' => 'btn btn-outline-secondary', 'escape' => false, 'title' => 'Bearbeiten']
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-arrow-counterclockwise"></i>',
                                ['action' => 'reset', $account->id],
                                ['class' => 'btn btn-outline-warning', 'escape' => false, 'title' => 'Zurücksetzen', 'confirm' => __('Konto "{0}" auf Standardwerte zurücksetzen und alle Transaktionen löschen?', $account->name)]
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $account->id],
                                ['class' => 'btn btn-outline-danger', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('Konto "{0}" wirklich löschen?', $account->name)]
                            ) ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($this->Paginator->total() > 1): ?>
    <div class="card-footer">
        <nav aria-label="Seitennavigation">
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <?= $this->Paginator->first('<i class="bi bi-chevron-double-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->prev('<i class="bi bi-chevron-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next('<i class="bi bi-chevron-right"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->last('<i class="bi bi-chevron-double-right"></i>', ['escape' => false]) ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
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
