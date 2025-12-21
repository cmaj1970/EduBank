<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-people me-2"></i><?= __('Übungsfirmen') ?></h3>
    <a href="/users/add" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i><?= __('Neue Übungsfirma') ?>
    </a>
</div>

<?php if (isset($isSuperadmin) && $isSuperadmin && !empty($schoolList)): ?>
<!-- Filter für Superadmin -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-center" id="filterForm">
            <div class="col-md-4">
                <select name="school_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value=""><?= __('Alle Schulen') ?></option>
                    <?php foreach ($schoolList as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($selectedSchool ?? '') == $id ? 'selected' : '' ?>>
                        <?= h($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="<?= __('Suche nach Name, Benutzername oder Schule...') ?>"
                       value="<?= h($search ?? '') ?>"
                       id="userSearch">
            </div>
            <div class="col-md-2">
                <?php if (!empty($selectedSchool) || !empty($search)): ?>
                <a href="/users" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x-lg"></i> <?= __('Reset') ?>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($isSchoolAdmin): ?>
<!-- Password Info for School Admin -->
<div class="alert alert-info d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-key-fill fs-4 me-3"></i>
    <div class="flex-grow-1">
        <strong>Passwort für alle Übungsfirmen:</strong>
        <code class="ms-2 fs-5 user-select-all"><?= h($defaultPassword) ?></code>
        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="navigator.clipboard.writeText('<?= h($defaultPassword) ?>'); this.innerHTML='<i class=\'bi bi-check\'></i> Kopiert'; setTimeout(() => this.innerHTML='<i class=\'bi bi-clipboard\'></i>', 2000);">
            <i class="bi bi-clipboard"></i>
        </button>
    </div>
    <small class="text-muted ms-3">Dieses Passwort an Schüler:innen weitergeben</small>
</div>
<?php endif; ?>

<?php if ($users->isEmpty()): ?>
<!-- Empty State -->
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
            <i class="bi bi-people text-info" style="font-size: 3rem;"></i>
        </div>
        <h4 class="mb-3">Noch keine Übungsfirmen vorhanden</h4>
        <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
            Übungsfirmen sind die Schülergruppen oder Teams, die jeweils ein eigenes Bankkonto erhalten.
            Jede Übungsfirma kann Überweisungen an andere Übungsfirmen tätigen.
        </p>

        <div class="card bg-light border-0 mb-4 mx-auto" style="max-width: 400px;">
            <div class="card-body text-start">
                <h6 class="card-title"><i class="bi bi-lightbulb me-2"></i>Zum Erstellen benötigen Sie:</h6>
                <ul class="mb-0 text-muted small">
                    <li>Einen Namen für die Übungsfirma</li>
                    <li>Einen eindeutigen Benutzernamen</li>
                    <li>Ein Passwort für die Anmeldung</li>
                </ul>
            </div>
        </div>

        <a href="/users/add" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-lg me-2"></i>Erste Übungsfirma erstellen
        </a>
    </div>
</div>

<?php else: ?>
<!-- Users Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <th><?= $this->Paginator->sort('name', 'Name') ?></th>
                    <th><?= $this->Paginator->sort('username', 'Benutzername') ?></th>
                    <th><?= $this->Paginator->sort('school_id', 'Schule') ?></th>
                    <th class="text-center"><?= $this->Paginator->sort('active', 'Status') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Erstellt') ?></th>
                    <th class="text-end"><?= __('Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <strong><?= h($user->name) ?></strong>
                    </td>
                    <td>
                        <code><?= h($user->username) ?></code>
                    </td>
                    <td>
                        <?= $user->has('school') ? h($user->school->name) : '<span class="text-muted">-</span>' ?>
                    </td>
                    <td class="text-center">
                        <?php if ($user->active): ?>
                            <span class="badge bg-success">Aktiv</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inaktiv</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><?= h($user->created->format('d.m.Y')) ?></small>
                    </td>
                    <td class="text-end">
                        <div class="d-flex flex-column gap-1">
                            <?php if ($isSchoolAdmin): ?>
                            <?= $this->Html->link(
                                'Anmelden als',
                                ['action' => 'impersonate', $user->id],
                                ['class' => 'btn btn-sm btn-outline-success']
                            ) ?>
                            <?php endif; ?>
                            <?= $this->Html->link(
                                'Ansehen',
                                ['action' => 'view', $user->id],
                                ['class' => 'btn btn-sm btn-outline-primary']
                            ) ?>
                            <?= $this->Html->link(
                                'Bearbeiten',
                                ['action' => 'edit', $user->id],
                                ['class' => 'btn btn-sm btn-outline-secondary']
                            ) ?>
                            <?= $this->Form->postLink(
                                'Löschen',
                                ['action' => 'delete', $user->id],
                                ['class' => 'btn btn-sm btn-outline-danger', 'confirm' => __('Übungsfirma "{0}" wirklich löschen?', $user->name)]
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
            <?= $this->Paginator->counter('Zeige {{start}}-{{end}} von {{count}} Übungsfirmen') ?>
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
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('userSearch');
    if (searchInput) {
        var timeout = null;

        // Fokus wiederherstellen nach Reload
        if (searchInput.value) {
            searchInput.focus();
            searchInput.selectionStart = searchInput.selectionEnd = searchInput.value.length;
        }

        // Suche bei Enter oder nach 800ms Pause
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                document.getElementById('filterForm').submit();
            }, 800);
        });
    }
});
</script>
