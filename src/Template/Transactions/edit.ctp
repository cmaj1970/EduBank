<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i><?= __('Transaktion bearbeiten') ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <?= $this->Form->create($transaction) ?>

                <div class="mb-3">
                    <label for="account-id" class="form-label"><?= __('Konto') ?></label>
                    <?= $this->Form->select('account_id', $accounts, [
                        'class' => 'form-select',
                        'id' => 'account-id',
                        'empty' => '-- Konto wählen --'
                    ]) ?>
                </div>

                <fieldset class="mb-4">
                    <legend class="h6 text-muted"><?= __('Empfänger') ?></legend>

                    <div class="mb-3">
                        <label for="empfaenger-name" class="form-label"><?= __('Name') ?></label>
                        <?= $this->Form->text('empfaenger_name', [
                            'class' => 'form-control',
                            'id' => 'empfaenger-name'
                        ]) ?>
                    </div>

                    <div class="mb-3">
                        <label for="empfaenger-adresse" class="form-label"><?= __('Adresse') ?></label>
                        <?= $this->Form->text('empfaenger_adresse', [
                            'class' => 'form-control',
                            'id' => 'empfaenger-adresse'
                        ]) ?>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="empfaenger-iban" class="form-label"><?= __('IBAN') ?></label>
                            <?= $this->Form->text('empfaenger_iban', [
                                'class' => 'form-control',
                                'id' => 'empfaenger-iban'
                            ]) ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="empfaenger-bic" class="form-label"><?= __('BIC') ?></label>
                            <?= $this->Form->text('empfaenger_bic', [
                                'class' => 'form-control',
                                'id' => 'empfaenger-bic'
                            ]) ?>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="mb-4">
                    <legend class="h6 text-muted"><?= __('Zahlungsdetails') ?></legend>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="betrag" class="form-label"><?= __('Betrag') ?></label>
                            <div class="input-group">
                                <?= $this->Form->text('betrag', [
                                    'class' => 'form-control',
                                    'id' => 'betrag'
                                ]) ?>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="datum" class="form-label"><?= __('Ausführungsdatum') ?></label>
                            <?= $this->Form->date('datum', [
                                'class' => 'form-control',
                                'id' => 'datum'
                            ]) ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="zahlungszweck" class="form-label"><?= __('Verwendungszweck') ?></label>
                        <?= $this->Form->text('zahlungszweck', [
                            'class' => 'form-control',
                            'id' => 'zahlungszweck'
                        ]) ?>
                    </div>
                </fieldset>

                <div class="d-flex gap-2 justify-content-end">
                    <?= $this->Html->link(__('Abbrechen'), ['action' => 'index'], ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->button(__('Speichern'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
