<?php
/**
 * Übungsfirmen-Übersicht für Schuladmins
 * Kompakte Card-Liste + Aktivitäts-Feed
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 * @var array $recentTransactions
 */
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-building me-2"></i>Übungsfirmen</h4>
    <a href="/users/add" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Neue Übungsfirma
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

<?php if ($isSchoolAdmin): ?>
<!-- Passwort-Info -->
<div class="alert alert-info d-flex align-items-center mb-4 py-2">
    <i class="bi bi-key-fill me-2"></i>
    <span>Passwort für Schüler: <code class="user-select-all"><?= h($defaultPassword) ?></code></span>
    <button type="button" class="btn btn-sm btn-link ms-2 p-0" onclick="navigator.clipboard.writeText('<?= h($defaultPassword) ?>'); this.innerHTML='<i class=\'bi bi-check\'></i>';">
        <i class="bi bi-clipboard"></i>
    </button>
</div>
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

<!-- Übungsfirmen Cards -->
<div class="row g-3 mb-4">
    <?php foreach ($users as $user): ?>
    <?php
        # Konto-Infos
        $hasAccounts = !empty($user->accounts);
        $firstAccount = $hasAccounts ? $user->accounts[0] : null;
        $totalBalance = 0;
        if ($hasAccounts) {
            foreach ($user->accounts as $acc) {
                $totalBalance += $acc->balance;
            }
        }

        # Letzte Aktivität formatieren
        $lastActivity = null;
        if ($user->last_login) {
            $diff = time() - $user->last_login->getTimestamp();
            if ($diff < 60) {
                $lastActivity = 'gerade eben';
            } elseif ($diff < 3600) {
                $lastActivity = 'vor ' . round($diff / 60) . ' Min';
            } elseif ($diff < 86400) {
                $lastActivity = 'vor ' . round($diff / 3600) . ' Std';
            } else {
                $lastActivity = $user->last_login->format('d.m.Y H:i');
            }
        }
    ?>
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <!-- Header: Name + Status -->
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-0"><?= h($user->name) ?></h6>
                        <small class="text-muted">Login: <?= h($user->username) ?></small>
                    </div>
                    <?php if ($user->active): ?>
                        <span class="badge bg-success">Aktiv</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inaktiv</span>
                    <?php endif; ?>
                </div>

                <?php if ($hasAccounts && $firstAccount): ?>
                <!-- IBAN + Kontostand -->
                <div class="bg-light rounded p-2 mb-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="font-monospace small text-truncate" style="max-width: 70%;">
                            <?= h($firstAccount->iban) ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-link p-0 copy-iban" data-iban="<?= h($firstAccount->iban) ?>" title="IBAN kopieren">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <div class="mt-1">
                        <span class="fw-bold <?= $totalBalance >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $this->Number->currency($totalBalance, 'EUR') ?>
                        </span>
                        <?php if (count($user->accounts) > 1): ?>
                        <small class="text-muted">(<?= count($user->accounts) ?> Konten)</small>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Letzte Aktivität -->
                <?php if ($lastActivity): ?>
                <small class="text-muted d-block mb-2">
                    <i class="bi bi-clock me-1"></i>Zuletzt aktiv: <?= $lastActivity ?>
                </small>
                <?php endif; ?>

                <!-- Aktionen -->
                <div class="d-flex gap-2">
                    <?php if ($isSchoolAdmin): ?>
                    <?= $this->Html->link(
                        '<i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als',
                        ['action' => 'impersonate', $user->id],
                        ['class' => 'btn btn-sm btn-outline-success flex-grow-1', 'escape' => false]
                    ) ?>
                    <?php endif; ?>
                    <?= $this->Html->link(
                        '<i class="bi bi-arrow-right"></i>',
                        ['action' => 'view', $user->id],
                        ['class' => 'btn btn-sm btn-outline-primary', 'escape' => false, 'title' => 'Details']
                    ) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination -->
<?php if ($this->Paginator->total() > 1): ?>
<div class="d-flex justify-content-between align-items-center mb-4">
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

<?php if ($isSchoolAdmin && !empty($recentTransactions)): ?>
<!-- Aktivitäts-Feed -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0"><i class="bi bi-activity me-2"></i>Letzte Aktivitäten</h6>
        <a href="#" onclick="location.reload(); return false;" class="btn btn-sm btn-link p-0">
            <i class="bi bi-arrow-clockwise"></i>
        </a>
    </div>
    <div class="list-group list-group-flush">
        <?php foreach ($recentTransactions as $tx): ?>
        <div class="list-group-item py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= h($tx->account->user->name ?? 'Unbekannt') ?></strong>
                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                    <span><?= h($tx->empfaenger_name) ?></span>
                </div>
                <div class="text-end">
                    <span class="text-danger fw-bold">
                        -<?= $this->Number->currency($tx->betrag, 'EUR') ?>
                    </span>
                    <br>
                    <small class="text-muted"><?= $tx->created->format('H:i') ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // IBAN kopieren
    document.querySelectorAll('.copy-iban').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
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
