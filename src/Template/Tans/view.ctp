<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tan $tan
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Tan'), ['action' => 'edit', $tan->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Tan'), ['action' => 'delete', $tan->id], ['confirm' => __('Are you sure you want to delete # {0}?', $tan->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Tans'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Tan'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Accounts'), ['controller' => 'Accounts', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Account'), ['controller' => 'Accounts', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="tans view large-9 medium-8 columns content">
    <h3><?= h($tan->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Account') ?></th>
            <td><?= $tan->has('account') ? $this->Html->link($tan->account->name, ['controller' => 'Accounts', 'action' => 'view', $tan->account->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Tan') ?></th>
            <td><?= h($tan->tan) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($tan->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($tan->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($tan->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Used') ?></th>
            <td><?= $tan->used ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
