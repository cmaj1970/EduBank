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
<!-- ========================================== -->
<!-- LANDING PAGE - Bank-Style für Besucher    -->
<!-- ========================================== -->

<!-- Hero Section -->
<div class="bg-gradient-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, #1a365d 0%, #2d4a7c 100%); margin: -1.5rem -0.75rem 2rem -0.75rem; padding-left: 1.5rem; padding-right: 1.5rem;">
    <div class="container">
        <div class="row align-items-center py-4">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold mb-3">
                    Banking lernen.<br>Sicher und einfach.
                </h1>
                <p class="lead mb-4 opacity-90">
                    EduBank ist die kostenlose Banking-Simulation für Schulen.
                    Schüler lernen den Umgang mit Finanzen in einer realistischen, aber sicheren Umgebung.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="/users/login" class="btn btn-light btn-lg px-4">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Anmelden
                    </a>
                    <a href="/schools/register" class="btn btn-outline-light btn-lg px-4">
                        <i class="bi bi-plus-circle me-2"></i>Schule registrieren
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center d-none d-lg-block">
                <i class="bi bi-bank2" style="font-size: 12rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Features -->
<div class="container mb-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Warum EduBank?</h2>
        <p class="text-muted">Die ideale Plattform für praxisnahes Finanzwissen</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-wallet2 text-primary fs-2"></i>
                    </div>
                    <h5 class="card-title">Echte Konten</h5>
                    <p class="card-text text-muted small">Jede Übungsfirma erhält ein vollwertiges Konto mit IBAN und BIC.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-arrow-left-right text-success fs-2"></i>
                    </div>
                    <h5 class="card-title">Überweisungen</h5>
                    <p class="card-text text-muted small">Realistische Transaktionen zwischen allen Konten im System.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-shield-check text-info fs-2"></i>
                    </div>
                    <h5 class="card-title">TAN-Verfahren</h5>
                    <p class="card-text text-muted small">Sicherheit wie beim echten Banking mit TAN-Bestätigung.</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center p-4">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-people text-warning fs-2"></i>
                    </div>
                    <h5 class="card-title">Mehrere Schulen</h5>
                    <p class="card-text text-muted small">Schulübergreifende Transaktionen für realistische Szenarien.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- How it works -->
<div class="bg-light py-5 mb-5" style="margin-left: -0.75rem; margin-right: -0.75rem; padding-left: 1.5rem; padding-right: 1.5rem;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">So funktioniert's</h2>
            <p class="text-muted">In drei Schritten zum digitalen Klassenzimmer-Banking</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 fs-3 fw-bold" style="width: 60px; height: 60px;">1</div>
                    <h5>Schule registrieren</h5>
                    <p class="text-muted">Lehrkraft registriert die Schule und erhält Admin-Zugang per E-Mail.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 fs-3 fw-bold" style="width: 60px; height: 60px;">2</div>
                    <h5>Übungsfirmen anlegen</h5>
                    <p class="text-muted">Admin erstellt Übungsfirmen - jede bekommt automatisch ein Konto.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 fs-3 fw-bold" style="width: 60px; height: 60px;">3</div>
                    <h5>Banking starten</h5>
                    <p class="text-muted">Schüler melden sich an und führen Überweisungen durch.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Info Section -->
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-6 mb-4">
            <div class="card border-0 shadow-sm bg-primary text-white h-100">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3"><i class="bi bi-info-circle me-2"></i>Über EduBank</h5>
                    <p class="card-text opacity-90 mb-2">
                        EduBank ist eine kostenlose Banking-Simulation für den Schulunterricht – entwickelt aus Überzeugung,
                        nicht aus Profit. Dieses Projekt entsteht ehrenamtlich in meiner Freizeit, um Schülerinnen und
                        Schülern praktische Erfahrungen mit digitalem Zahlungsverkehr zu ermöglichen.
                    </p>
                    <p class="card-text opacity-90 small mb-3">
                        <i class="bi bi-heart-fill me-1"></i> 100% kostenlos · Open Source · Made with ❤️ for Education
                    </p>
                    <hr class="opacity-25">
                    <div class="d-flex gap-3">
                        <a href="https://github.com/cmaj1970/EduBank" target="_blank" class="text-white text-decoration-none small">
                            <i class="bi bi-github me-1"></i> GitHub
                        </a>
                        <a href="https://www.paypal.com/paypalme/carlmajneri" target="_blank" class="text-white text-decoration-none small">
                            <i class="bi bi-piggy-bank me-1"></i> Unterstützen
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-building text-primary fs-1 mb-3"></i>
                    <h5>Für Schulen</h5>
                    <p class="text-muted mb-4">Registrieren Sie Ihre Schule kostenlos und starten Sie mit dem digitalen Banking im Unterricht.</p>
                    <a href="/schools/register" class="btn btn-primary btn-lg">
                        <i class="bi bi-rocket-takeoff me-2"></i>Jetzt starten
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="text-center py-5 mb-3" style="background: linear-gradient(135deg, #1a365d 0%, #2d4a7c 100%); margin-left: -0.75rem; margin-right: -0.75rem; padding-left: 1.5rem; padding-right: 1.5rem;">
    <div class="container">
        <h2 class="text-white fw-bold mb-3">Bereit für digitales Banking im Unterricht?</h2>
        <p class="text-white opacity-75 mb-4">Starten Sie noch heute mit EduBank - kostenlos und ohne Installation.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="/schools/register" class="btn btn-light btn-lg px-4">
                <i class="bi bi-rocket-takeoff me-2"></i>Schule registrieren
            </a>
            <a href="/users/login" class="btn btn-outline-light btn-lg px-4">
                Anmelden
            </a>
        </div>
    </div>
</div>

<?php endif; ?>
