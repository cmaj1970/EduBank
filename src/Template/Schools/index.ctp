<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School[]|\Cake\Collection\CollectionInterface $schools
 * @var array $schoolStats
 * @var array $schoolList
 * @var string|null $selectedSchool
 * @var string|null $search
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-building me-2"></i><?= __('Schulen') ?></h3>
    <a href="/schools/add" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i><?= __('Neue Schule') ?>
    </a>
</div>

<?php if (isset($isSuperadmin) && $isSuperadmin && !empty($schoolList)): ?>
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
                       placeholder="<?= __('Suche nach Name oder Kurzname...') ?>"
                       value="<?= h($search ?? '') ?>"
                       id="schoolSearch">
            </div>
            <div class="col-md-2">
                <?php if (!empty($selectedSchool) || !empty($search)): ?>
                <a href="/schools" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="bi bi-x-lg"></i> <?= __('Reset') ?>
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0 table-sm">
            <thead class="table-primary">
                <tr>
                    <th><?= $this->Paginator->sort('name', 'Schulname') ?></th>
                    <th><?= $this->Paginator->sort('kurzname', 'Kurzname') ?></th>
                    <th class="text-center"><?= __('Übungsfirmen') ?></th>
                    <th class="text-center"><?= __('Konten') ?></th>
                    <th class="text-center"><?= __('Transaktionen') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Erstellt') ?></th>
                    <th class="text-end"><?= __('Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schools as $school): ?>
                <tr>
                    <td>
                        <strong><?= h($school->name) ?></strong><br>
                        <small class="text-muted">
                            IBAN: <code><?= h($school->ibanprefix) ?></code> |
                            BIC: <code><?= h($school->bic) ?></code>
                        </small>
                    </td>
                    <td>
                        <code><?= h($school->kurzname) ?></code>
                    </td>
                    <td class="text-center">
                        <strong><?= $schoolStats[$school->id]['users'] ?? 0 ?></strong><br>
                        <small class="text-muted">
                            <?php if (!empty($schoolStats[$school->id]['lastUser'])): ?>
                                <?= $schoolStats[$school->id]['lastUser']->format('d.m.Y') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </small>
                    </td>
                    <td class="text-center">
                        <strong><?= $schoolStats[$school->id]['accounts'] ?? 0 ?></strong><br>
                        <small class="text-muted">
                            <?php if (!empty($schoolStats[$school->id]['lastAccount'])): ?>
                                <?= $schoolStats[$school->id]['lastAccount']->format('d.m.Y') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </small>
                    </td>
                    <td class="text-center">
                        <strong><?= $schoolStats[$school->id]['transactions'] ?? 0 ?></strong><br>
                        <small class="text-muted">
                            <?php if (!empty($schoolStats[$school->id]['lastTransaction'])): ?>
                                <?= $schoolStats[$school->id]['lastTransaction']->format('d.m.Y') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </small>
                    </td>
                    <td>
                        <small><?= h($school->created->format('d.m.Y')) ?></small>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?php if (isset($isSuperadmin) && $isSuperadmin && isset($schoolAdmins[$school->id])): ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-person-badge"></i>',
                                ['controller' => 'Users', 'action' => 'impersonate', $schoolAdmins[$school->id]->id],
                                ['class' => 'btn btn-outline-warning', 'escape' => false, 'title' => 'Anmelden als']
                            ) ?>
                            <?php endif; ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $school->id],
                                ['class' => 'btn btn-outline-primary', 'escape' => false, 'title' => 'Ansehen']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $school->id],
                                ['class' => 'btn btn-outline-secondary', 'escape' => false, 'title' => 'Bearbeiten']
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $school->id],
                                ['class' => 'btn btn-outline-danger', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('ACHTUNG: Schule "{0}" löschen?\n\nDies löscht ALLE zugehörigen:\n- Admins und Übungsfirmen\n- Konten\n- Transaktionen\n\nDieser Vorgang kann nicht rückgängig gemacht werden!', $school->name)]
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
            <?= $this->Paginator->counter('Zeige {{start}}-{{end}} von {{count}} Schulen') ?>
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
// Dynamische Suche mit Debounce
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('schoolSearch');
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
