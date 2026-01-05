<?php
/**
 * Pending verification page
 * Shown to logged-in admins whose school is not yet verified
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 */
?>

<div class="row justify-content-center py-5">
    <div class="col-lg-5 col-md-8">
        <!-- Header -->
        <div class="text-center mb-4">
            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class="bi bi-envelope-exclamation text-warning" style="font-size: 2.5rem;"></i>
            </div>
            <h2 class="mt-2 mb-1">E-Mail-Bestätigung erforderlich</h2>
            <p class="text-muted">Ihre Schule ist noch nicht freigeschaltet.</p>
        </div>

        <!-- Info Card -->
        <div class="card border-0 shadow mb-4">
            <div class="card-body p-4">
                <h5 class="card-title mb-3"><i class="bi bi-building me-2"></i><?= h($school->name) ?></h5>

                <div class="alert alert-warning mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Wichtig:</strong> Um EduBank nutzen zu können, müssen Sie zuerst Ihre E-Mail-Adresse bestätigen.
                </div>

                <p class="mb-3">
                    Wir haben eine E-Mail mit einem Bestätigungslink an
                    <strong><?= h($school->contact_email) ?></strong> gesendet.
                </p>

                <ol class="mb-4 ps-3">
                    <li>Öffnen Sie Ihr E-Mail-Postfach</li>
                    <li>Suchen Sie nach einer E-Mail von EduBank</li>
                    <li>Klicken Sie auf den grünen Button "E-Mail bestätigen"</li>
                    <li>Nach der Bestätigung können Sie sich erneut anmelden</li>
                </ol>

                <div class="bg-light rounded p-3 mb-4">
                    <p class="small text-muted mb-2">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Tipp:</strong> Prüfen Sie auch Ihren Spam-/Junk-Ordner, falls Sie keine E-Mail erhalten haben.
                    </p>
                </div>

                <hr>

                <!-- Resend Email -->
                <p class="text-muted small mb-2">E-Mail nicht erhalten?</p>
                <?= $this->Form->create(null, ['url' => ['action' => 'resendVerification']]) ?>
                <div class="d-grid gap-2">
                    <?= $this->Form->button(__('Bestätigungs-E-Mail erneut senden'), [
                        'class' => 'btn btn-outline-primary'
                    ]) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>

        <!-- Logout -->
        <div class="text-center">
            <a href="/users/logout" class="btn btn-link text-muted">
                <i class="bi bi-box-arrow-left me-1"></i>Abmelden
            </a>
        </div>
    </div>
</div>
