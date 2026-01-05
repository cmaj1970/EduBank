<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
# Prüfen ob eingeloggter User ein Schuladmin ist
$isSchoolAdminLoggedIn = isset($loggedinschool);

# Zurück-URL: Schuladmin geht zur Detailseite, Superadmin zur Liste
$backUrl = $isSchoolAdminLoggedIn ? ['action' => 'view', $user->id] : ['action' => 'index'];
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i><?= __('Übungsfirma bearbeiten') ?></h5>
            </div>
            <div class="card-body">
                <?= $this->Form->create($user) ?>

                <div class="mb-3" <?= $this->HelpText->attr('firma_edit', 'name') ?>>
                    <label for="name" class="form-label"><?= __('Name der Übungsfirma') ?> <span class="text-danger">*</span></label>
                    <?= $this->Form->text('name', [
                        'class' => 'form-control',
                        'id' => 'name',
                        'placeholder' => 'Name der Übungsfirma',
                        'required' => true
                    ]) ?>
                </div>

                <div class="mb-3" <?= $this->HelpText->attr('firma_edit', 'username') ?>>
                    <label class="form-label"><?= __('Benutzername') ?></label>
                    <input type="text" class="form-control bg-light" readonly value="<?= h($user->username) ?>">
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Der Benutzername wird automatisch generiert und kann nicht geändert werden.</div>
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

                <div class="mb-3" <?= $this->HelpText->attr('firma_edit', 'active') ?>>
                    <div class="form-check form-switch">
                        <?= $this->Form->checkbox('active', [
                            'class' => 'form-check-input',
                            'id' => 'active'
                        ]) ?>
                        <label class="form-check-label" for="active"><?= __('Aktiv') ?></label>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <?= $this->Html->link(__('Abbrechen'), $backUrl, ['class' => 'btn btn-secondary']) ?>
                    <?= $this->Form->button(__('Speichern'), ['class' => 'btn btn-primary']) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
