<?php
/**
 * Schuladmin Dashboard - Einstiegsseite
 * Zeigt Übersicht, Passwort-Info und Quick-Links
 *
 * @var \App\View\AppView $this
 * @var string $defaultPassword
 * @var int $userCount
 * @var int $accountCount
 * @var float $totalBalance
 * @var int $transactionsToday
 */

$school = $loggedinschool;
?>

<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-1">
            <i class="bi bi-house-door me-2"></i>Willkommen, <?= h($school['name']) ?>
        </h4>
        <p class="text-muted mb-0"><?= strftime('%A, %d. %B %Y') ?></p>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="text-primary mb-2"><i class="bi bi-building fs-2"></i></div>
                <h3 class="mb-1"><?= $userCount ?></h3>
                <small class="text-muted">Übungsfirmen</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="text-success mb-2"><i class="bi bi-wallet2 fs-2"></i></div>
                <h3 class="mb-1"><?= $accountCount ?></h3>
                <small class="text-muted">Konten</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="text-info mb-2"><i class="bi bi-currency-euro fs-2"></i></div>
                <h3 class="mb-1"><?= $this->Number->currency($totalBalance, 'EUR') ?></h3>
                <small class="text-muted">Gesamtguthaben</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card text-center h-100">
            <div class="card-body">
                <div class="text-warning mb-2"><i class="bi bi-arrow-left-right fs-2"></i></div>
                <h3 class="mb-1"><?= $transactionsToday ?></h3>
                <small class="text-muted">Transaktionen heute</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <a href="/users" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-building text-primary fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-dark">Übungsfirmen verwalten</h5>
                    <small class="text-muted">Firmen anlegen, bearbeiten, Konten einsehen</small>
                </div>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="/transactions" class="card text-decoration-none h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-activity text-success fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1 text-dark">Live-Transaktionen</h5>
                    <small class="text-muted">Überweisungen in Echtzeit verfolgen</small>
                </div>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
</div>

<!-- Info-Box: Kennwort -->
<div class="card border-primary">
    <div class="card-header bg-primary text-white">
        <i class="bi bi-key-fill me-2"></i>Wichtige Information
    </div>
    <div class="card-body">
        <p class="mb-2">
            <strong>Kennwort für alle Übungsfirmen:</strong>
        </p>
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control bg-light font-monospace" value="<?= h($defaultPassword) ?>" readonly>
            <button type="button" class="btn btn-outline-secondary" id="copyPassword" title="Kopieren">
                <i class="bi bi-clipboard"></i>
            </button>
        </div>
        <small class="text-muted d-block mt-2">
            Alle Übungsfirmen melden sich mit ihrem Benutzernamen und diesem Kennwort an.
        </small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var copyBtn = document.getElementById('copyPassword');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            var password = '<?= h($defaultPassword) ?>';
            navigator.clipboard.writeText(password).then(function() {
                copyBtn.innerHTML = '<i class="bi bi-check"></i>';
                setTimeout(function() {
                    copyBtn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);
            });
        });
    }
});
</script>
