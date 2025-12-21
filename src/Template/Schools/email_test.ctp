<?php
/**
 * Email Test Page for Superadmin
 * Tests SMTP configuration and sends test emails
 */
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="bi bi-envelope-check me-2"></i>E-Mail Konfiguration testen</h1>
                <?= $this->Html->link(
                    '<i class="bi bi-arrow-left me-1"></i>Zurück',
                    ['action' => 'index'],
                    ['class' => 'btn btn-outline-secondary', 'escape' => false]
                ) ?>
            </div>

            <!-- Configuration Overview -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-gear me-2"></i>Aktuelle Konfiguration
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">Aktiver Transport</th>
                                <td>
                                    <?php if (strpos($transportType, 'SMTP') !== false): ?>
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>SMTP</span>
                                        <span class="text-muted ms-2"><?= h($transportType) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>PHP mail()</span>
                                        <small class="text-muted ms-2">Empfohlen: SMTP konfigurieren</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>EMAIL_FROM</th>
                                <td><code><?= h($config['EMAIL_FROM']) ?></code></td>
                            </tr>
                            <tr>
                                <th>SMTP_HOST</th>
                                <td>
                                    <?php if ($config['SMTP_HOST']): ?>
                                        <code><?= h($config['SMTP_HOST']) ?></code>
                                    <?php else: ?>
                                        <span class="text-muted">(nicht gesetzt)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>SMTP_PORT</th>
                                <td><code><?= h($config['SMTP_PORT']) ?></code></td>
                            </tr>
                            <tr>
                                <th>SMTP_USER</th>
                                <td>
                                    <?php if ($config['SMTP_USER']): ?>
                                        <span class="text-success"><i class="bi bi-check me-1"></i><?= h($config['SMTP_USER']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">(nicht gesetzt)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>SMTP_PASS</th>
                                <td>
                                    <?php if ($config['SMTP_PASS']): ?>
                                        <span class="text-success"><i class="bi bi-check me-1"></i><?= h($config['SMTP_PASS']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">(nicht gesetzt)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>SMTP_TLS</th>
                                <td>
                                    <?php if ($config['SMTP_TLS']): ?>
                                        <span class="badge bg-success">Aktiviert</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Deaktiviert</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Send Test Email -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-send me-2"></i>Test-E-Mail senden
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null, ['class' => 'row g-3']) ?>
                        <div class="col-md-8">
                            <?= $this->Form->control('recipient', [
                                'type' => 'email',
                                'label' => false,
                                'class' => 'form-control',
                                'placeholder' => 'empfaenger@example.com',
                                'required' => true
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send me-1"></i>Test senden
                            </button>
                        </div>
                    <?= $this->Form->end() ?>

                    <?php if (isset($testError) && $testError): ?>
                        <div class="alert alert-danger mt-3 mb-0">
                            <h6 class="alert-heading"><i class="bi bi-x-circle me-1"></i>Fehler beim Senden</h6>
                            <code><?= h($testError) ?></code>

                            <?php if (strpos($testError, 'Connection refused') !== false || strpos($testError, 'Connection timed out') !== false): ?>
                                <hr>
                                <p class="mb-0"><strong>Mögliche Ursachen:</strong></p>
                                <ul class="mb-0">
                                    <li>SMTP-Host nicht erreichbar</li>
                                    <li>Port blockiert (Firewall)</li>
                                    <li>Falscher Host oder Port</li>
                                </ul>
                            <?php elseif (strpos($testError, 'Authentication') !== false || strpos($testError, 'credentials') !== false): ?>
                                <hr>
                                <p class="mb-0"><strong>Mögliche Ursachen:</strong></p>
                                <ul class="mb-0">
                                    <li>Falscher SMTP-Benutzername</li>
                                    <li>Falsches SMTP-Passwort</li>
                                </ul>
                            <?php elseif (strpos($testError, 'certificate') !== false || strpos($testError, 'SSL') !== false): ?>
                                <hr>
                                <p class="mb-0"><strong>Mögliche Ursachen:</strong></p>
                                <ul class="mb-0">
                                    <li>SSL/TLS-Zertifikatproblem</li>
                                    <li>Versuche <code>SMTP_TLS=false</code> in der .env</li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-question-circle me-2"></i>Konfiguration in config/.env
                </div>
                <div class="card-body">
                    <p>Um SMTP zu aktivieren, füge folgende Zeilen in <code>config/.env</code> hinzu:</p>
                    <pre class="bg-dark text-light p-3 rounded"><code># E-Mail-Konfiguration
EMAIL_FROM=noreply@edubank.at

# SMTP-Konfiguration
SMTP_HOST=smtp.dein-provider.at
SMTP_PORT=587
SMTP_USER=dein_benutzer
SMTP_PASS=dein_passwort
SMTP_TLS=true</code></pre>

                    <p class="text-muted mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Wenn <code>SMTP_HOST</code> nicht gesetzt ist, wird PHP <code>mail()</code> verwendet.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
