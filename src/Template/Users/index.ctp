<?php
/**
 * Übungsfirmen-Übersicht für Schuladmins
 * Flache Tabelle mit Link zur Detailseite
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */

# Schule des aktuellen Admins
$school = isset($loggedinschool) ? $loggedinschool : null;

# iframe-Modus: Minimales Layout ohne Header
$isIframe = $this->request->getQuery('iframe') === '1';
?>

<?php if (!$isIframe): ?>
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">

        <!-- Header mit Titel und Erstellen-Button -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h3 class="mb-2"><i class="bi bi-building me-2"></i><?= $school ? h($school['name']) : 'Übungsfirmen' ?></h3>
                <p class="text-muted mb-0">
                    Verwalten Sie die Übungsfirmen Ihrer Schule
                </p>
            </div>
            <a href="/users/add" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Neue Übungsfirma
            </a>
        </div>

        <?php if (isset($isSuperadmin) && $isSuperadmin && !empty($schoolList)): ?>
        <!-- Filter für Superadmin -->
        <div class="card mb-4">
            <div class="card-body py-2">
                <form method="get" class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <select name="school_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Alle Schulen</option>
                            <?php foreach ($schoolList as $id => $name): ?>
                            <option value="<?= $id ?>" <?= ($selectedSchool ?? '') == $id ? 'selected' : '' ?>>
                                <?= h($name) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Suche..." value="<?= h($search ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <?php if (!empty($selectedSchool) || !empty($search)): ?>
                        <a href="/users" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Info für Schuladmin: Passwort + Tipp -->
        <?php if (!empty($isSchoolAdmin)): ?>
        <div class="alert alert-info border-info mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-key me-2 fs-5"></i>
                <div>
                    <strong>Kennwort für alle Übungsfirmen:</strong>
                    <code class="ms-2 fs-6"><?= h($defaultPassword ?? 'Schueler2024') ?></code>
                </div>
            </div>
            <hr class="my-2">
            <small class="text-muted">
                <i class="bi bi-lightbulb me-1"></i>
                <strong>Tipp:</strong> Mit "Anmelden als" können Sie sich direkt als Übungsfirma einloggen.
            </small>
        </div>
        <?php endif; ?>

<?php endif; ?>

<?php if ($users->isEmpty()): ?>
        <!-- Keine Übungsfirmen -->
        <div class="text-center py-5">
            <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3">Noch keine Übungsfirmen</h5>
            <p class="text-muted">Erstellen Sie die erste Übungsfirma für Ihre Schüler.</p>
            <a href="/users/add" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Erste Übungsfirma erstellen
            </a>
        </div>

<?php else: ?>

        <!-- Tabelle -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%"><?= $this->Paginator->sort('name', 'Firmenname') ?></th>
                                <th style="width: 25%"><?= $this->Paginator->sort('username', 'Benutzername') ?></th>
                                <th style="width: 15%" class="text-center">Konten</th>
                                <th style="width: 25%" class="text-end">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <?php $accountCount = count($user->accounts); ?>
                            <tr>
                                <td>
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <strong><?= h($user->name) ?></strong>
                                </td>
                                <td>
                                    <code><?= h($user->username) ?></code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $accountCount ?></span>
                                </td>
                                <td class="text-end">
                                    <?= $this->Html->link(
                                        '<i class="bi bi-eye me-1"></i>Details',
                                        ['action' => 'view', $user->id],
                                        ['class' => 'btn btn-sm btn-outline-primary me-1', 'escape' => false]
                                    ) ?>
                                    <?php if (!empty($isSchoolAdmin) || !empty($isSuperadmin)): ?>
                                    <a href="/users/impersonate/<?= $user->id ?>" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($this->Paginator->total() > 1): ?>
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    <?= $this->Paginator->counter('{{start}}-{{end}} von {{count}} Übungsfirmen') ?>
                </small>
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
                        <?= $this->Paginator->prev() ?>
                        <?= $this->Paginator->numbers(['modulus' => 3]) ?>
                        <?= $this->Paginator->next() ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>

<?php endif; ?>

<?php if (!$isIframe): ?>
    </div>
</div>
<?php endif; ?>
