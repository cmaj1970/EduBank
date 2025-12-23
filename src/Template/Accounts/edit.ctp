<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */

# Zurück-URL: Wenn redirect_user_id gesetzt, zur Übungsfirma zurück
$redirectUserId = $this->request->getQuery('redirect_user_id');
$backUrl = $redirectUserId ? ['controller' => 'Users', 'action' => 'view', $redirectUserId] : ['action' => 'index'];
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i><?= __('Konto bearbeiten') ?></h5>
            </div>
            <div class="card-body">
                <?= $this->Form->create($account) ?>
                <?= $this->Form->hidden('user_id') ?>

                <div class="mb-3" <?= $this->HelpText->attr('konto_edit', 'name') ?>>
                    <label for="name" class="form-label"><?= __('Kontoname') ?></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name'
                    ]) ?>
                </div>

                <div class="row" <?= $this->HelpText->attr('konto_edit', 'iban') ?>>
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
                    <div class="col-md-6 mb-3" <?= $this->HelpText->attr('konto_edit', 'balance') ?>>
                        <label for="balance" class="form-label"><?= __('Kontostand') ?></label>
                        <div class="input-group">
                            <?php if ($transactionCount > 0): ?>
                            <?= $this->Form->text('balance', [
                                'class' => 'form-control bg-light',
                                'id' => 'balance',
                                'disabled' => true
                            ]) ?>
                            <?php else: ?>
                            <?= $this->Form->text('balance', [
                                'class' => 'form-control',
                                'id' => 'balance'
                            ]) ?>
                            <?php endif; ?>
                            <span class="input-group-text">€</span>
                        </div>
                        <?php if ($transactionCount > 0): ?>
                        <div class="form-text text-muted">
                            <i class="bi bi-lock me-1"></i>Kontostand wird aus Transaktionen berechnet
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3" <?= $this->HelpText->attr('konto_edit', 'maxlimit') ?>>
                        <label for="maxlimit" class="form-label"><?= __('Überziehungsrahmen') ?></label>
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
                    <?= $this->Html->link(__('Abbrechen'), $backUrl, ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->button(__('Speichern'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>

        <!-- Konto zurücksetzen -->
        <div class="card border-warning" <?= $this->HelpText->attr('konto_edit', 'reset') ?>>
            <div class="card-header bg-warning bg-opacity-10">
                <h6 class="mb-0"><i class="bi bi-arrow-counterclockwise me-2"></i><?= __('Konto zurücksetzen') ?></h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Alle Transaktionen werden gelöscht und der Kontostand auf 10.000 € zurückgesetzt.
                </p>
                <?php $resetUrl = ['action' => 'reset', $account->id]; if ($redirectUserId) { $resetUrl['?'] = ['redirect_user_id' => $redirectUserId]; } ?>
                <div class="d-flex gap-2 flex-wrap">
                    <span <?= $this->HelpText->attr('konto_edit', 'leeren') ?>><?= $this->Form->create(null, ['url' => $resetUrl]) ?>
                    <?= $this->Form->hidden('prefill', ['value' => '0']) ?>
                    <?= $this->Form->button(
                        '<i class="bi bi-eraser me-1"></i> Leeren',
                        [
                            'class' => 'btn btn-outline-warning',
                            'escape' => false,
                            'confirm' => 'Alle Transaktionen löschen und Konto auf Startwerte zurücksetzen?'
                        ]
                    ) ?>
                    <?= $this->Form->end() ?></span>

                    <span <?= $this->HelpText->attr('konto_edit', 'prefill') ?>><?= $this->Form->create(null, ['url' => $resetUrl]) ?>
                    <?= $this->Form->hidden('prefill', ['value' => '1']) ?>
                    <?= $this->Form->button(
                        '<i class="bi bi-shuffle me-1"></i> Mit Beispieldaten befüllen',
                        [
                            'class' => 'btn btn-outline-primary',
                            'escape' => false,
                            'confirm' => 'Alle Transaktionen löschen und mit neuen Beispieldaten befüllen?'
                        ]
                    ) ?>
                    <?= $this->Form->end() ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
