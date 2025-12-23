<?php
/**
 * Mockup: Schuladmin Übungsfirma-Detailansicht
 * Statisches HTML zur Vorschau des neuen Designs
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">

        <!-- Zurück-Link -->
        <div class="mb-4">
            <a href="mockup_admin_list" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Zurück zur Liste
            </a>
        </div>

        <!-- Block 1: Firmendaten -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Firmendaten</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="row flex-grow-1">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="text-muted small">Zugehörige Schule</div>
                            <div class="fw-semibold">PTS Musterstadt</div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="text-muted small">Firmenname</div>
                            <div class="fw-semibold">Handelsfirma Sonnenschein</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Login-Name</div>
                            <div><code class="fs-6">pts1-001</code></div>
                        </div>
                    </div>
                    <div class="ms-3 d-flex gap-2 flex-shrink-0">
                        <a href="#" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i>Bearbeiten
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-sm" onclick="return confirm('Wirklich löschen?')">
                            <i class="bi bi-trash me-1"></i>Löschen
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Block 2: Konten -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Konten dieser Übungsfirma</h5>
                <a href="#" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Konto hinzufügen
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Kontoname</th>
                                <th style="width: 30%">IBAN</th>
                                <th style="width: 20%" class="text-end">Überweisungslimit</th>
                                <th style="width: 20%" class="text-end">Kontostand</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Konto 1 -->
                            <tr>
                                <td>
                                    <i class="bi bi-credit-card text-primary me-2"></i>
                                    <strong>Hauptkonto</strong>
                                </td>
                                <td>
                                    <code class="font-monospace">AT12 3456 7890 1234 5678</code>
                                    <a href="#" class="text-muted ms-1" title="IBAN kopieren">
                                        <i class="bi bi-clipboard"></i>
                                    </a>
                                </td>
                                <td class="text-end">
                                    5.000,00 €
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">12.450,00 €</span>
                                </td>
                            </tr>

                            <!-- Konto 2 -->
                            <tr>
                                <td>
                                    <i class="bi bi-credit-card text-primary me-2"></i>
                                    <strong>Sparkonto</strong>
                                </td>
                                <td>
                                    <code class="font-monospace">AT98 7654 3210 9876 5432</code>
                                    <a href="#" class="text-muted ms-1" title="IBAN kopieren">
                                        <i class="bi bi-clipboard"></i>
                                    </a>
                                </td>
                                <td class="text-end">
                                    1.000,00 €
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-danger">-250,00 €</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        2 Konten insgesamt
                    </span>
                    <span class="fw-semibold">
                        Gesamtguthaben:
                        <span class="text-success">12.200,00 €</span>
                    </span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Copy IBAN Feature (Demo)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.bi-clipboard').forEach(function(icon) {
        icon.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            var iban = this.previousElementSibling.textContent.trim();
            navigator.clipboard.writeText(iban).then(function() {
                icon.className = 'bi bi-check text-success';
                setTimeout(function() {
                    icon.className = 'bi bi-clipboard';
                }, 1500);
            });
        });
    });
});
</script>
