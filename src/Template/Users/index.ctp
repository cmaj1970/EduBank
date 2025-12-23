<?php
/**
 * Übungsfirmen-Übersicht für Schuladmins
 * Zeigt alle Übungsfirmen mit ihren Konten
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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-building me-2"></i><?= $school ? h($school['name']) : 'Übungsfirmen' ?>
    </h4>
    <div class="d-flex gap-2">
        <a href="/users/add" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Neue Übungsfirma
        </a>
    </div>
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

<div class="card">
    <div class="card-header py-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-2"></i>Übungsfirmen & Konten</span>
        <small class="text-muted"><?= $users->count() ?> Übungsfirmen</small>
    </div>
    <div class="table-wrapper" <?= $this->HelpText->attr('schuladmin', 'firmen_tabelle') ?>>
        <table class="table mb-0" id="firmenTable">
            <thead class="table-light">
                <tr>
                    <th>Name / Konto</th>
                    <th>Details</th>
                    <th class="text-end">Kontostand</th>
                    <th style="width: 120px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <?php $hasAccounts = !empty($user->accounts); ?>
                <?php
                    # Letzter Login formatieren
                    $lastLogin = null;
                    if ($user->last_login) {
                        $diff = time() - $user->last_login->getTimestamp();
                        if ($diff < 3600) {
                            $lastLogin = 'vor ' . max(1, round($diff / 60)) . ' Min';
                        } elseif ($diff < 86400) {
                            $lastLogin = 'vor ' . round($diff / 3600) . ' Std';
                        } else {
                            $lastLogin = $user->last_login->format('d.m.Y H:i');
                        }
                    }
                ?>
                <!-- Übungsfirma-Zeile -->
                <tr class="firma-row" data-user-id="<?= $user->id ?>">
                    <td>
                        <strong><?= h($user->name) ?></strong>
                        <br>
                        <small class="text-muted">Login: <?= h($user->username) ?></small>
                    </td>
                    <td>
                        <?php if ($user->active): ?>
                            <span class="badge bg-success me-2">Aktiv</span>
                        <?php else: ?>
                            <span class="badge bg-secondary me-2">Inaktiv</span>
                        <?php endif; ?>
                        <span <?= $this->HelpText->attr('schuladmin', 'last_login') ?>>
                        <?php if ($lastLogin): ?>
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i><?= $lastLogin ?>
                        </small>
                        <?php else: ?>
                        <small class="text-muted">Noch nie angemeldet</small>
                        <?php endif; ?>
                        </span>
                    </td>
                    <td></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <?php if ($isSchoolAdmin): ?>
                            <a href="/users/impersonate/<?= $user->id ?>" class="btn btn-outline-success" title="Anmelden als" <?= $this->HelpText->attr('schuladmin', 'btn_impersonate') ?>>
                                <i class="bi bi-box-arrow-in-right"></i>
                            </a>
                            <?php endif; ?>
                            <a href="/users/edit/<?= $user->id ?>" class="btn btn-outline-secondary" title="Bearbeiten" <?= $this->HelpText->attr('schuladmin', 'btn_edit_firma') ?>>
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?= $this->Html->link(
                                '<i class="bi bi-arrow-right"></i>',
                                ['controller' => 'Users', 'action' => 'view', $user->id],
                                ['class' => 'btn btn-outline-primary', 'escape' => false, 'title' => 'Details']
                            ) ?>
                        </div>
                    </td>
                </tr>
                <!-- Konto-Zeilen -->
                <?php if ($hasAccounts): ?>
                    <?php foreach ($user->accounts as $account): ?>
                    <tr class="konto-row" data-user-id="<?= $user->id ?>" <?= $this->HelpText->attr('schuladmin', 'konto_zeile') ?>>
                        <td class="ps-4">
                            <i class="bi bi-credit-card text-muted me-2"></i>
                            <strong><?= h($account->name ?: 'Girokonto') ?></strong>
                            <br>
                            <span class="font-monospace small text-muted ps-4"><?= h($account->iban) ?></span>
                            <button type="button" class="btn btn-link btn-sm p-0 ms-1 copy-iban"
                                    data-iban="<?= h($account->iban) ?>" title="IBAN kopieren" <?= $this->HelpText->attr('schuladmin', 'copy_iban') ?>>
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </td>
                        <td>
                            <small class="text-muted">
                                Limit: <?= $this->Number->currency($account->maxlimit ?? 0, 'EUR') ?>
                            </small>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $this->Number->currency($account->balance, 'EUR') ?>
                            </span>
                        </td>
                        <td>
                            <span <?= $this->HelpText->attr('schuladmin', 'btn_edit_konto') ?>>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['controller' => 'Accounts', 'action' => 'edit', $account->id],
                                ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false, 'title' => 'Konto bearbeiten']
                            ) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($this->Paginator->total() > 1): ?>
    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">
            <?= $this->Paginator->counter('{{start}}-{{end}} von {{count}}') ?>
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

<style>
/* Übungsfirma-Zeilen: blauer Akzent links */
.firma-row {
    background-color: #fff;
    border-top: 2px solid #dee2e6 !important;
}
.firma-row:first-child {
    border-top: none !important;
}
.firma-row td {
    padding-top: 0.75rem;
    padding-bottom: 0.5rem;
}
.firma-row td:first-child {
    border-left: 3px solid #0d6efd;
}

/* Konto-Zeilen: hellblauer Hintergrund */
.konto-row {
    background-color: #f0f7ff !important;
    font-size: 0.9rem;
}
.konto-row td {
    border-top: none !important;
    padding-top: 0.25rem;
    padding-bottom: 0.25rem;
}

/* Letzte Konto-Zeile vor nächster Firma */
.konto-row:has(+ .firma-row) td,
.konto-row:last-child td {
    padding-bottom: 0.5rem;
}

/* Copy-Button Animation */
.copy-iban .bi-check {
    color: #198754;
}

<?php if ($isIframe): ?>
/* iframe-Modus: Kein Padding */
body {
    padding: 0 !important;
}
.container-fluid {
    padding: 0.5rem !important;
}
<?php endif; ?>
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // IBAN kopieren
    document.querySelectorAll('.copy-iban').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var iban = this.getAttribute('data-iban');
            navigator.clipboard.writeText(iban).then(function() {
                btn.innerHTML = '<i class="bi bi-check"></i>';
                setTimeout(function() {
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);
            });
        });
    });
});
</script>
