<?php
/**
 * Mockup: Schuladmin Übungsfirmen-Liste
 * Statisches HTML zur Vorschau des neuen Designs
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">

        <!-- Header mit Titel und Erstellen-Button -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h3 class="mb-2"><i class="bi bi-building me-2"></i>Übungsfirmen</h3>
                <p class="text-muted mb-0">
                    Verwalten Sie die Übungsfirmen Ihrer Schule
                </p>
            </div>
            <a href="#" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Neue Übungsfirma
            </a>
        </div>

        <!-- Info für Schuladmin -->
        <div class="alert alert-light border mb-4">
            <i class="bi bi-info-circle me-2 text-primary"></i>
            <strong>Tipp:</strong> Mit "Anmelden als" können Sie sich als Übungsfirma einloggen, um Transaktionen durchzuführen oder den Kontostand zu prüfen.
        </div>

        <!-- Tabelle -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 35%">Firmenname</th>
                                <th style="width: 25%">Login-Name</th>
                                <th style="width: 15%" class="text-center">Konten</th>
                                <th style="width: 25%" class="text-end">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Beispiel 1 -->
                            <tr>
                                <td>
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <strong>Handelsfirma Sonnenschein</strong>
                                </td>
                                <td>
                                    <code>pts1-001</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">2</span>
                                </td>
                                <td class="text-end">
                                    <a href="mockup_admin_detail" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-eye me-1"></i>Details
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als
                                    </a>
                                </td>
                            </tr>

                            <!-- Beispiel 2 -->
                            <tr>
                                <td>
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <strong>Werbeagentur Kreativ</strong>
                                </td>
                                <td>
                                    <code>pts1-002</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">1</span>
                                </td>
                                <td class="text-end">
                                    <a href="mockup_admin_detail" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-eye me-1"></i>Details
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als
                                    </a>
                                </td>
                            </tr>

                            <!-- Beispiel 3 -->
                            <tr>
                                <td>
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <strong>IT-Solutions Meyer</strong>
                                </td>
                                <td>
                                    <code>pts1-003</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">3</span>
                                </td>
                                <td class="text-end">
                                    <a href="mockup_admin_detail" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-eye me-1"></i>Details
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als
                                    </a>
                                </td>
                            </tr>

                            <!-- Beispiel 4 -->
                            <tr>
                                <td>
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <strong>Logistik Express GmbH</strong>
                                </td>
                                <td>
                                    <code>pts1-004</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">1</span>
                                </td>
                                <td class="text-end">
                                    <a href="mockup_admin_detail" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-eye me-1"></i>Details
                                    </a>
                                    <a href="#" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Anmelden als
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav class="mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Zeige 1–4 von 12 Übungsfirmen</small>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item disabled">
                        <a class="page-link" href="#">&laquo;</a>
                    </li>
                    <li class="page-item active">
                        <a class="page-link" href="#">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link" href="#">&raquo;</a>
                    </li>
                </ul>
            </div>
        </nav>

    </div>
</div>
