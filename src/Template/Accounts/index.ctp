<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account[]|\Cake\Collection\CollectionInterface $accounts
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-wallet2 me-2"></i><?= __('Konten') ?></h3>
    <div class="d-flex gap-2">
        <a href="/accounts/directory" class="btn btn-outline-primary">
            <i class="bi bi-building me-1"></i><?= __('Konten aller teilnehmenden Übungsfirmen') ?>
        </a>
        <?php if($authuser['role'] == 'admin'): ?>
        <a href="/accounts/add" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i><?= __('Neues Konto') ?>
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($isSuperadmin) && $isSuperadmin && !empty($userList)): ?>
<!-- Filter für Superadmin -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-center" id="filterForm">
            <div class="col-md-4">
                <select name="user_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value=""><?= __('Alle Übungsfirmen') ?></option>
                    <?php foreach ($userList as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($selectedUser ?? '') == $id ? 'selected' : '' ?>>
                        <?= h($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="<?= __('Suche nach Übungsfirma, Kontoname oder IBAN...') ?>"
                       value="<?= h($search ?? '') ?>"
                       id="accountSearch">
            </div>
            <div class="col-md-2">
                <?php if (!empty($selectedUser) || !empty($search)): ?>
                <a href="/accounts" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x-lg"></i> <?= __('Reset') ?>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

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
                        <div class="d-flex flex-column gap-1">
                            <?= $this->Html->link(
                                'Ansehen',
                                ['action' => 'view', $account->id],
                                ['class' => 'btn btn-sm btn-outline-primary']
                            ) ?>
                            <?php if($authuser['role'] == 'admin'): ?>
                            <?= $this->Html->link(
                                'Bearbeiten',
                                ['action' => 'edit', $account->id],
                                ['class' => 'btn btn-sm btn-outline-secondary']
                            ) ?>
                            <?= $this->Form->postLink(
                                'Zurücksetzen',
                                ['action' => 'reset', $account->id],
                                ['class' => 'btn btn-sm btn-outline-warning', 'confirm' => __('Konto "{0}" auf Standardwerte zurücksetzen und alle Transaktionen löschen?', $account->name)]
                            ) ?>
                            <?= $this->Form->postLink(
                                'Löschen',
                                ['action' => 'delete', $account->id],
                                ['class' => 'btn btn-sm btn-outline-danger', 'confirm' => __('Konto "{0}" wirklich löschen?', $account->name)]
                            ) ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">
            <?= $this->Paginator->counter('Zeige {{start}}-{{end}} von {{count}} Konten') ?>
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
    // IBAN kopieren
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

    // Dynamische Suche mit Debounce
    var searchInput = document.getElementById('accountSearch');
    if (searchInput) {
        var timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 400);
        });
    }
});
</script>
<?php endif; ?>
