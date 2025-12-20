<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
# Prüfen ob Schul-Admin (username beginnt mit "admin-")
$isSchoolAdminUser = (strpos($user->username, 'admin-') === 0);
# Prüfen ob Superadmin
$isSuperAdmin = ($user->username === 'admin');
# Prüfen ob eingeloggter User ein Schuladmin ist
$isSchoolAdminLoggedIn = isset($loggedinschool);
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i><?= __('Übungsfirma bearbeiten') ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <?= $this->Form->create($user) ?>

                <div class="mb-3">
                    <label for="name" class="form-label"><?= __('Name') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name',
                        'required' => true
                    ]) ?>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label"><?= __('Benutzername') ?></label>
                    <?php if ($isSchoolAdminUser || $isSuperAdmin): ?>
                        <?= $this->Form->text('username', [
                            'class' => 'form-control bg-light',
                            'id' => 'username',
                            'readonly' => true
                        ]) ?>
                        <div class="form-text text-warning">
                            <i class="bi bi-lock me-1"></i>
                            <?php if ($isSuperAdmin): ?>
                                Der Superadmin-Benutzername kann nicht geändert werden.
                            <?php else: ?>
                                Schul-Admin Benutzernamen können nicht geändert werden.
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?= $this->Form->text('username', [
                            'class' => 'form-control',
                            'id' => 'username',
                            'required' => true
                        ]) ?>
                    <?php endif; ?>
                </div>

                <?php if ($isSchoolAdminLoggedIn): ?>
                <!-- Schuladmin: Schule ist fix -->
                <div class="mb-3">
                    <label class="form-label"><?= __('Schule') ?></label>
                    <input type="text" class="form-control bg-light" readonly value="<?= h($loggedinschool['name']) ?>">
                    <?= $this->Form->hidden('school_id', ['value' => $loggedinschool['id']]) ?>
                </div>

                <!-- Schuladmin: Rolle readonly anzeigen -->
                <div class="mb-3">
                    <label class="form-label"><?= __('Rolle') ?></label>
                    <input type="text" class="form-control bg-light" readonly value="<?= $user->role == 'admin' ? 'Admin' : 'Übungsfirma' ?>">
                    <?= $this->Form->hidden('role') ?>
                </div>
                <?php else: ?>
                <!-- Superadmin: Kann Schule und Rolle wählen -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="school-id" class="form-label"><?= __('Schule') ?></label>
                        <?= $this->Form->select('school_id', $schools, [
                            'class' => 'form-select',
                            'id' => 'school-id',
                            'empty' => '-- Keine Schule --'
                        ]) ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label"><?= __('Rolle') ?></label>
                        <?= $this->Form->select('role', ['admin' => 'Admin', 'user' => 'Übungsfirma'], [
                            'class' => 'form-select',
                            'id' => 'role'
                        ]) ?>
                    </div>
                </div>
                <?php endif; ?>

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
