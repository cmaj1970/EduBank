<?php
/**
 * Geschäftspartner - System-Konten für Überweisungen
 *
 * @var \App\View\AppView $this
 * @var array $systemAccounts
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">

        <!-- Titel und Erklärung -->
        <div class="mb-4">
            <h3 class="mb-2"><i class="bi bi-shop me-2"></i>Geschäftspartner</h3>
            <p class="text-muted mb-0">
                Diese fiktiven Geschäftspartner stehen allen Übungsfirmen als Überweisungsempfänger zur Verfügung.
            </p>
        </div>

        <?php if (!empty($systemAccounts)): ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Geschäftspartner</th>
                                <th>IBAN</th>
                                <th>BIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($systemAccounts as $account): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-shop text-primary me-2"></i>
                                    <strong><?= h($account->user->name) ?></strong>
                                </td>
                                <td class="font-monospace">
                                    <?= h($account->iban) ?>
                                    <a href="#" class="text-muted ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                                        <i class="bi bi-clipboard"></i>
                                    </a>
                                </td>
                                <td class="font-monospace"><?= h($account->bic) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-muted small mt-3">
            <i class="bi bi-info-circle me-1"></i>
            Bei einer Überweisung können diese Empfänger aus dem Dropdown ausgewählt werden.
        </div>

        <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-shop display-4"></i>
                <p class="mt-3 mb-0">Noch keine Geschäftspartner vorhanden.</p>
                <?php if ($authuser['role'] === 'admin' && !isset($loggedinschool)): ?>
                <a href="/schools/demo-school" class="btn btn-primary mt-3">
                    <i class="bi bi-plus-lg me-1"></i>System-Konten erstellen
                </a>
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
