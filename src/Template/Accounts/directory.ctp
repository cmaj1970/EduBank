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

<?php if (!empty($schoolList)): ?>
<!-- Filter -->
<div class="card mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <select id="schoolFilter" class="form-select form-select-sm">
                    <option value=""><?= __('Alle Schulen') ?></option>
                    <?php foreach ($schoolList as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($selectedSchool ?? '') == $id ? 'selected' : '' ?>>
                        <?= h($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control form-control-sm"
                       placeholder="<?= __('Suche nach Schule, Übungsfirma oder IBAN...') ?>"
                       value="<?= h($search ?? '') ?>"
                       id="directorySearch">
            </div>
            <div class="col-md-2">
                <button type="button" id="resetBtn" class="btn btn-sm btn-outline-secondary w-100" style="display: none;">
                    <i class="bi bi-x-lg"></i> <?= __('Reset') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div id="resultsContainer">
<?php if (empty($companiesBySchool)): ?>
<div class="card border-0 shadow-sm" id="emptyState">
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

<div class="alert alert-info mb-4" id="tipAlert">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Tipp:</strong> Kopieren Sie die IBAN einer Übungsfirma, um Überweisungen an diese zu tätigen.
</div>

<div id="schoolCards">
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
</div>

<?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('directorySearch');
    var schoolFilter = document.getElementById('schoolFilter');
    var resetBtn = document.getElementById('resetBtn');
    var resultsContainer = document.getElementById('resultsContainer');
    var timeout = null;

    // IBAN Copy Handler (delegiert für dynamische Inhalte)
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.copy-iban');
        if (btn) {
            var iban = btn.getAttribute('data-iban');
            navigator.clipboard.writeText(iban).then(function() {
                btn.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(function() {
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);
            });
        }
    });

    // Reset Button Sichtbarkeit
    function updateResetBtn() {
        if (searchInput.value || schoolFilter.value) {
            resetBtn.style.display = 'block';
        } else {
            resetBtn.style.display = 'none';
        }
    }
    updateResetBtn();

    // AJAX Suche
    function doSearch() {
        var params = new URLSearchParams();
        if (schoolFilter.value) params.append('school_id', schoolFilter.value);
        if (searchInput.value) params.append('search', searchInput.value);

        fetch('/accounts/directory?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            renderResults(data.results);
            updateResetBtn();
        })
        .catch(function(err) {
            console.error('Suche fehlgeschlagen:', err);
        });
    }

    // Ergebnisse rendern
    function renderResults(results) {
        if (!results || results.length === 0) {
            resultsContainer.innerHTML = '<div class="card border-0 shadow-sm"><div class="card-body text-center py-5"><div class="rounded-circle bg-info bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;"><i class="bi bi-building text-info" style="font-size: 3rem;"></i></div><h4 class="mb-3">Keine Übungsfirmen gefunden</h4><p class="text-muted mb-0">Keine Treffer für Ihre Suche.</p></div></div>';
            return;
        }

        var html = '<div class="alert alert-info mb-4"><i class="bi bi-info-circle me-2"></i><strong>Tipp:</strong> Kopieren Sie die IBAN einer Übungsfirma, um Überweisungen an diese zu tätigen.</div><div id="schoolCards">';

        results.forEach(function(school) {
            html += '<div class="card border-0 shadow-sm mb-4">';
            html += '<div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>' + escapeHtml(school.school_name) + '</h5></div>';
            html += '<div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0">';
            html += '<thead class="table-light"><tr><th>Übungsfirma</th><th>IBAN</th><th>BIC</th></tr></thead><tbody>';

            school.companies.forEach(function(company) {
                if (company.accounts && company.accounts.length > 0) {
                    company.accounts.forEach(function(account) {
                        html += '<tr><td><strong>' + escapeHtml(company.name) + '</strong>';
                        if (account.name !== company.name) {
                            html += '<br><small class="text-muted">' + escapeHtml(account.name) + '</small>';
                        }
                        html += '</td><td><code>' + escapeHtml(account.iban) + '</code>';
                        html += '<button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="' + escapeHtml(account.iban) + '" title="IBAN kopieren"><i class="bi bi-clipboard"></i></button>';
                        html += '</td><td><code>' + escapeHtml(account.bic) + '</code></td></tr>';
                    });
                } else {
                    html += '<tr><td><strong>' + escapeHtml(company.name) + '</strong></td><td colspan="2" class="text-muted"><em>Kein Konto vorhanden</em></td></tr>';
                }
            });

            html += '</tbody></table></div></div>';
            html += '<div class="card-footer text-muted small">' + school.companies.length + ' Übungsfirma(en)</div></div>';
        });

        html += '</div>';
        resultsContainer.innerHTML = html;
    }

    // HTML escapen
    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Event Listener
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(doSearch, 300);
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(timeout);
                doSearch();
            }
        });
    }

    if (schoolFilter) {
        schoolFilter.addEventListener('change', function() {
            doSearch();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            schoolFilter.value = '';
            doSearch();
        });
    }
});
</script>
