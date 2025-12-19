<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<?= $this->element('nav'); ?>
<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('Add User') ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('username');

            echo $this->Form->control('school_id');
	    echo $this->Form->control('role', [
                'options' => $user->roles
            ]);
            #echo $this->Form->control('verfuegernummer');
            #echo $this->Form->control('verfuegername');
            echo $this->Form->control('password', array('value' => $passworddefault));
            echo $this->Form->control('active');

        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
