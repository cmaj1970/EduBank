<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i><?= __('Konto bearbeiten') ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <?= $this->Form->create($account) ?>
                <?= $this->Form->hidden('user_id') ?>

                <div class="mb-3">
                    <label for="name" class="form-label"><?= __('Kontoname') ?></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name'
                    ]) ?>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="iban" class="form-label"><?= __('IBAN') ?></label>
                        <?= $this->Form->text('iban', [
                            'class' => 'form-control bg-light',
                            'id' => 'iban',
                            'readonly' => true
                        ]) ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="bic" class="form-label"><?= __('BIC') ?></label>
                        <?= $this->Form->text('bic', [
                            'class' => 'form-control bg-light',
                            'id' => 'bic',
                            'readonly' => true
                        ]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="balance" class="form-label"><?= __('Kontostand') ?></label>
                        <div class="input-group">
                            <?= $this->Form->text('balance', [
                                'class' => 'form-control',
                                'id' => 'balance'
                            ]) ?>
                            <span class="input-group-text">€</span>
                        </div>
                        <div class="form-text text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>Änderung beeinflusst den berechneten Kontostand!
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="maxlimit" class="form-label"><?= __('Überweisungslimit') ?></label>
                        <div class="input-group">
                            <?= $this->Form->text('maxlimit', [
                                'class' => 'form-control',
                                'id' => 'maxlimit'
                            ]) ?>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <?= $this->Html->link(__('Abbrechen'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->button(__('Speichern'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
