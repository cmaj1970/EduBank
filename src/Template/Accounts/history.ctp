<?php
/**
 * Auftragshistorie - Übersicht aller eigenen Überweisungen
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">

        <!-- Zurück-Button -->
        <div class="mb-3">
            <a href="/accounts/view/<?= $account->id ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Zurück zur Kontoübersicht
            </a>
        </div>

        <!-- Titel und Erklärung -->
        <div class="mb-4">
            <h3 class="mb-2"><i class="bi bi-clock-history me-2"></i>Auftragshistorie</h3>
            <p class="text-muted mb-0">
                Die Auftragshistorie zeigt eine Übersicht aller selbst getätigten Überweisungen.
            </p>
        </div>

        <!-- Auftragshistorie -->
        <div class="card">
            <div class="card-body p-0">
                <?php if (!empty($account->transactions) && $account->transactions->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Empfänger</th>
                                    <th>Gesendet am</th>
                                    <th>Status</th>
                                    <th class="text-end">Betrag</th>
                                    <?php if ($authuser['role'] == 'admin'): ?>
                                    <th class="text-center d-print-none">Aktion</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($account->transactions as $transaction): ?>
                                    <?php
                                    $isExecuted = ($transaction->datum <= new \DateTime());
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= h($transaction->empfaenger_name) ?></strong>
                                            <?php if ($transaction->zahlungszweck): ?>
                                                <br><small class="text-muted"><?= h(\Cake\Utility\Text::truncate($transaction->zahlungszweck, 40)) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-nowrap">
                                            <?= h($transaction->created->format('d.m.Y')) ?><br>
                                            <small class="text-muted"><?= h($transaction->created->format('H:i')) ?> Uhr</small>
                                        </td>
                                        <td class="text-nowrap">
                                            <?php if ($isExecuted): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Durchgeführt
                                                </span>
                                                <br><small class="text-muted">am <?= h($transaction->datum->format('d.m.Y')) ?></small>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-clock me-1"></i>Geplant
                                                </span>
                                                <br><small class="text-muted">für <?= h($transaction->datum->format('d.m.Y')) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <span class="text-danger fw-bold">
                                                -<?= $this->Number->currency($transaction->betrag, 'EUR') ?>
                                            </span>
                                        </td>
                                        <?php if ($authuser['role'] == 'admin'): ?>
                                        <td class="text-center d-print-none">
                                            <?php if (!$isExecuted): ?>
                                                <?= $this->Form->postLink(
                                                    '<i class="bi bi-x-circle me-1"></i>Stornieren',
                                                    ['controller' => 'Transactions', 'action' => 'storno', $transaction->id],
                                                    [
                                                        'class' => 'btn btn-sm btn-outline-danger',
                                                        'escape' => false,
                                                        'confirm' => __('Soll dieser Auftrag wirklich storniert werden?')
                                                    ]
                                                ) ?>
                                            <?php endif; ?>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox display-4"></i>
                        <p class="mt-3 mb-0">Noch keine Überweisungen vorhanden.</p>
                        <a href="/transactions/add" class="btn btn-primary mt-3">
                            <i class="bi bi-send me-1"></i>Erste Überweisung tätigen
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
