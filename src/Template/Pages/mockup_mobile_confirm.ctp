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

<!-- Custom styles for modal -->
<style>
#mobileConfirmModal .modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.6);
    z-index: 1;
}
#mobileConfirmModal .phone-container {
    position: relative;
    z-index: 2;
}
</style>

<div class="modal fade" id="mobileConfirmModal" tabindex="-1" data-bs-backdrop="false" aria-labelledby="mobileConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="margin: 0; max-width: 100%; width: 100%; height: 100%;">
        <div class="modal-content border-0 d-flex align-items-center justify-content-center" style="background: transparent; height: 100%;">

            <!-- Custom Backdrop (clickable to close) -->
            <div class="modal-backdrop-custom" data-bs-dismiss="modal"></div>

            <!-- Smartphone Frame (iPhone 13 Pro: 390x844 CSS pixels, ratio 19.5:9) -->
            <div class="phone-container" style="width: 280px;">

                <!-- Phone Outer Frame -->
                <div style="background: linear-gradient(145deg, #2d2d2d, #1a1a1a); border-radius: 36px; padding: 10px; box-shadow: 0 25px 50px rgba(0,0,0,0.5);">

                    <!-- Phone Inner Frame -->
                    <div style="background: #000; border-radius: 28px; padding: 6px; position: relative;">

                        <!-- Notch (Dynamic Island style) -->
                        <div style="position: absolute; top: 10px; left: 50%; transform: translateX(-50%); width: 80px; height: 22px; background: #000; border-radius: 11px; z-index: 10;"></div>

                        <!-- Screen (iPhone 13 Pro aspect ratio: 390/844) -->
                        <div id="phoneScreen" style="background: linear-gradient(180deg, #1a365d 0%, #2d4a7c 100%); border-radius: 22px; aspect-ratio: 390/844; overflow: hidden; display: flex; flex-direction: column;">

                            <!-- Status Bar -->
                            <div class="d-flex justify-content-between align-items-center px-3 pt-3 pb-1 text-white" style="font-size: 11px; flex-shrink: 0;">
                                <span>9:41</span>
                                <div class="d-flex gap-1 align-items-center">
                                    <i class="bi bi-reception-4"></i>
                                    <i class="bi bi-wifi"></i>
                                    <i class="bi bi-battery-full"></i>
                                </div>
                            </div>

                            <!-- App Header -->
                            <div class="text-center text-white py-2" style="flex-shrink: 0;">
                                <i class="bi bi-bank2 mb-1 d-block opacity-75" style="font-size: 1.5rem;"></i>
                                <div class="fw-bold" style="font-size: 0.85rem;">EduBank</div>
                                <small class="opacity-75" style="font-size: 0.7rem;">Überweisung bestätigen</small>
                            </div>

                            <!-- Content Area -->
                            <div id="confirmContent" class="bg-white mx-2 rounded-3 p-2 shadow" style="flex: 1; overflow: auto; margin-bottom: 8px;">

                                <!-- Initial State: Confirmation Details -->
                                <div id="stateInitial">
                                    <div class="text-center mb-2">
                                        <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-1" style="width: 36px; height: 36px;">
                                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 1rem;"></i>
                                        </div>
                                        <div class="fw-bold" style="font-size: 0.8rem;">Überweisung prüfen</div>
                                        <small class="text-muted" style="font-size: 0.65rem;">Bitte kontrollieren Sie die Daten</small>
                                    </div>

                                    <div class="border rounded-2 p-2 mb-2" style="background: #f8f9fa; font-size: 0.75rem;">
                                        <div class="mb-1">
                                            <small class="text-muted d-block" style="font-size: 0.6rem;">Empfänger</small>
                                            <strong>Werbeagentur Kreativ</strong>
                                        </div>
                                        <div class="mb-1">
                                            <small class="text-muted d-block" style="font-size: 0.6rem;">IBAN</small>
                                            <span class="font-monospace" style="font-size: 0.65rem;">AT12 3456 7890 1234 5678</span>
                                        </div>
                                        <div class="mb-1">
                                            <small class="text-muted d-block" style="font-size: 0.6rem;">Verwendungszweck</small>
                                            <span>Rechnung Nr. 2025-0142</span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Betrag</small>
                                            <strong class="text-primary" style="font-size: 1rem;">1.250,00 €</strong>
                                        </div>
                                    </div>

                                    <button id="btnConfirm" class="btn btn-success w-100 py-1" style="font-size: 0.8rem;" onclick="startConfirmation()">
                                        <i class="bi bi-check-lg me-1"></i>Bestätigen
                                    </button>

                                    <button class="btn btn-link text-muted w-100 py-0 mt-1" style="font-size: 0.7rem;" data-bs-dismiss="modal">
                                        Abbrechen
                                    </button>
                                </div>

                                <!-- Processing State -->
                                <div id="stateProcessing" class="text-center py-3" style="display: none;">
                                    <div class="spinner-border text-primary mb-2" role="status" style="width: 2.5rem; height: 2.5rem;">
                                        <span class="visually-hidden">Wird verarbeitet...</span>
                                    </div>
                                    <div class="fw-bold" style="font-size: 0.8rem;">Wird verarbeitet...</div>
                                    <small class="text-muted" style="font-size: 0.7rem;">Bitte warten</small>
                                </div>

                                <!-- Success State -->
                                <div id="stateSuccess" class="text-center py-3" style="display: none;">
                                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <i class="bi bi-check-lg text-success" style="font-size: 1.8rem;"></i>
                                    </div>
                                    <div class="text-success fw-bold mb-1" style="font-size: 0.9rem;">Erfolgreich!</div>
                                    <p class="text-muted mb-2" style="font-size: 0.7rem;">
                                        Überweisung über<br>
                                        <strong class="text-dark" style="font-size: 1rem;">1.250,00 €</strong><br>
                                        wurde ausgeführt.
                                    </p>
                                    <button class="btn btn-outline-success btn-sm" style="font-size: 0.7rem;" onclick="resetDemo()">
                                        <i class="bi bi-arrow-repeat me-1"></i>Wiederholen
                                    </button>
                                </div>

                            </div>

                        </div>

                        <!-- Home Indicator -->
                        <div class="mx-auto mt-1" style="width: 90px; height: 4px; background: #555; border-radius: 2px;"></div>

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
