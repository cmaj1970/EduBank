<?php
/**
 * EduBank - Banking Simulation für Schulen
 * Default Layout with Bootstrap 5 - Responsive Design
 */

// Für Übungsfirma: Erstes Konto des Users für Navigation holen
$userAccountId = null;
if ($authuser && $authuser['role'] == 'user') {
    $accountsTable = \Cake\ORM\TableRegistry::get('Accounts');
    $userAccount = $accountsTable->find()
        ->where(['user_id' => $authuser['id']])
        ->first();
    if ($userAccount) {
        $userAccountId = $userAccount->id;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBank - Banking Simulation</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <?= $this->Html->css('bootstrap-custom.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-edubank sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <?= $this->Html->image('logo.svg', ['alt' => 'EduBank Logo', 'class' => 'logo']) ?>
            </a>

            <?php if($authuser): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <!-- Hauptnavigation -->
                <ul class="navbar-nav me-auto">
                    <?php if($authuser['role'] == 'admin'): ?>
                        <?php if(!isset($loggedinschool)): ?>
                        <!-- Superadmin: Schulen anzeigen -->
                        <li class="nav-item">
                            <a class="nav-link" href="/schools">
                                <i class="bi bi-building me-1"></i>Schulen
                            </a>
                        </li>
                        <?php endif; ?>

                        <!-- Admins: Übungsfirmen verwalten -->
                        <li class="nav-item">
                            <a class="nav-link" href="/users">
                                <i class="bi bi-people me-1"></i>Übungsfirmen
                            </a>
                        </li>

                        <!-- Admins: Konten -->
                        <li class="nav-item">
                            <a class="nav-link" href="/accounts">
                                <i class="bi bi-wallet2 me-1"></i>Konten
                            </a>
                        </li>

                        <!-- Admins: Transaktionen -->
                        <li class="nav-item">
                            <a class="nav-link" href="/transactions">
                                <i class="bi bi-arrow-left-right me-1"></i>Transaktionen
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Übungsfirma: Konto-Navigation -->
                        <?php if ($userAccountId): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/accounts/view/<?= $userAccountId ?>">
                                <i class="bi bi-wallet2 me-1"></i>Mein Konto
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/accounts/history/<?= $userAccountId ?>">
                                <i class="bi bi-clock-history me-1"></i>Auftragshistorie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/transactions/add/<?= $userAccountId ?>">
                                <i class="bi bi-send me-1"></i>Neue Überweisung
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/accounts">
                                <i class="bi bi-wallet2 me-1"></i>Mein Konto
                            </a>
                        </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <!-- User Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= h($authuser['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="/users/logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Abmelden
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </nav>

    <?php
    // Check if impersonating
    $originalAdmin = $this->request->getSession()->read('Auth.OriginalAdmin');
    if ($originalAdmin && $authuser):
    ?>
    <!-- Impersonation Banner -->
    <div class="bg-warning text-dark py-2 d-print-none">
        <div class="container-fluid d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <i class="bi bi-eye-fill me-2"></i>
                <strong>Ansicht als:</strong> <?= h($authuser['name']) ?>
                <span class="text-muted ms-2">(<?= h($authuser['username']) ?>)</span>
            </div>
            <a href="/users/stop-impersonating" class="btn btn-sm btn-dark">
                <i class="bi bi-arrow-return-left me-1"></i>Zurück zur Admin-Ansicht
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php $flash = $this->Flash->render(); ?>
    <?php if($flash): ?>
    <div class="container-fluid py-2">
        <?= $flash ?>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <div class="container-fluid py-4">
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-edubank mt-auto d-print-none">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span>&copy; <?= date('Y') ?> EduBank - Banking Simulation für Schulen</span>
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <a href="/pages/impressum" class="text-white text-decoration-none me-3">Impressum</a>
                    <?php
                    $versionFile = ROOT . DS . 'Version.txt';
                    $version = file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'unknown';
                    ?>
                    <span class="text-white">v<?= $version ?></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery (für bestehende Funktionalität) -->
    <?php echo $this->Html->script('/js/jquery.min.js'); ?>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.0/dist/jquery.validate.min.js"></script>
    <?php echo $this->Html->script('/js/messages.de.js'); ?>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?= $this->fetch('script') ?>
</body>
</html>
