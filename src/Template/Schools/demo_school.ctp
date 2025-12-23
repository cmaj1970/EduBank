<?php
/**
 * Demo School Management Page
 * Superadmin only - Create, regenerate or delete demo data
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0"><i class="bi bi-database me-2"></i><?= __('Demoschulen verwalten') ?></h3>
            <a href="/schools" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i><?= __('Zurück') ?>
            </a>
        </div>

        <!-- Info Box -->
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Demodaten</strong> erstellen 3 Schulen (PTS Demo, HAK Demo, HTL Demo) mit je 5 Übungsfirmen,
            Bankkonten und ca. 25 Transaktionen über die letzten 2 Monate – auch schulübergreifend.
        </div>

        <?php if (!empty($demoStats)): ?>
        <!-- Current Demo Data -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>Demoschulen vorhanden</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-md-3">
                        <div class="display-6 text-primary"><?= count($demoStats['schools']) ?></div>
                        <small class="text-muted">Schulen</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-info"><?= $demoStats['totalUsers'] ?></div>
                        <small class="text-muted">Übungsfirmen</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-success"><?= $demoStats['totalAccounts'] ?></div>
                        <small class="text-muted">Konten</small>
                    </div>
                    <div class="col-md-3">
                        <div class="display-6 text-warning"><?= $demoStats['totalTransactions'] ?></div>
                        <small class="text-muted">Transaktionen</small>
                    </div>
                </div>

                <h6 class="mb-3">Erstellte Schulen:</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Schule</th>
                                <th>Kurzname</th>
                                <th>BIC</th>
                                <th>IBAN-Prefix</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demoStats['schools'] as $school): ?>
                            <tr>
                                <td><strong><?= h($school->name) ?></strong></td>
                                <td><code><?= h($school->kurzname) ?></code></td>
                                <td><code><?= h($school->bic) ?></code></td>
                                <td><code><?= h($school->ibanprefix) ?></code></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="d-flex gap-2 flex-wrap">
                    <?= $this->Form->postLink(
                        '<i class="bi bi-arrow-clockwise me-1"></i> Neu generieren',
                        ['action' => 'regenerateDemoSchool'],
                        [
                            'class' => 'btn btn-warning',
                            'escape' => false,
                            'confirm' => 'Alle Demodaten löschen und neu erstellen?'
                        ]
                    ) ?>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-trash me-1"></i> Löschen',
                        ['action' => 'deleteDemoSchool'],
                        [
                            'class' => 'btn btn-danger',
                            'escape' => false,
                            'confirm' => 'Alle Demoschulen und deren Daten unwiderruflich löschen?'
                        ]
                    ) ?>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- No Demo Data -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center py-5">
                <div class="rounded-circle bg-secondary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-database text-secondary" style="font-size: 3rem;"></i>
                </div>
                <h4 class="mb-3">Keine Demoschulen vorhanden</h4>
                <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                    Erstellen Sie Testdaten mit 3 Schulen, 15 Übungsfirmen und realistischen Transaktionen,
                    um die Anwendung zu demonstrieren.
                </p>

                <?= $this->Form->postLink(
                    '<i class="bi bi-plus-lg me-2"></i> Demoschulen erstellen',
                    ['action' => 'createDemoSchool'],
                    [
                        'class' => 'btn btn-primary btn-lg',
                        'escape' => false
                    ]
                ) ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- System-Konten -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header <?= $systemStats ? 'bg-primary text-white' : '' ?>">
                <h5 class="mb-0"><i class="bi bi-shop me-2"></i>System-Konten (Geschäftspartner)</h5>
            </div>
            <div class="card-body">
                <?php if ($systemStats): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="badge bg-primary fs-6"><?= $systemStats['count'] ?></span>
                        <span class="ms-2">Geschäftspartner verfügbar</span>
                    </div>
                    <?= $this->Form->postLink(
                        '<i class="bi bi-trash me-1"></i> Löschen',
                        ['action' => 'deleteSystemAccounts'],
                        [
                            'class' => 'btn btn-sm btn-outline-danger',
                            'escape' => false,
                            'confirm' => 'System-Konten löschen? Transaktionen zu diesen Konten werden ebenfalls gelöscht.'
                        ]
                    ) ?>
                </div>
                <p class="text-muted small mb-0">
                    Fiktive Geschäftspartner wie Bürobedarf, Druckerei, IT-Service etc.
                    Alle Übungsfirmen können an diese Konten überweisen.
                </p>
                <?php else: ?>
                <p class="text-muted mb-3">
                    <strong>System-Konten</strong> sind 10 fiktive Geschäftspartner (Bürobedarf, Druckerei, Catering etc.),
                    die für alle Schulen als Überweisungsempfänger verfügbar sind.
                    Ideal für Schulen mit wenigen Übungsfirmen.
                </p>
                <?= $this->Form->postLink(
                    '<i class="bi bi-plus-lg me-2"></i> System-Konten erstellen',
                    ['action' => 'createSystemAccounts'],
                    [
                        'class' => 'btn btn-primary',
                        'escape' => false
                    ]
                ) ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Demo Login Credentials -->
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-key me-2"></i>Demo-Zugangsdaten</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Schuladmins:</h6>
                        <ul class="list-unstyled small">
                            <li><code>admin-ptsdemo</code></li>
                            <li><code>admin-hakdemo</code></li>
                            <li><code>admin-htldemo</code></li>
                        </ul>
                        <p class="text-muted small">Passwort: <code><?= h(env('DEFAULT_ADMIN_PASSWORD', 'SchulAdmin2024')) ?></code></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Übungsfirmen:</h6>
                        <p class="text-muted small">
                            Benutzernamen: <code>ptsdemo-sonnenschein</code>, <code>hakdemo-goldener</code>, etc.
                        </p>
                        <p class="text-muted small">Passwort: <code><?= h(env('DEFAULT_USER_PASSWORD', 'Schueler2024')) ?></code></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
