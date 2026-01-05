<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tan $tan
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Tans'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Accounts'), ['controller' => 'Accounts', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Account'), ['controller' => 'Accounts', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="tans form large-9 medium-8 columns content">
    <?= $this->Form->create($tan) ?>
    <fieldset>
        <legend><?= __('Add Tan') ?></legend>
        <?php
            echo $this->Form->control('account_id', ['options' => $accounts, 'empty' => true]);
            echo $this->Form->control('tan', ['value' => substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),0,8)]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
