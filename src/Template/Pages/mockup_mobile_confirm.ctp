<?php
/**
 * Mockup: Mobile Bestätigung einer Überweisung
 * Stilisiertes Handy-Display im Modal statt TAN-Eingabe
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

        <!-- Header -->
        <div class="mb-4">
            <h3 class="mb-2"><i class="bi bi-send me-2"></i>Neue Überweisung</h3>
            <p class="text-muted mb-0">Überweisung erfassen und per Smartphone bestätigen</p>
        </div>

        <!-- Überweisungsformular (Demo) -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Überweisungsdaten</h5>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Empfänger</label>
                        <input type="text" class="form-control" value="Werbeagentur Kreativ" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label">IBAN</label>
                        <input type="text" class="form-control font-monospace" value="AT12 3456 7890 1234 5678" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Betrag</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-end" value="1.250,00" readonly>
                            <span class="input-group-text">EUR</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Datum</label>
                        <input type="text" class="form-control" value="30.12.2025" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Verwendungszweck</label>
                        <input type="text" class="form-control" value="Rechnung Nr. 2025-0142" readonly>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <a href="#" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Zurück
                    </a>
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#mobileConfirmModal">
                        <i class="bi bi-phone me-2"></i>Am Smartphone bestätigen
                    </button>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <strong>So funktioniert's:</strong> Nach dem Klick auf "Am Smartphone bestätigen" erscheint eine Vorschau
            der Überweisung. Bestätigen Sie diese mit einem Klick – ganz ohne TAN-Eingabe.
        </div>

    </div>
</div>

<!-- ============================================== -->
<!-- MOBILE CONFIRMATION MODAL                      -->
<!-- ============================================== -->
<div class="modal fade" id="mobileConfirmModal" tabindex="-1" aria-labelledby="mobileConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="pointer-events: none;">
        <div class="modal-content border-0" style="background: transparent;">

            <!-- Smartphone Frame (iPhone 13 Pro: 390x844 CSS pixels, ratio 19.5:9) -->
            <div class="mx-auto" style="width: 300px; pointer-events: auto; position: relative; z-index: 1060;">

                <!-- Phone Outer Frame -->
                <div style="background: linear-gradient(145deg, #2d2d2d, #1a1a1a); border-radius: 40px; padding: 12px; box-shadow: 0 25px 50px rgba(0,0,0,0.3);">

                    <!-- Phone Inner Frame -->
                    <div style="background: #000; border-radius: 32px; padding: 8px; position: relative;">

                        <!-- Notch (Dynamic Island style) -->
                        <div style="position: absolute; top: 12px; left: 50%; transform: translateX(-50%); width: 90px; height: 24px; background: #000; border-radius: 12px; z-index: 10;"></div>

                        <!-- Screen (iPhone 13 Pro aspect ratio: 390/844 ≈ 0.462) -->
                        <div id="phoneScreen" style="background: linear-gradient(180deg, #1a365d 0%, #2d4a7c 100%); border-radius: 26px; aspect-ratio: 390/844; overflow: hidden;">

                            <!-- Status Bar -->
                            <div class="d-flex justify-content-between align-items-center px-4 pt-4 pb-2 text-white" style="font-size: 12px;">
                                <span>9:41</span>
                                <div class="d-flex gap-1 align-items-center">
                                    <i class="bi bi-reception-4"></i>
                                    <i class="bi bi-wifi"></i>
                                    <i class="bi bi-battery-full"></i>
                                </div>
                            </div>

                            <!-- App Header -->
                            <div class="text-center text-white py-3">
                                <i class="bi bi-bank2 fs-2 mb-2 d-block opacity-75"></i>
                                <h6 class="mb-0 fw-bold">EduBank</h6>
                                <small class="opacity-75">Überweisung bestätigen</small>
                            </div>

                            <!-- Content Area -->
                            <div id="confirmContent" class="bg-white mx-3 rounded-4 p-4 shadow">

                                <!-- Initial State: Confirmation Details -->
                                <div id="stateInitial">
                                    <div class="text-center mb-4">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                            <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                                        </div>
                                        <h6 class="mb-1">Überweisung prüfen</h6>
                                        <small class="text-muted">Bitte kontrollieren Sie die Daten</small>
                                    </div>

                                    <div class="border rounded-3 p-3 mb-3" style="background: #f8f9fa;">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Empfänger</small>
                                            <strong>Werbeagentur Kreativ</strong>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">IBAN</small>
                                            <span class="font-monospace small">AT12 3456 7890 1234 5678</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Verwendungszweck</small>
                                            <span>Rechnung Nr. 2025-0142</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Betrag</small>
                                            <strong class="fs-5 text-primary">1.250,00 €</strong>
                                        </div>
                                    </div>

                                    <button id="btnConfirm" class="btn btn-success w-100 py-2" onclick="startConfirmation()">
                                        <i class="bi bi-check-lg me-2"></i>Bestätigen
                                    </button>

                                    <button class="btn btn-link text-muted w-100 mt-2" data-bs-dismiss="modal">
                                        Abbrechen
                                    </button>
                                </div>

                                <!-- Processing State -->
                                <div id="stateProcessing" class="text-center py-4" style="display: none;">
                                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="visually-hidden">Wird verarbeitet...</span>
                                    </div>
                                    <h6 class="mb-1">Wird verarbeitet...</h6>
                                    <small class="text-muted">Bitte warten Sie einen Moment</small>
                                </div>

                                <!-- Success State -->
                                <div id="stateSuccess" class="text-center py-4" style="display: none;">
                                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                        <i class="bi bi-check-lg text-success" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h5 class="text-success mb-2">Erfolgreich!</h5>
                                    <p class="text-muted small mb-3">
                                        Ihre Überweisung über<br>
                                        <strong class="text-dark fs-5">1.250,00 €</strong><br>
                                        wurde ausgeführt.
                                    </p>
                                    <button class="btn btn-outline-success" onclick="resetDemo()">
                                        <i class="bi bi-arrow-repeat me-1"></i>Demo wiederholen
                                    </button>
                                </div>

                            </div>

                        </div>

                        <!-- Home Indicator -->
                        <div class="mx-auto mt-2 mb-1" style="width: 100px; height: 4px; background: #666; border-radius: 3px;"></div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- JavaScript für die Animation -->
<script>
function startConfirmation() {
    // Hide initial state
    document.getElementById('stateInitial').style.display = 'none';
    // Show processing state
    document.getElementById('stateProcessing').style.display = 'block';

    // After 3 seconds, show success
    setTimeout(function() {
        document.getElementById('stateProcessing').style.display = 'none';
        document.getElementById('stateSuccess').style.display = 'block';
    }, 3000);
}

function resetDemo() {
    // Reset all states
    document.getElementById('stateInitial').style.display = 'block';
    document.getElementById('stateProcessing').style.display = 'none';
    document.getElementById('stateSuccess').style.display = 'none';
}

// Reset when modal is closed
document.getElementById('mobileConfirmModal').addEventListener('hidden.bs.modal', function () {
    resetDemo();
});
</script>
