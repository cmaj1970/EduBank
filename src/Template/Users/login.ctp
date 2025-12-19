<div class="users form" style="max-width: 500px; margin: 2rem auto;">
<?= $this->Flash->render() ?>
<?= $this->Form->create() ?>
    <fieldset>
        <legend><?= __('Bitte geben Sie den Benutzernamen und das Passwort ein.') ?></legend>
        <?= $this->Form->control('username') ?>
        <?= $this->Form->control('password') ?>
    </fieldset>
<?= $this->Form->button(__('Anmelden')); ?>
<?= $this->Form->end() ?>
</div>
