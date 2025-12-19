<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i><?= __('Schule bearbeiten') ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <?= $this->Form->create($school) ?>

                <div class="mb-3">
                    <label for="name" class="form-label"><?= __('Schulname') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name',
                        'required' => true
                    ]) ?>
                </div>

                <div class="mb-3">
                    <label for="kurzname" class="form-label"><?= __('Kurzname') ?></label>
                    <?= $this->Form->text('kurzname', [
                        'class' => 'form-control bg-light',
                        'id' => 'kurzname',
                        'readonly' => true
                    ]) ?>
                    <div class="form-text text-warning">
                        <i class="bi bi-lock me-1"></i>Der Kurzname kann nicht geändert werden, da er für Benutzernamen verwendet wird.
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ibanprefix" class="form-label"><?= __('IBAN-Prefix') ?></label>
                        <?= $this->Form->text('ibanprefix', [
                            'class' => 'form-control bg-light',
                            'id' => 'ibanprefix',
                            'readonly' => true
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bic" class="form-label"><?= __('BIC') ?></label>
                        <?= $this->Form->text('bic', [
                            'class' => 'form-control bg-light',
                            'id' => 'bic',
                            'readonly' => true
                        ]) ?>
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
