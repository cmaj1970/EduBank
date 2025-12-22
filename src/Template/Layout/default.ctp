<?php
/**
 * EduBank - Banking Simulation für Schulen
 * Default Layout with Bootstrap 5 - Responsive Design
 */

// Für Übungsfirma: Erstes Konto des Users für Navigation holen
$userAccountId = null;
$userSchool = null;
if ($authuser && $authuser['role'] == 'user') {
    $accountsTable = \Cake\ORM\TableRegistry::get('Accounts');
    $userAccount = $accountsTable->find()
        ->where(['user_id' => $authuser['id']])
        ->first();
    if ($userAccount) {
        $userAccountId = $userAccount->id;
    }

    // Schule für Übungsfirma laden
    if (!empty($authuser['school_id'])) {
        $schoolsTable = \Cake\ORM\TableRegistry::get('Schools');
        $userSchool = $schoolsTable->get($authuser['school_id']);
    }
}

// Aktive Schule ermitteln (für Schuladmin oder Übungsfirma)
$activeSchool = isset($loggedinschool) ? $loggedinschool : $userSchool;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBank - Banking Simulation</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">

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
                <?php if ($activeSchool): ?>
                <!-- Dynamisches Logo mit Schulname -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50 60" width="50" height="60" class="me-2">
                    <g fill="#ffffff">
                        <!-- Dach/Giebel -->
                        <path d="M 5 18 L 25 8 L 45 18 L 43 18 L 25 10 L 7 18 Z" />
                        <!-- Säulen -->
                        <rect x="9" y="19" width="5" height="20" />
                        <rect x="18" y="19" width="5" height="20" />
                        <rect x="27" y="19" width="5" height="20" />
                        <rect x="36" y="19" width="5" height="20" />
                        <!-- Basis -->
                        <rect x="6" y="39" width="38" height="3" />
                        <!-- Buch -->
                        <path d="M 25 44 L 17 46 L 17 52 L 25 50 L 33 52 L 33 46 Z" fill="none" stroke="#ffffff" stroke-width="1.5" />
                        <path d="M 25 44 L 25 50" stroke="#ffffff" stroke-width="1" />
                    </g>
                </svg>
                <div class="d-flex flex-column lh-sm">
                    <span class="fw-bold" style="font-size: 1.25rem;">EduBank</span>
                    <span class="text-white-50" style="font-size: 0.75rem;"><?= h($activeSchool->name) ?></span>
                </div>
                <?php else: ?>
                <?= $this->Html->image('logo.svg', ['alt' => 'EduBank Logo', 'class' => 'logo']) ?>
                <?php endif; ?>
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
                        <li class="nav-item">
                            <a class="nav-link" href="/schools/demo-school">
                                <i class="bi bi-database me-1"></i>Demodaten
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/schools/email-test">
                                <i class="bi bi-envelope-check me-1"></i>E-Mail
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
                            <a class="nav-link" href="/pages/faq">
                                <i class="bi bi-question-circle me-1"></i>Hilfe
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

    <!-- Hilfe-Button (Floating Action Button) -->
    <?php if($authuser && $authuser['role'] != 'admin'): ?>
    <button type="button" class="btn btn-primary rounded-circle shadow help-fab d-print-none"
            id="helpButton" title="Hilfe anzeigen"
            style="position: fixed; bottom: 20px; right: 20px; width: 56px; height: 56px; z-index: 1050;">
        <i class="bi bi-question-lg fs-4"></i>
    </button>
    <?php endif; ?>

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

    <!-- Hilfe-Modus Script -->
    <?php if($authuser && $authuser['role'] != 'admin'): ?>
    <script>
    (function() {
        var helpMode = false;
        var helpButton = document.getElementById('helpButton');
        var activePopover = null;

        if (!helpButton) return;

        // Hilfe-Button Klick
        helpButton.addEventListener('click', function() {
            helpMode = !helpMode;

            if (helpMode) {
                // Hilfe-Modus aktivieren
                helpButton.classList.remove('btn-primary');
                helpButton.classList.add('btn-warning');
                helpButton.innerHTML = '<i class="bi bi-x-lg fs-4"></i>';
                helpButton.title = 'Hilfe schließen';
                document.body.classList.add('help-mode-active');

                // Alle Elemente mit data-help hervorheben
                document.querySelectorAll('[data-help]').forEach(function(el) {
                    el.classList.add('help-highlight');
                });

                // Eingabefelder deaktivieren
                document.querySelectorAll('input, textarea, select, button[type="submit"], button[type="button"]:not(#helpButton)').forEach(function(el) {
                    if (!el.disabled) {
                        el.dataset.helpDisabled = 'true';
                        el.disabled = true;
                    }
                });

            } else {
                closeHelpMode();
            }
        });

        // Hilfe-Modus schließen
        function closeHelpMode() {
            helpMode = false;
            helpButton.classList.remove('btn-warning');
            helpButton.classList.add('btn-primary');
            helpButton.innerHTML = '<i class="bi bi-question-lg fs-4"></i>';
            helpButton.title = 'Hilfe anzeigen';
            document.body.classList.remove('help-mode-active');

            // Aktives Popover schließen
            if (activePopover) {
                activePopover.dispose();
                activePopover = null;
            }

            // Highlights entfernen
            document.querySelectorAll('.help-highlight').forEach(function(el) {
                el.classList.remove('help-highlight');
            });

            // Eingabefelder wieder aktivieren
            document.querySelectorAll('[data-help-disabled="true"]').forEach(function(el) {
                el.disabled = false;
                delete el.dataset.helpDisabled;
            });
        }

        // Klick auf Element mit data-help im Hilfe-Modus
        document.addEventListener('click', function(e) {
            if (!helpMode) return;

            var target = e.target.closest('[data-help]');

            // Vorheriges Popover schließen
            if (activePopover) {
                activePopover.dispose();
                activePopover = null;
            }

            if (target) {
                e.preventDefault();
                e.stopPropagation();

                // Neues Popover öffnen
                activePopover = new bootstrap.Popover(target, {
                    content: target.getAttribute('data-help'),
                    trigger: 'manual',
                    placement: 'auto',
                    html: false,
                    customClass: 'help-popover'
                });
                activePopover.show();
            }
        }, true);

        // ESC-Taste schließt Hilfe-Modus
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && helpMode) {
                closeHelpMode();
            }
        });
    })();
    </script>
    <style>
    .help-mode-active [data-help] {
        cursor: help;
    }
    .help-highlight {
        outline: 3px solid #ffc107 !important;
        outline-offset: 2px;
        animation: help-pulse 1.5s ease-in-out infinite;
    }
    @keyframes help-pulse {
        0%, 100% { outline-color: #ffc107; }
        50% { outline-color: #ff9800; }
    }
    .help-popover {
        max-width: 300px;
        z-index: 1060;
        --bs-popover-bg: #fff3cd;
        --bs-popover-border-color: #ffc107;
        --bs-popover-arrow-border: #ffc107;
        border-width: 2px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .help-popover .popover-body {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #664d03;
        padding: 0.75rem 1rem;
    }
    .help-popover .popover-body::before {
        content: "Hilfe: ";
        font-weight: bold;
        display: block;
        margin-bottom: 0.25rem;
        color: #997404;
    }
    .help-fab:hover {
        transform: scale(1.1);
        transition: transform 0.2s;
    }
    </style>
    <?php endif; ?>
</body>
</html>
