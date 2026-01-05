<div class="row justify-content-center py-5">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-4">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <?= $this->Html->image('logo.svg', ['alt' => 'EduBank Logo', 'style' => 'height: 50px; filter: brightness(0);']) ?>
                </div>

                <h4 class="text-center mb-4">Anmelden</h4>

                <?= $this->Form->create(null, ['class' => 'needs-validation']) ?>

                <div class="mb-3">
                    <label for="username" class="form-label">Benutzername</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <?= $this->Form->text('username', [
                            'class' => 'form-control',
                            'id' => 'username',
                            'placeholder' => 'Benutzername eingeben',
                            'required' => true,
                            'autofocus' => true
                        ]) ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Passwort</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <?= $this->Form->password('password', [
                            'class' => 'form-control',
                            'id' => 'password',
                            'placeholder' => 'Passwort eingeben',
                            'required' => true
                        ]) ?>
                    </div>
                </div>

                <div class="d-grid">
                    <?= $this->Form->button(__('Anmelden'), [
                        'class' => 'btn btn-primary btn-lg',
                        'type' => 'submit'
                    ]) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <span class="text-muted small">Noch keine Schule registriert?</span>
            <a href="/schools/register" class="small ms-1">Jetzt registrieren</a>
        </div>

        <p class="text-center text-muted mt-3 small">
            <i class="bi bi-shield-check me-1"></i>
            Sichere Verbindung
        </p>
    </div>
</div>
