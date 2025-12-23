<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 * @var array $users
 * @var \App\Model\Entity\User|null $fixedUser
 * @var int|null $fixedUserId
 */

# Zurück-URL bestimmen
$backUrl = ['action' => 'index'];
if (!empty($fixedUserId)) {
    $backUrl = ['controller' => 'Users', 'action' => 'view', $fixedUserId];
}
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-wallet-plus me-2"></i><?= __('Neues Konto') ?></h5>
            </div>
            <div class="card-body">
                <?php if(!empty($users)): ?>
                <?= $this->Form->create($account) ?>

                <?php if (!empty($fixedUserId)): ?>
                <?= $this->Form->hidden('redirect_user_id', ['value' => $fixedUserId]) ?>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="user-id" class="form-label"><?= __('Übungsfirma') ?> <span class="text-danger">*</span></label>
                    <?php if (!empty($fixedUser)): ?>
                    <div class="form-control bg-light">
                        <i class="bi bi-building me-2 text-primary"></i>
                        <strong><?= h($fixedUser->name) ?></strong>
                    </div>
                    <?= $this->Form->hidden('user_id', ['value' => $fixedUser->id]) ?>
                    <?php else: ?>
                    <?= $this->Form->select('user_id', $users, [
                        'class' => 'form-select',
                        'id' => 'user-id',
                        'empty' => '-- Übungsfirma wählen --',
                        'required' => true
                    ]) ?>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label"><?= __('Kontoname') ?></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name',
                        'placeholder' => 'z.B. Girokonto'
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
                        <div class="form-text">Automatisch generiert</div>
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
                        <label for="balance" class="form-label"><?= __('Startguthaben') ?></label>
                        <div class="input-group">
                            <?= $this->Form->text('balance', [
                                'class' => 'form-control bg-light',
                                'id' => 'balance',
                                'value' => '10000',
                                'readonly' => true
                            ]) ?>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="maxlimit" class="form-label"><?= __('Überziehungsrahmen') ?></label>
                        <div class="input-group">
                            <?= $this->Form->text('maxlimit', [
                                'class' => 'form-control bg-light',
                                'id' => 'maxlimit',
                                'value' => '2000',
                                'readonly' => true
                            ]) ?>
                            <span class="input-group-text">€</span>
                        </div>
                    </div>
                </div>

                <!-- Beispieltransaktionen -->
                <div class="mb-4">
                    <div class="form-check">
                        <?= $this->Form->checkbox('prefill_sample_data', [
                            'class' => 'form-check-input',
                            'id' => 'prefill-sample-data'
                        ]) ?>
                        <label class="form-check-label" for="prefill-sample-data">
                            <i class="bi bi-shuffle me-1"></i><?= __('Mit Beispieltransaktionen befüllen') ?>
                        </label>
                        <div class="form-text">Erstellt ca. 15 zufällige Transaktionen mit Partnerunternehmen</div>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <?= $this->Html->link(__('Abbrechen'), $backUrl, ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->button(__('Konto erstellen'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?= $this->Form->end() ?>

                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Es können keine Konten hinzugefügt werden, da keine Übungsfirmen ohne Konto vorhanden sind.
                </div>
                <div class="d-flex gap-2">
                    <?= $this->Html->link('<i class="bi bi-list"></i> Kontenliste', ['action' => 'index'], ['class' => 'btn btn-outline-primary', 'escape' => false]) ?>
                    <?= $this->Html->link('<i class="bi bi-person-plus"></i> Übungsfirma anlegen', ['controller' => 'Users', 'action' => 'add'], ['class' => 'btn btn-primary', 'escape' => false]) ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
