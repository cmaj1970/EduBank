<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
# Prüfen ob Schuladmin (dann ist $loggedinschool gesetzt)
$isSchoolAdmin = isset($loggedinschool);
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i><?= __('Neue Übungsfirma') ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <?= $this->Form->create($user) ?>

                <div class="mb-3">
                    <label for="name" class="form-label"><?= __('Name der Übungsfirma') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name',
                        'placeholder' => 'z.B. Musterfirma GmbH',
                        'required' => true
                    ]) ?>
                </div>

                <?php if ($isSchoolAdmin): ?>
                <!-- Schuladmin: Benutzername wird automatisch generiert -->
                <div class="mb-3">
                    <label class="form-label"><?= __('Benutzername') ?></label>
                    <input type="text" class="form-control bg-light" readonly value="<?= h($user->username) ?>">
                    <?= $this->Form->hidden('username', ['value' => $user->username]) ?>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Wird automatisch generiert</div>
                </div>

                <!-- Schuladmin: Schule und Rolle werden automatisch gesetzt (nicht angezeigt) -->
                <?= $this->Form->hidden('school_id', ['value' => $loggedinschool['id']]) ?>
                <?= $this->Form->hidden('role', ['value' => 'user']) ?>

                <!-- Kontoname für automatische Kontoerstellung -->
                <hr class="my-4">
                <h6 class="mb-3"><i class="bi bi-wallet2 me-2"></i><?= __('Bankkonto') ?></h6>
                <div class="mb-3">
                    <label for="account_name" class="form-label"><?= __('Kontoname') ?></label>
                    <?= $this->Form->text('account_name', [
                        'class' => 'form-control',
                        'id' => 'account_name',
                        'placeholder' => 'z.B. Geschäftskonto (optional, wird automatisch aus Firmenname erstellt)'
                    ]) ?>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Ein Konto mit IBAN und BIC wird automatisch angelegt</div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <?= $this->Form->checkbox('prefill_sample_data', [
                            'class' => 'form-check-input',
                            'id' => 'prefillSampleData'
                        ]) ?>
                        <label class="form-check-label" for="prefillSampleData">
                            <i class="bi bi-shuffle me-1"></i><?= __('Mit Beispieltransaktionen befüllen') ?>
                        </label>
                        <div class="form-text">Erstellt ~15 realistische Transaktionen der letzten 3 Monate</div>
                    </div>
                </div>

                <?php else: ?>
                <!-- Superadmin: Kann Schule und Rolle wählen -->
                <div class="mb-3">
                    <label for="username" class="form-label"><?= __('Benutzername') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->text('username', [
                        'class' => 'form-control',
                        'id' => 'username',
                        'required' => true
                    ]) ?>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="school-id" class="form-label"><?= __('Schule') ?></label>
                        <?= $this->Form->select('school_id', $schools, [
                            'class' => 'form-select',
                            'id' => 'school-id',
                            'empty' => '-- Schule wählen --'
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label"><?= __('Rolle') ?> <span class="text-danger">*</span></label>
                        <?= $this->Form->select('role', ['admin' => 'Admin', 'user' => 'Übungsfirma'], [
                            'class' => 'form-select',
                            'id' => 'role',
                            'required' => true
                        ]) ?>
                    </div>
                </div>
                <?php endif; ?>

                <?= $this->Form->hidden('password', ['value' => $passworddefault, 'id' => 'password']) ?>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <?= $this->Form->checkbox('active', [
                            'class' => 'form-check-input',
                            'id' => 'active'
                        ]) ?>
                        <label class="form-check-label" for="active"><?= __('Aktiv') ?></label>
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

<?php if (!$isSchoolAdmin): ?>
<?php $this->start('script'); ?>
<script>
$(document).ready(function() {
    var defaultAdminPassword = <?= json_encode($defaultAdminPassword ?? '') ?>;
    var defaultUserPassword = <?= json_encode($defaultUserPassword ?? '') ?>;

    // Passwort basierend auf Rolle aktualisieren
    $('#role').on('change', function() {
        var role = $(this).val();
        if (role === 'admin') {
            $('#password').val(defaultAdminPassword);
        } else {
            $('#password').val(defaultUserPassword);
        }
    });

    // Initial setzen
    $('#role').trigger('change');
});
</script>
<?php $this->end(); ?>
<?php endif; ?>
