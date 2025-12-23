<?php
/**
 * Partnerunternehmen - Fiktive Firmen für Überweisungen (nach Branchen gruppiert)
 *
 * @var \App\View\AppView $this
 * @var array $groupedPartners
 * @var int $partnerCount
 * @var bool $isSuperadmin
 * @var bool $isAdmin
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">

        <!-- Titel und Erklärung -->
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h3 class="mb-2"><i class="bi bi-building me-2"></i>Partnerunternehmen</h3>
                <p class="text-muted mb-0">
                    Diese fiktiven Partnerunternehmen stehen allen Übungsfirmen als Überweisungsempfänger zur Verfügung.
                </p>
            </div>
            <?php if (!empty($isSuperadmin) && $partnerCount == 0): ?>
            <div>
                <?= $this->Form->postLink(
                    '<i class="bi bi-plus-lg me-1"></i> Erstellen',
                    ['action' => 'createPartners'],
                    [
                        'class' => 'btn btn-primary btn-sm',
                        'escape' => false
                    ]
                ) ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($isAdmin) && $partnerCount > 0): ?>
        <!-- Info-Box für Admins -->
        <div class="card border-info mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title text-info mb-2">
                            <i class="bi bi-info-circle me-2"></i>Wozu dienen Partnerunternehmen?
                        </h6>
                        <p class="card-text small mb-2">
                            Partnerunternehmen sind fiktive Geschäftspartner, an die Übungsfirmen überweisen können.
                            Sie simulieren typische Lieferanten und Dienstleister (Bürobedarf, IT, Versicherungen etc.).
                        </p>
                        <ul class="small mb-0 ps-3">
                            <li>Bei einer Überweisung können Übungsfirmen diese Empfänger aus dem Dropdown auswählen</li>
                            <li>IBANs und BICs sind systemweit gültig</li>
                            <li><?= $partnerCount ?> Unternehmen in 5 Branchen für realistisches Üben</li>
                        </ul>
                    </div>
                    <a href="<?= $this->Url->build(['action' => 'exportPartnersCsv']) ?>" class="btn btn-outline-secondary btn-sm ms-3 flex-shrink-0">
                        <i class="bi bi-download me-1"></i> CSV für Excel
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($isSuperadmin) && $partnerCount > 0): ?>
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Hinweis:</strong> Partnerunternehmen dürfen nach dem initialen Setup nicht mehr gelöscht oder neu generiert werden,
            da sonst bestehende Überweisungen im gesamten System ungültig werden (IBAN-Referenzen).
        </div>
        <?php endif; ?>

        <?php if (!empty($groupedPartners)): ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 45%">Unternehmen</th>
                                <th style="width: 35%">IBAN</th>
                                <th style="width: 20%">BIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groupedPartners as $branch => $partners): ?>
                            <!-- Branchen-Überschrift -->
                            <tr class="table-secondary">
                                <td colspan="3" class="fw-semibold">
                                    <i class="bi bi-folder me-2"></i><?= h($branch) ?>
                                </td>
                            </tr>
                            <?php foreach ($partners as $partner): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-building text-primary me-2"></i>
                                    <?= h($partner->name) ?>
                                    <?php if ($partner->description): ?>
                                    <br><small class="text-muted"><?= h($partner->description) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="font-monospace">
                                    <?= h($partner->iban) ?>
                                    <a href="#" class="text-muted ms-1 copy-iban" data-iban="<?= h($partner->iban) ?>" title="IBAN kopieren">
                                        <i class="bi bi-clipboard"></i>
                                    </a>
                                </td>
                                <td class="font-monospace"><?= h($partner->bic) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (empty($isAdmin)): ?>
        <div class="text-muted small mt-3">
            <i class="bi bi-info-circle me-1"></i>
            Bei einer Überweisung können diese Empfänger aus dem Dropdown ausgewählt werden.
            <span class="badge bg-secondary ms-2"><?= $partnerCount ?> Unternehmen</span>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="rounded-circle bg-secondary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-building text-secondary" style="font-size: 3rem;"></i>
                </div>
                <h4 class="mb-3">Keine Partnerunternehmen vorhanden</h4>
                <?php if (!empty($isSuperadmin)): ?>
                <p class="text-muted mb-4" style="max-width: 500px; margin: 0 auto;">
                    Erstellen Sie 25 fiktive Partnerunternehmen in 5 Branchen, an die alle Übungsfirmen überweisen können.
                </p>
                <?= $this->Form->postLink(
                    '<i class="bi bi-plus-lg me-2"></i> Partnerunternehmen erstellen',
                    ['action' => 'createPartners'],
                    [
                        'class' => 'btn btn-primary btn-lg',
                        'escape' => false
                    ]
                ) ?>
                <?php else: ?>
                <p class="text-muted mb-0">Die Partnerunternehmen wurden noch nicht eingerichtet.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-iban').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var iban = this.getAttribute('data-iban');
            var el = this;
            navigator.clipboard.writeText(iban).then(function() {
                el.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(function() {
                    el.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 1500);
            });
        });
    });
});
</script>
