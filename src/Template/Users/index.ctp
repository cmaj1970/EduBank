<?php
/**
 * Übungsfirmen-Übersicht für Schuladmins
 * Master-Detail Layout mit Live-Transaktions-Feed
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 * @var array $recentTransactions
 */

# Schule des aktuellen Admins
$school = isset($loggedinschool) ? $loggedinschool : null;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-building me-2"></i><?= $school ? h($school['name']) : 'Übungsfirmen' ?>
    </h4>
    <div class="d-flex gap-2">
        <a href="/users/add" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Neue Übungsfirma
        </a>
        <?php if ($isSchoolAdmin): ?>
        <button class="btn btn-outline-secondary btn-sm" id="toggleFeed" title="Feed ein-/ausblenden">
            <i class="bi bi-layout-sidebar-reverse"></i>
        </button>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($isSuperadmin) && $isSuperadmin && !empty($schoolList)): ?>
<!-- Filter für Superadmin -->
<div class="card mb-4">
    <div class="card-body py-2">
        <form method="get" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="school_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Alle Schulen</option>
                    <?php foreach ($schoolList as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($selectedSchool ?? '') == $id ? 'selected' : '' ?>>
                        <?= h($name) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Suche..." value="<?= h($search ?? '') ?>">
            </div>
            <div class="col-md-2">
                <?php if (!empty($selectedSchool) || !empty($search)): ?>
                <a href="/users" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($isSchoolAdmin): ?>
<!-- Passwort-Info -->
<div class="alert alert-info d-flex align-items-center mb-4 py-2">
    <i class="bi bi-key-fill me-2"></i>
    <span>Passwort für Schüler: <code class="user-select-all"><?= h($defaultPassword) ?></code></span>
    <button type="button" class="btn btn-sm btn-link ms-2 p-0" onclick="navigator.clipboard.writeText('<?= h($defaultPassword) ?>'); this.innerHTML='<i class=\'bi bi-check\'></i>';">
        <i class="bi bi-clipboard"></i>
    </button>
</div>

<!-- Tab-Umschalter (Mobile immer, Desktop bei eingeklappter Sidebar) -->
<div class="mb-3" id="tabSwitcher" <?= $this->HelpText->attr('schuladmin', 'tab_switcher') ?>>
    <div class="btn-group w-100" role="group">
        <button type="button" class="btn btn-tab active" id="mobileTabFirmen">
            <i class="bi bi-building me-1"></i>Übungsfirmen
        </button>
        <button type="button" class="btn btn-tab" id="mobileTabTransaktionen">
            <i class="bi bi-activity me-1"></i>Transaktionen
        </button>
    </div>
</div>
<?php endif; ?>

<?php if ($users->isEmpty()): ?>
<!-- Keine Übungsfirmen -->
<div class="text-center py-5">
    <i class="bi bi-building text-muted" style="font-size: 4rem;"></i>
    <h5 class="mt-3">Noch keine Übungsfirmen</h5>
    <p class="text-muted">Erstellen Sie die erste Übungsfirma für Ihre Schüler.</p>
    <a href="/users/add" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Erste Übungsfirma erstellen
    </a>
</div>

<?php else: ?>

<div class="row">
    <!-- Hauptbereich: Übungsfirmen + Konten -->
    <div class="<?= $isSchoolAdmin ? 'col-lg-6' : 'col-12' ?>" id="mainContent">
        <div class="card sticky-card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2"></i>Übungsfirmen & Konten</span>
                <small class="text-muted"><?= $users->count() ?> Übungsfirmen</small>
            </div>
            <div class="table-wrapper" <?= $this->HelpText->attr('schuladmin', 'firmen_tabelle') ?>>
                <table class="table mb-0" id="firmenTable">
                    <thead class="table-light">
                        <tr>
                            <?php if ($isSchoolAdmin): ?>
                            <th style="width: 30px;">
                                <input type="checkbox" class="form-check-input" id="selectAll" title="Alle auswählen">
                            </th>
                            <?php endif; ?>
                            <th>Name / Konto</th>
                            <th>Details</th>
                            <th class="text-end konto-only">Kontostand</th>
                            <th style="width: 120px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <?php $hasAccounts = !empty($user->accounts); ?>
                        <?php
                            # Letzter Login formatieren
                            $lastLogin = null;
                            if ($user->last_login) {
                                $diff = time() - $user->last_login->getTimestamp();
                                if ($diff < 3600) {
                                    $lastLogin = 'vor ' . max(1, round($diff / 60)) . ' Min';
                                } elseif ($diff < 86400) {
                                    $lastLogin = 'vor ' . round($diff / 3600) . ' Std';
                                } else {
                                    $lastLogin = $user->last_login->format('d.m.Y H:i');
                                }
                            }
                        ?>
                        <!-- Übungsfirma-Zeile -->
                        <tr class="firma-row" data-user-id="<?= $user->id ?>">
                            <?php if ($isSchoolAdmin): ?>
                            <td <?= $this->HelpText->attr('schuladmin', 'checkbox') ?>>
                                <input type="checkbox" class="form-check-input firma-checkbox"
                                       value="<?= $user->id ?>" data-name="<?= h($user->name) ?>">
                            </td>
                            <?php endif; ?>
                            <td>
                                <strong><?= h($user->name) ?></strong>
                                <br>
                                <small class="text-muted">Login: <?= h($user->username) ?></small>
                            </td>
                            <td>
                                <?php if ($user->active): ?>
                                    <span class="badge bg-success me-2">Aktiv</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary me-2">Inaktiv</span>
                                <?php endif; ?>
                                <span <?= $this->HelpText->attr('schuladmin', 'last_login') ?>>
                                <?php if ($lastLogin): ?>
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i><?= $lastLogin ?>
                                </small>
                                <?php else: ?>
                                <small class="text-muted">Noch nie angemeldet</small>
                                <?php endif; ?>
                                </span>
                            </td>
                            <td class="konto-only"></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if ($isSchoolAdmin): ?>
                                    <a href="/users/impersonate/<?= $user->id ?>" class="btn btn-outline-success" title="Anmelden als" <?= $this->HelpText->attr('schuladmin', 'btn_impersonate') ?>>
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="/users/edit/<?= $user->id ?>" class="btn btn-outline-secondary" title="Bearbeiten" <?= $this->HelpText->attr('schuladmin', 'btn_edit_firma') ?>>
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?= $this->Html->link(
                                        '<i class="bi bi-arrow-right"></i>',
                                        ['controller' => 'Users', 'action' => 'view', $user->id],
                                        ['class' => 'btn btn-outline-primary', 'escape' => false, 'title' => 'Details']
                                    ) ?>
                                </div>
                            </td>
                        </tr>
                        <!-- Konto-Zeilen (eingerückt, anderer Hintergrund) -->
                        <?php if ($hasAccounts): ?>
                            <?php foreach ($user->accounts as $account): ?>
                            <tr class="konto-row" data-user-id="<?= $user->id ?>" <?= $this->HelpText->attr('schuladmin', 'konto_zeile') ?>>
                                <?php if ($isSchoolAdmin): ?>
                                <td></td>
                                <?php endif; ?>
                                <td class="ps-4">
                                    <i class="bi bi-credit-card text-muted me-2"></i>
                                    <strong><?= h($account->name ?: 'Girokonto') ?></strong>
                                    <br>
                                    <span class="font-monospace small text-muted ps-4"><?= h($account->iban) ?></span>
                                    <button type="button" class="btn btn-link btn-sm p-0 ms-1 copy-iban"
                                            data-iban="<?= h($account->iban) ?>" title="IBAN kopieren" <?= $this->HelpText->attr('schuladmin', 'copy_iban') ?>>
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        Limit: <?= $this->Number->currency($account->maxlimit ?? 0, 'EUR') ?>
                                    </small>
                                </td>
                                <td class="text-end konto-only">
                                    <span class="fw-bold <?= $account->balance >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= $this->Number->currency($account->balance, 'EUR') ?>
                                    </span>
                                </td>
                                <td>
                                    <span <?= $this->HelpText->attr('schuladmin', 'btn_edit_konto') ?>>
                                    <?= $this->Html->link(
                                        '<i class="bi bi-pencil"></i>',
                                        ['controller' => 'Accounts', 'action' => 'edit', $account->id],
                                        ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false, 'title' => 'Konto bearbeiten']
                                    ) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($this->Paginator->total() > 1): ?>
            <div class="card-footer d-flex justify-content-between align-items-center py-2">
                <small class="text-muted">
                    <?= $this->Paginator->counter('{{start}}-{{end}} von {{count}}') ?>
                </small>
                <?php
                $this->Paginator->setTemplates([
                    'number' => '<li class="page-item"><a class="page-link" href="{{url}}">{{text}}</a></li>',
                    'current' => '<li class="page-item active"><span class="page-link">{{text}}</span></li>',
                    'prevActive' => '<li class="page-item"><a class="page-link" href="{{url}}">‹</a></li>',
                    'prevDisabled' => '<li class="page-item disabled"><span class="page-link">‹</span></li>',
                    'nextActive' => '<li class="page-item"><a class="page-link" href="{{url}}">›</a></li>',
                    'nextDisabled' => '<li class="page-item disabled"><span class="page-link">›</span></li>',
                ]);
                ?>
                <nav>
                    <ul class="pagination pagination-sm mb-0">
                        <?= $this->Paginator->prev() ?>
                        <?= $this->Paginator->numbers(['modulus' => 3]) ?>
                        <?= $this->Paginator->next() ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($isSchoolAdmin && !empty($recentTransactions)): ?>
    <!-- Sidebar: Live-Transaktions-Feed (sticky) -->
    <div class="col-lg-6" id="feedSidebar">
        <div class="card sticky-sidebar" <?= $this->HelpText->attr('schuladmin', 'transaktionen') ?>>
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-activity me-2"></i>Live-Transaktionen</span>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted"><?= count($recentTransactions) ?> Einträge</small>
                    <span class="badge bg-primary" id="filterBadge" style="display: none;">0</span>
                    <button class="btn btn-link btn-sm p-0" id="refreshFeed" title="Aktualisieren">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
            </div>

            <!-- Filter-Info -->
            <div class="card-body py-2 border-bottom bg-light" id="filterInfo" style="display: none;" <?= $this->HelpText->attr('schuladmin', 'filter') ?>>
                <small class="text-muted">
                    <i class="bi bi-funnel me-1"></i>
                    Filter: <span id="filterNames"></span>
                    <a href="#" class="ms-2" id="clearFilter">Zurücksetzen</a>
                </small>
            </div>

            <!-- Transaktions-Liste (scrollbar) -->
            <div class="transaction-feed-wrapper">
                <div class="list-group list-group-flush" id="transactionFeed">
                    <?php foreach ($recentTransactions as $tx): ?>
                    <?php
                        $senderName = $tx->account->user->name ?? 'Unbekannt';
                        $senderId = $tx->account->user->id ?? 0;
                        $senderSchoolId = $tx->account->user->school_id ?? 0;

                        # Empfänger-Schule ermitteln
                        $recipientSchool = null;
                        if (isset($recipientAccounts[$tx->empfaenger_iban])) {
                            $recipientAcc = $recipientAccounts[$tx->empfaenger_iban];
                            if (!empty($recipientAcc->user->school) && $recipientAcc->user->school_id != $senderSchoolId) {
                                $recipientSchool = $recipientAcc->user->school->name;
                            }
                        }
                    ?>
                    <div class="list-group-item py-2 transaction-item" data-sender-id="<?= $senderId ?>" data-tx-id="<?= $tx->id ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="small">
                                <span class="text-muted"><?= $tx->created->format('d.m.Y H:i') ?></span>
                                <br>
                                <strong><?= h($senderName) ?></strong>
                                <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                <?= $this->Number->currency($tx->betrag, 'EUR') ?>
                                an <strong><?= h($tx->empfaenger_name) ?></strong>
                                <?php if ($recipientSchool): ?>
                                <span class="text-muted">(<?= h($recipientSchool) ?>)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!empty($tx->zahlungszweck)): ?>
                        <small class="text-muted d-block mt-1">
                            <i class="bi bi-chat-left-text me-1"></i><?= h($tx->zahlungszweck) ?>
                        </small>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<style>
/* Tab-Buttons passend zum Header */
.btn-tab {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #adb5bd;
}
.btn-tab:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #6c757d;
}
.btn-tab.active {
    background-color: #6c757d;
    border-color: #6c757d;
    color: #fff;
}
.btn-tab:focus {
    box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
}

/* Desktop: Beide Cards sticky und scrollbar */
@media (min-width: 992px) {
    .sticky-card,
    .sticky-sidebar {
        position: sticky;
        top: 1rem;
        max-height: calc(100vh - 2rem);
        display: flex;
        flex-direction: column;
    }

    /* Scrollbarer Tabellenbereich */
    .table-wrapper {
        overflow-y: auto;
        flex: 1;
        max-height: calc(100vh - 12rem);
    }

    /* Sticky Table Header innerhalb des Wrappers */
    #firmenTable thead {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
    }
    #firmenTable thead th {
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Scrollbarer Transaktions-Feed */
    .transaction-feed-wrapper {
        overflow-y: auto;
        flex: 1;
        max-height: calc(100vh - 12rem);
    }
}

/* Übungsfirma-Zeilen: weißer Hintergrund, leicht hervorgehoben */
.firma-row {
    background-color: #fff;
    border-top: 2px solid #dee2e6 !important;
}
.firma-row:first-child {
    border-top: none !important;
}
.firma-row td {
    padding-top: 0.75rem;
    padding-bottom: 0.5rem;
}

/* Konto-Zeilen: hellblauer Hintergrund, eingerückt, linker Akzent */
.konto-row {
    background-color: #f0f7ff !important;
    font-size: 0.9rem;
}
.konto-row td {
    border-top: none !important;
    padding-top: 0.25rem;
    padding-bottom: 0.25rem;
}
.konto-row td:first-child {
    border-left: 3px solid #0d6efd;
}

/* Letzte Konto-Zeile vor nächster Firma */
.konto-row:has(+ .firma-row) td,
.konto-row:last-child td {
    padding-bottom: 0.5rem;
}

/* Kontostand-Spalte nur bei Konto-Zeilen (Single-View) */
.konto-only {
    display: none;
}
.single-view .konto-only {
    display: table-cell;
}

/* Expanded-Modus für Hauptbereich */
#mainContent.expanded {
    width: 100%;
    flex: 0 0 100%;
    max-width: 100%;
}

/* Tab-Umschalter: Desktop standardmäßig ausgeblendet */
@media (min-width: 992px) {
    #tabSwitcher {
        display: none;
    }
    #tabSwitcher.show-switcher {
        display: block;
    }
}

/* Single-View-Modus: Keine Scrollbars, volle Höhe, volle Breite */
.single-view .sticky-card,
.single-view .sticky-sidebar {
    position: static;
    max-height: none;
}
.single-view .table-wrapper,
.single-view .transaction-feed-wrapper {
    overflow-y: visible;
    max-height: none;
}
.single-view #firmenTable thead {
    position: static;
}
.single-view #mainContent,
.single-view #feedSidebar {
    width: 100%;
    flex: 0 0 100%;
    max-width: 100%;
}

/* Copy-Button Animation */
.copy-iban .bi-check {
    color: #198754;
}

/* Spin-Animation für Refresh */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.spin {
    animation: spin 0.5s linear infinite;
}

/* Transaktions-Items hover */
.transaction-item:hover {
    background-color: #f8f9fa;
}

/* Neue Transaktionen hervorheben */
.transaction-item.new-transaction {
    background-color: #d4edda;
    transition: background-color 0.5s ease;
}
</style>

<?php if ($isSchoolAdmin): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var feedSidebar = document.getElementById('feedSidebar');
    var mainContent = document.getElementById('mainContent');
    var toggleBtn = document.getElementById('toggleFeed');
    var filterInfo = document.getElementById('filterInfo');
    var filterBadge = document.getElementById('filterBadge');
    var filterNames = document.getElementById('filterNames');
    var transactionItems = document.querySelectorAll('.transaction-item');
    var mobileTabFirmen = document.getElementById('mobileTabFirmen');
    var mobileTabTransaktionen = document.getElementById('mobileTabTransaktionen');
    var tabSwitcher = document.getElementById('tabSwitcher');

    // Aktuelle Ansicht aus localStorage laden
    var currentView = localStorage.getItem('schuladmin_current_view') || 'firmen';

    // Sidebar-Status aus localStorage laden
    var sidebarCollapsed = localStorage.getItem('schuladmin_sidebar_collapsed') === 'true';
    if (sidebarCollapsed && toggleBtn) {
        toggleBtn.querySelector('i').className = 'bi bi-layout-sidebar';
    }

    // View-Status aktualisieren
    function updateView() {
        var isDesktop = window.innerWidth >= 992;
        var isCollapsed = localStorage.getItem('schuladmin_sidebar_collapsed') === 'true';
        var kontoRows = document.querySelectorAll('.konto-row');

        if (!feedSidebar) {
            // Keine Transaktionen - immer Single-View
            document.body.classList.add('single-view');
            if (tabSwitcher) tabSwitcher.style.display = 'none';
            kontoRows.forEach(function(row) { row.style.display = ''; });
            return;
        }

        if (isDesktop && !isCollapsed) {
            // Split-View: Beide Spalten nebeneinander, nur Firmen-Zeilen
            if (tabSwitcher) tabSwitcher.classList.remove('show-switcher');
            mainContent.style.display = '';
            feedSidebar.style.display = '';
            mainContent.classList.remove('expanded');
            document.body.classList.remove('single-view');
            // Konto-Zeilen ausblenden
            kontoRows.forEach(function(row) { row.style.display = 'none'; });
        } else {
            // Single-View: Tab-Modus mit Konten
            if (tabSwitcher) tabSwitcher.classList.add('show-switcher');
            document.body.classList.add('single-view');
            // Konto-Zeilen einblenden
            kontoRows.forEach(function(row) { row.style.display = ''; });

            if (currentView === 'firmen') {
                mainContent.style.display = 'block';
                mainContent.classList.add('expanded');
                feedSidebar.style.display = 'none';
                if (mobileTabFirmen) mobileTabFirmen.classList.add('active');
                if (mobileTabTransaktionen) mobileTabTransaktionen.classList.remove('active');
            } else {
                mainContent.style.display = 'none';
                feedSidebar.style.display = 'block';
                if (mobileTabFirmen) mobileTabFirmen.classList.remove('active');
                if (mobileTabTransaktionen) mobileTabTransaktionen.classList.add('active');
            }
        }
    }

    // Initial und bei Resize
    updateView();
    window.addEventListener('resize', updateView);

    // Tab-Umschalter
    if (mobileTabFirmen) {
        mobileTabFirmen.addEventListener('click', function() {
            currentView = 'firmen';
            localStorage.setItem('schuladmin_current_view', 'firmen');
            updateView();
        });
    }

    if (mobileTabTransaktionen) {
        mobileTabTransaktionen.addEventListener('click', function() {
            currentView = 'transaktionen';
            localStorage.setItem('schuladmin_current_view', 'transaktionen');
            updateView();
        });
    }

    // Desktop: Feed ein-/ausklappen
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            var isCollapsed = localStorage.getItem('schuladmin_sidebar_collapsed') === 'true';

            if (isCollapsed) {
                // Aufklappen
                toggleBtn.querySelector('i').className = 'bi bi-layout-sidebar-reverse';
                localStorage.setItem('schuladmin_sidebar_collapsed', 'false');
            } else {
                // Einklappen
                toggleBtn.querySelector('i').className = 'bi bi-layout-sidebar';
                currentView = 'firmen';
                localStorage.setItem('schuladmin_sidebar_collapsed', 'true');
            }

            updateView();
        });
    }

    // Alle Checkboxen auswählen
    var selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            var checkboxes = document.querySelectorAll('.firma-checkbox');
            checkboxes.forEach(function(cb) {
                cb.checked = this.checked;
            }.bind(this));
            updateFilter();
        });
    }

    // Einzelne Checkbox
    document.querySelectorAll('.firma-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateFilter);
    });

    // Filter aktualisieren
    function updateFilter() {
        if (!filterInfo || !filterBadge || !filterNames) return;

        var checked = document.querySelectorAll('.firma-checkbox:checked');
        var selectedIds = [];
        var selectedNames = [];

        checked.forEach(function(cb) {
            selectedIds.push(cb.value);
            selectedNames.push(cb.dataset.name);
        });

        if (selectedIds.length > 0 && selectedIds.length < document.querySelectorAll('.firma-checkbox').length) {
            filterInfo.style.display = 'block';
            filterBadge.style.display = 'inline';
            filterBadge.textContent = selectedIds.length;
            filterNames.textContent = selectedNames.join(', ');

            // Transaktionen filtern
            transactionItems.forEach(function(item) {
                var senderId = item.dataset.senderId;
                if (selectedIds.includes(senderId)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        } else {
            filterInfo.style.display = 'none';
            filterBadge.style.display = 'none';

            // Alle anzeigen
            transactionItems.forEach(function(item) {
                item.style.display = 'block';
            });
        }
    }

    // Filter zurücksetzen
    var clearFilter = document.getElementById('clearFilter');
    if (clearFilter) {
        clearFilter.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.firma-checkbox').forEach(function(cb) {
                cb.checked = false;
            });
            var selectAll = document.getElementById('selectAll');
            if (selectAll) selectAll.checked = false;
            updateFilter();
        });
    }

    // IBAN kopieren
    document.querySelectorAll('.copy-iban').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var iban = this.getAttribute('data-iban');
            navigator.clipboard.writeText(iban).then(function() {
                btn.innerHTML = '<i class="bi bi-check"></i>';
                setTimeout(function() {
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);
            });
        });
    });

    // Bekannte Transaktions-IDs speichern
    var knownTransactionIds = {};
    document.querySelectorAll('.transaction-item').forEach(function(item) {
        var id = item.getAttribute('data-tx-id');
        if (id) knownTransactionIds[id] = true;
    });

    // Währung formatieren
    function formatCurrency(amount) {
        return new Intl.NumberFormat('de-AT', { style: 'currency', currency: 'EUR' }).format(amount);
    }

    // Transaktion als HTML rendern
    function renderTransaction(tx, isNew) {
        var schoolInfo = tx.recipient_school ? ' <span class="text-muted">(' + tx.recipient_school + ')</span>' : '';
        var purposeHtml = tx.purpose ? '<small class="text-muted d-block mt-1"><i class="bi bi-chat-left-text me-1"></i>' + tx.purpose + '</small>' : '';
        var newClass = isNew ? ' new-transaction' : '';

        return '<div class="list-group-item py-2 transaction-item' + newClass + '" data-sender-id="' + tx.sender_id + '" data-tx-id="' + tx.id + '">' +
            '<div class="d-flex justify-content-between align-items-start">' +
            '<div class="small">' +
            '<span class="text-muted">' + tx.created + '</span><br>' +
            '<strong>' + tx.sender_name + '</strong> ' +
            '<i class="bi bi-arrow-right mx-1 text-muted"></i> ' +
            formatCurrency(tx.amount) + ' an <strong>' + tx.recipient_name + '</strong>' + schoolInfo +
            '</div></div>' + purposeHtml + '</div>';
    }

    // Feed via AJAX aktualisieren
    function refreshTransactions() {
        var icon = refreshFeed ? refreshFeed.querySelector('i') : null;
        if (icon) icon.classList.add('spin');

        fetch('/users/ajax-transactions')
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.transactions && transactionFeed) {
                    var html = '';
                    var newIds = [];

                    data.transactions.forEach(function(tx) {
                        var isNew = !knownTransactionIds[tx.id];
                        if (isNew) newIds.push(tx.id);
                        html += renderTransaction(tx, isNew);
                    });
                    transactionFeed.innerHTML = html;

                    // Neue IDs merken
                    newIds.forEach(function(id) {
                        knownTransactionIds[id] = true;
                    });

                    // Highlight nach 60 Sekunden entfernen
                    if (newIds.length > 0) {
                        setTimeout(function() {
                            document.querySelectorAll('.transaction-item.new-transaction').forEach(function(item) {
                                item.classList.remove('new-transaction');
                            });
                        }, 60000);
                    }

                    // Event-Listener für neue Items (Filter)
                    transactionItems = document.querySelectorAll('.transaction-item');
                    updateFilter();

                    // Anzahl aktualisieren
                    var countEl = document.querySelector('#feedSidebar .card-header .text-muted');
                    if (countEl) countEl.textContent = data.count + ' Einträge';
                }
            })
            .catch(function(err) { console.error('Feed refresh failed:', err); })
            .finally(function() {
                if (icon) icon.classList.remove('spin');
            });
    }

    var transactionFeed = document.getElementById('transactionFeed');
    var refreshFeed = document.getElementById('refreshFeed');

    // Manueller Refresh-Button
    if (refreshFeed) {
        refreshFeed.addEventListener('click', function(e) {
            e.preventDefault();
            refreshTransactions();
        });
    }

    // Auto-Refresh alle 10 Sekunden (nur wenn Feed sichtbar)
    setInterval(function() {
        if (feedSidebar && feedSidebar.style.display !== 'none') {
            refreshTransactions();
        }
    }, 10000);
});
</script>
<?php endif; ?>
