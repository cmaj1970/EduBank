<?php
/**
 * EduBank - Banking Simulation für Schulen
 * Error Layout - Styled like the main application
 */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBank - <?= $this->fetch('title') ?: 'Fehler' ?></title>

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

    <!-- Navbar (simplified for error pages) -->
    <nav class="navbar navbar-dark navbar-edubank">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <?= $this->Html->image('logo.svg', ['alt' => 'EduBank Logo', 'class' => 'logo']) ?>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow-1 d-flex align-items-center">
        <div class="container py-5">
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer-edubank mt-auto">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <span>&copy; <?= date('Y') ?> EduBank - Banking Simulation für Schulen</span>
                </div>
                <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                    <?php
                    $versionFile = ROOT . DS . 'Version.txt';
                    $version = file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'unknown';
                    ?>
                    <span class="text-white">v<?= $version ?></span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?= $this->fetch('script') ?>
</body>
</html>
