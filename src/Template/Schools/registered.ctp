<?php
/**
 * Registration confirmation page
 * Shows credentials and allows email resend
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 * @var string $email
 * @var bool $emailSent
 * @var string $username
 * @var string $password
 */
$resent = $this->request->getQuery('resent') === '1';
?>

<div class="row justify-content-center py-4">
    <div class="col-lg-5 col-md-8">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-check-lg text-success" style="font-size: 2.5rem;"></i>
            </div>
            <h2 class="mt-2 mb-1">Registrierung erfolgreich!</h2>
            <p class="text-muted">Ihre Schule <strong><?= h($school->name) ?></strong> wurde erstellt.</p>
        </div>

        <!-- Email Status -->
        <?php if ($emailSent): ?>
        <div class="alert alert-success d-flex align-items-center mb-4">
            <i class="bi bi-envelope-check fs-4 me-3"></i>
            <div>
                <?php if ($resent): ?>
                    <strong>E-Mail erneut gesendet!</strong><br>
                <?php else: ?>
                    <strong>E-Mail gesendet!</strong><br>
                <?php endif; ?>
                <span class="small">Zugangsdaten wurden an <strong><?= h($email) ?></strong> geschickt.</span>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
            <div>
                <strong>E-Mail konnte nicht gesendet werden</strong><br>
                <span class="small">Bitte notieren Sie sich die Zugangsdaten unten.</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Credentials Card -->
        <div class="card border-0 shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-key me-2"></i>Ihre Zugangsdaten</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4 text-muted">Benutzername:</div>
                    <div class="col-8">
                        <code class="fs-5 user-select-all"><?= h($username) ?></code>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 text-muted">Passwort:</div>
                    <div class="col-8">
                        <code class="fs-5 user-select-all"><?= h($password) ?></code>
                    </div>
                </div>

                <hr>

                <div class="bg-light rounded p-3 small">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    <strong>Tipp:</strong> Notieren Sie sich diese Daten oder machen Sie einen Screenshot.
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-grid gap-2 mb-4">
            <a href="/users/login" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Jetzt anmelden
            </a>
        </div>

        <!-- Resend Email -->
        <?php if ($emailSent): ?>
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-3">
                <p class="small text-muted mb-2">
                    <i class="bi bi-envelope me-1"></i>
                    E-Mail nicht erhalten? Prüfen Sie Ihren Spam-Ordner oder:
                </p>
                <?= $this->Form->create(null, ['url' => ['action' => 'resendEmail']]) ?>
                <?= $this->Form->hidden('school_id', ['value' => $school->id]) ?>
                <?= $this->Form->hidden('email', ['value' => $email]) ?>
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-repeat me-1"></i>E-Mail erneut senden
                </button>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <?php else: ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <p class="small text-muted mb-2">
                    <i class="bi bi-envelope me-1"></i>
                    E-Mail erneut versuchen:
                </p>
                <?= $this->Form->create(null, ['url' => ['action' => 'resendEmail']]) ?>
                <?= $this->Form->hidden('school_id', ['value' => $school->id]) ?>
                <div class="input-group input-group-sm">
                    <?= $this->Form->email('email', [
                        'class' => 'form-control',
                        'value' => $email,
                        'placeholder' => 'E-Mail-Adresse'
                    ]) ?>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Senden
                    </button>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="/" class="text-muted small"><i class="bi bi-arrow-left me-1"></i>Zurück zur Startseite</a>
        </div>
    </div>
</div>
