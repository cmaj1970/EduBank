<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0"><i class="bi bi-people me-2"></i><?= __('Benutzer') ?></h3>
    <a href="/users/add" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i><?= __('Neuer Benutzer') ?>
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th><?= $this->Paginator->sort('name', 'Name') ?></th>
                    <th><?= $this->Paginator->sort('username', 'Benutzername') ?></th>
                    <th><?= $this->Paginator->sort('school_id', 'Schule') ?></th>
                    <th class="text-center"><?= $this->Paginator->sort('active', 'Status') ?></th>
                    <th><?= $this->Paginator->sort('created', 'Erstellt') ?></th>
                    <th class="text-end"><?= __('Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <strong><?= h($user->name) ?></strong>
                    </td>
                    <td>
                        <code><?= h($user->username) ?></code>
                    </td>
                    <td>
                        <?= $user->has('school') ? h($user->school->name) : '<span class="text-muted">-</span>' ?>
                    </td>
                    <td class="text-center">
                        <?php if ($user->active): ?>
                            <span class="badge bg-success">Aktiv</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inaktiv</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small><?= h($user->created->format('d.m.Y')) ?></small>
                    </td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <?= $this->Html->link(
                                '<i class="bi bi-eye"></i>',
                                ['action' => 'view', $user->id],
                                ['class' => 'btn btn-outline-primary', 'escape' => false, 'title' => 'Anzeigen']
                            ) ?>
                            <?= $this->Html->link(
                                '<i class="bi bi-pencil"></i>',
                                ['action' => 'edit', $user->id],
                                ['class' => 'btn btn-outline-secondary', 'escape' => false, 'title' => 'Bearbeiten']
                            ) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash"></i>',
                                ['action' => 'delete', $user->id],
                                ['class' => 'btn btn-outline-danger', 'escape' => false, 'title' => 'Löschen', 'confirm' => __('Benutzer "{0}" wirklich löschen?', $user->name)]
                            ) ?>
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
