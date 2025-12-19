<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<?= $this->element('nav'); ?>
<div class="accounts form large-9 medium-8 columns content">
    <?= $this->Form->create($account) ?>
    <fieldset>
        <legend><?= __('Edit Account') ?></legend>
        <?php
            echo $this->Form->hidden('user_id');
            echo $this->Form->control('name');
            echo $this->Form->control('iban', ['readonly']);
            echo $this->Form->control('bic', ['readonly']);
            echo $this->Form->control('maxlimit');
            echo $this->Form->control('balance');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
