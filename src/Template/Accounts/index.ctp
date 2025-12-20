<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account[]|\Cake\Collection\CollectionInterface $accounts
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-wallet2 me-2"></i><?= __('Konten') ?></h3>
    <?php if($authuser['role'] == 'admin'): ?>
    <a href="/accounts/add" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i><?= __('Neues Konto') ?>
    </a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-primary">
                <tr>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <th><?= $this->Paginator->sort('user_id', 'Übungsfirma') ?></th>
                    <?php endif; ?>
                    <th><?= $this->Paginator->sort('name', 'Kontoname') ?></th>
                    <th><?= $this->Paginator->sort('iban', 'IBAN') ?></th>
                    <th class="text-end"><?= __('Kontostand') ?></th>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <th><?= $this->Paginator->sort('created', 'Erstellt') ?></th>
                    <?php endif; ?>
                    <th class="text-end"><?= __('Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                <tr>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <td><?= $account->has('user') ? h($account->user->name) : '-' ?></td>
                    <?php endif; ?>
                    <td>
                        <strong><?= h($account->name) ?></strong>
                    </td>
                    <td>
                        <code><?= h($account->iban) ?></code>
                        <br><small class="text-muted">BIC: <?= h($account->bic) ?></small>
                    </td>
                    <td class="text-end">
                        <?php
                        $balanceClass = $account->balance >= 0 ? 'text-success' : 'text-danger';
                        ?>
                        <span class="<?= $balanceClass ?> fw-bold">
                            <?= $this->Number->currency($account->balance, 'EUR') ?>
                        </span>
                        <br><small class="text-muted">Limit: <?= $this->Number->currency($account->maxlimit, 'EUR') ?></small>
                    </td>
                    <?php if($authuser['role'] == 'admin'): ?>
                    <td>
                        <small><?= h($account->created->format('d.m.Y')) ?></small>
                    </td>
                    <?php endif; ?>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $account->id],
                                ['class' => 'btn btn-outline-primary', 'escape' => false, 'title' => 'Anzeigen']
                            ) ?>
                            <?php if($authuser['role'] == 'admin'): ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $account->id],
                                ['class' => 'btn btn-outline-secondary', 'escape' => false, 'title' => 'Bearbeiten']
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-arrow-counterclockwise"></i>',
                                ['action' => 'reset', $account->id],
                                ['class' => 'btn btn-outline-warning', 'escape' => false, 'title' => 'Zurücksetzen', 'confirm' => __('Konto "{0}" auf Standardwerte zurücksetzen und alle Transaktionen löschen?', $account->name)]
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $account->id],
                                ['class' => 'btn btn-outline-danger', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('Konto "{0}" wirklich löschen?', $account->name)]
                            ) ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($this->Paginator->total() > 1): ?>
    <div class="card-footer">
        <nav aria-label="Seitennavigation">
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <?= $this->Paginator->first('<i class="bi bi-chevron-double-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->prev('<i class="bi bi-chevron-left"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next('<i class="bi bi-chevron-right"></i>', ['escape' => false]) ?>
                <?= $this->Paginator->last('<i class="bi bi-chevron-double-right"></i>', ['escape' => false]) ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
