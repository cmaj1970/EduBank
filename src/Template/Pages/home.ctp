<?php
/**
 * EduBank - Banking Simulation für Schulen
 * Startseite - Landing Page oder Dashboard
 */
?>

<?php if ($authuser): ?>
<!-- Dashboard für eingeloggte Benutzer -->
<div class="row mb-4">
    <div class="col-12">
        <h3><i class="bi bi-speedometer2 me-2"></i>Dashboard</h3>
        <?php if (isset($loggedinschool)): ?>
        <p class="text-muted">Willkommen zurück! Schuladministrator: <?= h($loggedinschool['name']) ?></p>
        <?php elseif ($authuser['role'] == 'admin'): ?>
        <p class="text-muted">Willkommen zurück, <?= h($authuser['name']) ?>! (Superadministrator)</p>
        <?php else: ?>
        <p class="text-muted">Willkommen zurück, <?= h($authuser['name']) ?>!</p>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <?php if ($authuser['role'] == 'admin'): ?>
    <!-- Admin Dashboard -->

    <?php if (!isset($loggedinschool)): ?>
    <!-- Nur Superadmin: Schulen -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-building text-primary fs-1 mb-3"></i>
                <h5 class="card-title">Schulen</h5>
                <p class="card-text text-muted small">Schulen verwalten</p>
                <a href="/schools" class="btn btn-outline-primary">Öffnen</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-people text-info fs-1 mb-3"></i>
                <h5 class="card-title">Übungsfirmen</h5>
                <p class="card-text text-muted small">Übungsfirmen verwalten</p>
                <a href="/users" class="btn btn-outline-info">Öffnen</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-wallet2 text-success fs-1 mb-3"></i>
                <h5 class="card-title">Konten</h5>
                <p class="card-text text-muted small">Konten verwalten</p>
                <a href="/accounts" class="btn btn-outline-success">Öffnen</a>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-arrow-left-right text-warning fs-1 mb-3"></i>
                <h5 class="card-title">Transaktionen</h5>
                <p class="card-text text-muted small">Überweisungen verwalten</p>
                <a href="/transactions" class="btn btn-outline-warning">Öffnen</a>
            </div>
        </div>
    </div>

    <?php else: ?>
    <!-- Übungsfirma Dashboard -->
    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-wallet2 text-success fs-1 mb-3"></i>
                <h5 class="card-title">Mein Konto</h5>
                <p class="card-text text-muted">Kontostand und Transaktionen anzeigen</p>
                <a href="/accounts" class="btn btn-success btn-lg">Zum Konto</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-send text-primary fs-1 mb-3"></i>
                <h5 class="card-title">Überweisung</h5>
                <p class="card-text text-muted">Geld an andere Konten überweisen</p>
                <a href="/transactions/add" class="btn btn-primary btn-lg">Neue Überweisung</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php else: ?>
<!-- Landing Page für nicht eingeloggte Benutzer -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <!-- Logo -->
            <div class="mb-4">
                <?= $this->Html->image('logo.svg', ['alt' => 'EduBank Logo', 'style' => 'height: 80px; filter: brightness(0);']) ?>
            </div>

            <h1 class="display-5 fw-bold text-primary mb-3">Willkommen bei EduBank</h1>
            <p class="lead text-muted mb-4">
                Die Banking-Simulation für Schulen. Lernen Sie den Umgang mit Finanzen in einer sicheren Umgebung.
            </p>

            <!-- Features -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-wallet2 text-primary fs-1 mb-3"></i>
                            <h5 class="card-title">Konten verwalten</h5>
                            <p class="card-text text-muted small">Erstellen und verwalten Sie virtuelle Bankkonten für Übungsfirmen.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-arrow-left-right text-success fs-1 mb-3"></i>
                            <h5 class="card-title">Überweisungen</h5>
                            <p class="card-text text-muted small">Führen Sie sichere Transaktionen zwischen Konten durch.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-shield-check text-info fs-1 mb-3"></i>
                            <h5 class="card-title">TAN-Verfahren</h5>
                            <p class="card-text text-muted small">Lernen Sie Sicherheitsverfahren wie im echten Online-Banking.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Button -->
            <a href="/users/login" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-box-arrow-in-right me-2"></i>Anmelden
            </a>
        </div>
    </div>
</div>
<?php endif; ?>
