<?php
/**
 * Request credentials form
 * Users can request their login credentials to be sent via email
 */
?>

<div class="row justify-content-center py-4">
    <div class="col-lg-4 col-md-6">
        <!-- Header -->
        <div class="text-center mb-4">
            <i class="bi bi-bank2 text-primary" style="font-size: 3rem;"></i>
            <h2 class="mt-2 mb-1">EduBank</h2>
            <p class="text-muted">Zugangsdaten anfordern</p>
        </div>

        <div class="card border-0 shadow">
            <div class="card-body p-4">
                <p class="text-muted mb-4">
                    Geben Sie Ihre E-Mail-Adresse und den Schulnamen ein.
                    Wir senden Ihnen Ihre Zugangsdaten zu.
                </p>

                <?= $this->Form->create(null, ['id' => 'credentialsform']) ?>

                <div class="mb-3">
                    <label for="email" class="form-label"><?= __('E-Mail-Adresse') ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <?= $this->Form->email('email', [
                            'class' => 'form-control form-control-lg',
                            'id' => 'email',
                            'placeholder' => 'ihre@email.at',
                            'required' => true
                        ]) ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="school_name" class="form-label"><?= __('Schulname') ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <?= $this->Form->text('school_name', [
                            'class' => 'form-control form-control-lg',
                            'id' => 'school_name',
                            'placeholder' => 'z.B. Handelsakademie',
                            'required' => true
                        ]) ?>
                    </div>
                    <div class="form-text">
                        Der Name oder Kurzname Ihrer Schule
                    </div>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <?= $this->Form->button(__('Zugangsdaten anfordern'), [
                        'class' => 'btn btn-primary btn-lg'
                    ]) ?>
                </div>

                <?= $this->Form->end() ?>

                <hr>

                <div class="text-center">
                    <a href="/users/login"><i class="bi bi-arrow-left me-1"></i>Zurück zum Login</a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <span class="text-muted small">Noch keine Schule registriert?</span>
            <a href="/schools/register" class="small ms-1">Jetzt registrieren</a>
        </div>
    </div>
</div>
