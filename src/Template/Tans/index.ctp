<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tan[]|\Cake\Collection\CollectionInterface $tans
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Tan'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Accounts'), ['controller' => 'Accounts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Account'), ['controller' => 'Accounts', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tans index large-9 medium-8 columns content">
    <h3><?= __('Tans') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('account_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('tan') ?></th>
                <th scope="col"><?= $this->Paginator->sort('used') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tans as $tan): ?>
            <tr>
                <td><?= $this->Number->format($tan->id) ?></td>
                <td><?= $tan->has('account') ? $this->Html->link($tan->account->name, ['controller' => 'Accounts', 'action' => 'view', $tan->account->id]) : '' ?></td>
                <td><?= h($tan->tan) ?></td>
                <td><?= h($tan->used) ?></td>
                <td><?= h($tan->created) ?></td>
                <td><?= h($tan->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $tan->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $tan->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $tan->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tan->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
