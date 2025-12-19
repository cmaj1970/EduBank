<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<?= $this->element('nav'); ?>
<div class="accounts form large-9 medium-8 columns content">
    <?php if(!empty($users)) : ?>
    <?= $this->Form->create($account) ?>
    <fieldset>
        <legend><?= __('Add Account') ?></legend>
        <?php
        echo $this->Form->control('user_id', ['options' => $users, 'empty' => false]);
        echo $this->Form->control('name');
        echo $this->Form->control('iban', array('readonly'));
        echo $this->Form->control('bic', array('readonly'));
        echo $this->Form->control('maxlimit', array('value' => 2000, 'readonly'));
        echo $this->Form->control('balance', array('value' => 10000, 'readonly'));
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
    <?php else: ?>
        <p>Es können keine Konten hinzugefügt werden, da keine Benutzer angelegt sind oder bereits allen Benutzern dieser Schule ein Konto zugewiesen wurde.</p>
    <p><?= $this->Html->link(__('List Accounts'), ['controller' => 'Accounts','action' => 'index']) ?></p>
    <p><?= $this->Html->link(__('Add User'), ['controller' => 'Users', 'action' => 'add']) ?></p>
    <?php endif; ?>
</div>
