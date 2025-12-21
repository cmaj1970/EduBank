<?php
/**
 * Directory - All practice companies from all schools
 *
 * @var \App\View\AppView $this
 * @var array $companiesBySchool Companies grouped by school
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0"><i class="bi bi-building me-2"></i><?= __('Übungsfirmen-Verzeichnis') ?></h3>
        <p class="text-muted mb-0 mt-1">Alle Übungsfirmen aller teilnehmenden Schulen</p>
    </div>
    <a href="/accounts" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i><?= __('Zurück') ?>
    </a>
</div>

<?php if (empty($companiesBySchool)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
            <i class="bi bi-building text-info" style="font-size: 3rem;"></i>
        </div>
        <h4 class="mb-3">Keine Übungsfirmen gefunden</h4>
        <p class="text-muted mb-0">
            Es sind noch keine Übungsfirmen bei anderen Schulen registriert.
        </p>
    </div>
</div>
<?php else: ?>

<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Tipp:</strong> Kopieren Sie die IBAN einer Übungsfirma, um Überweisungen an diese zu tätigen.
    Kontostände werden aus Datenschutzgründen nicht angezeigt.
</div>

<?php foreach ($companiesBySchool as $schoolData): ?>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-mortarboard me-2"></i><?= h($schoolData['school']->name) ?>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th><?= __('Übungsfirma') ?></th>
                        <th><?= __('IBAN') ?></th>
                        <th><?= __('BIC') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schoolData['companies'] as $company): ?>
                    <?php if (!empty($company->accounts)): ?>
                    <?php foreach ($company->accounts as $account): ?>
                    <tr>
                        <td>
                            <strong><?= h($company->name) ?></strong>
                            <?php if ($account->name !== $company->name): ?>
                            <br><small class="text-muted"><?= h($account->name) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <code><?= h($account->iban) ?></code>
                            <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </td>
                        <td>
                            <code><?= h($account->bic) ?></code>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td>
                            <strong><?= h($company->name) ?></strong>
                        </td>
                        <td colspan="2" class="text-muted">
                            <em>Kein Konto vorhanden</em>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer text-muted small">
        <?= count($schoolData['companies']) ?> Übungsfirma(en)
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-iban').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var iban = this.getAttribute('data-iban');
            navigator.clipboard.writeText(iban).then(function() {
                btn.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(function() {
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);
            });
        });
    });
});
</script>
