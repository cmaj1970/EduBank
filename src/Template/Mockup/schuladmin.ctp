<?php
/**
 * Mockup: Schuladmin Master-Detail Layout
 * 2/3 Tabelle + 1/3 Live-Feed (ein-/ausklappbar, sticky)
 *
 * @var \App\View\AppView $this
 * @var object $school
 * @var array $users
 * @var array $transactions
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-building me-2"></i><?= h($school->name) ?>
    </h4>
    <div class="d-flex gap-2">
        <a href="/users/add" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Neue Übungsfirma
        </a>
        <button class="btn btn-outline-secondary btn-sm" id="toggleFeed" title="Feed ein-/ausblenden">
            <i class="bi bi-layout-sidebar-reverse"></i>
        </button>
    </div>
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

<div class="row">
    <!-- Hauptbereich: Übungsfirmen + Konten -->
    <div class="col-lg-6" id="mainContent">
        <div class="card sticky-card">
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2"></i>Übungsfirmen & Konten</span>
                <small class="text-muted"><?= count($users) ?> Übungsfirmen</small>
            </div>
            <div class="table-wrapper" <?= $this->HelpText->attr('schuladmin', 'firmen_tabelle') ?>>
                <table class="table mb-0" id="firmenTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30px;">
                                <input type="checkbox" class="form-check-input" id="selectAll" title="Alle auswählen">
                            </th>
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
                            <td <?= $this->HelpText->attr('schuladmin', 'checkbox') ?>>
                                <input type="checkbox" class="form-check-input firma-checkbox"
                                       value="<?= $user->id ?>" data-name="<?= h($user->name) ?>">
                            </td>
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
                                    <a href="/users/impersonate/<?= $user->id ?>" class="btn btn-outline-success" title="Anmelden als" <?= $this->HelpText->attr('schuladmin', 'btn_impersonate') ?>>
                                        <i class="bi bi-box-arrow-in-right"></i>
                                    </a>
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
                                <td></td>
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
        </div>
    </div>

    <!-- Sidebar: Live-Transaktions-Feed (sticky) -->
    <div class="col-lg-6" id="feedSidebar">
        <div class="card sticky-sidebar" <?= $this->HelpText->attr('schuladmin', 'transaktionen') ?>>
            <div class="card-header py-2 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-activity me-2"></i>Live-Transaktionen</span>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted"><?= count($transactions) ?> Einträge</small>
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
                    <?php if (empty($transactions)): ?>
                    <div class="list-group-item text-center text-muted py-4">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">Keine Transaktionen</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                        <?php
                            $senderName = $tx->account_id ? ($tx->account->user->name ?? 'Unbekannt') : $tx->empfaenger_name;
                            $senderId = $tx->account->user->id ?? 0;
                            $senderSchool = $tx->account->user->school->name ?? '';
                        ?>
                        <div class="list-group-item py-2 transaction-item" data-sender-id="<?= $senderId ?>">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="small">
                                    <span class="text-muted"><?= $tx->created->format('d.m.Y H:i') ?></span>
                                    <br>
                                    <strong><?= h($senderName) ?></strong>
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                    <?= $this->Number->currency($tx->betrag, 'EUR') ?>
                                    an <strong><?= h($tx->empfaenger_name) ?></strong>
                                </div>
                            </div>
                            <?php if (!empty($tx->zahlungszweck)): ?>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-chat-left-text me-1"></i><?= h($tx->zahlungszweck) ?>
                            </small>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

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
        max-height: calc(100vh - 8rem);
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
        max-height: calc(100vh - 8rem);
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
</style>

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
    if (sidebarCollapsed) {
        toggleBtn.querySelector('i').className = 'bi bi-layout-sidebar';
    }

    // View-Status aktualisieren
    function updateView() {
        var isDesktop = window.innerWidth >= 992;
        var isCollapsed = localStorage.getItem('schuladmin_sidebar_collapsed') === 'true';
        var kontoRows = document.querySelectorAll('.konto-row');

        if (isDesktop && !isCollapsed) {
            // Split-View: Beide Spalten nebeneinander, nur Firmen-Zeilen
            tabSwitcher.classList.remove('show-switcher');
            mainContent.style.display = '';
            feedSidebar.style.display = '';
            mainContent.classList.remove('expanded');
            document.body.classList.remove('single-view');
            // Konto-Zeilen ausblenden
            kontoRows.forEach(function(row) { row.style.display = 'none'; });
        } else {
            // Single-View: Tab-Modus mit Konten
            tabSwitcher.classList.add('show-switcher');
            document.body.classList.add('single-view');
            // Konto-Zeilen einblenden
            kontoRows.forEach(function(row) { row.style.display = ''; });

            if (currentView === 'firmen') {
                mainContent.style.display = 'block';
                mainContent.classList.add('expanded');
                feedSidebar.style.display = 'none';
                mobileTabFirmen.classList.add('active');
                mobileTabTransaktionen.classList.remove('active');
            } else {
                mainContent.style.display = 'none';
                feedSidebar.style.display = 'block';
                mobileTabFirmen.classList.remove('active');
                mobileTabTransaktionen.classList.add('active');
            }
        }
    }

    // Initial und bei Resize
    updateView();
    window.addEventListener('resize', updateView);

    // Tab-Umschalter
    mobileTabFirmen.addEventListener('click', function() {
        currentView = 'firmen';
        localStorage.setItem('schuladmin_current_view', 'firmen');
        updateView();
    });

    mobileTabTransaktionen.addEventListener('click', function() {
        currentView = 'transaktionen';
        localStorage.setItem('schuladmin_current_view', 'transaktionen');
        updateView();
    });

    // Desktop: Feed ein-/ausklappen
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

    // Alle Checkboxen auswählen
    document.getElementById('selectAll').addEventListener('change', function() {
        var checkboxes = document.querySelectorAll('.firma-checkbox');
        checkboxes.forEach(function(cb) {
            cb.checked = this.checked;
        }.bind(this));
        updateFilter();
    });

    // Einzelne Checkbox
    document.querySelectorAll('.firma-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateFilter);
    });

    // Filter aktualisieren
    function updateFilter() {
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
    document.getElementById('clearFilter').addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.firma-checkbox').forEach(function(cb) {
            cb.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        updateFilter();
    });

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

    // Feed aktualisieren (AJAX-Platzhalter)
    document.getElementById('refreshFeed').addEventListener('click', function() {
        var icon = this.querySelector('i');
        icon.classList.add('spin');

        // TODO: AJAX-Request für neue Transaktionen
        setTimeout(function() {
            icon.classList.remove('spin');
            // location.reload();
        }, 500);
    });
});
</script>
