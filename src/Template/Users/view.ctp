<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person me-2"></i><?= h($user->name) ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Name') ?></label>
                        <div><strong><?= h($user->name) ?></strong></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Benutzername') ?></label>
                        <div><code><?= h($user->username) ?></code></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Rolle') ?></label>
                        <div>
                            <?php if ($user->role == 'admin'): ?>
                                <span class="badge bg-primary">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Übungsfirma</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Status') ?></label>
                        <div>
                            <?php if ($user->active): ?>
                                <span class="badge bg-success">Aktiv</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inaktiv</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Schule') ?></label>
                        <div>
                            <?php if ($user->school_id): ?>
                                <?= $user->has('school') ? h($user->school->name) : 'ID: ' . $user->school_id ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Erstellt am') ?></label>
                        <div><?= h($user->created->format('d.m.Y H:i')) ?></div>
                    </div>
                </div>

                <?php if (!empty($user->accounts)): ?>
                <hr>
                <h6 class="text-muted mb-3"><i class="bi bi-wallet2 me-2"></i><?= __('Konten') ?></h6>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th><?= __('Kontoname') ?></th>
                                <th><?= __('IBAN') ?></th>
                                <th class="text-end"><?= __('Kontostand') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($user->accounts as $account): ?>
                            <tr>
                                <td>
                                    <?= $this->Html->link(h($account->name), ['controller' => 'Accounts', 'action' => 'view', $account->id]) ?>
                                </td>
                                <td>
                                    <code><?= h($account->iban) ?></code>
                                    <button type="button" class="btn btn-sm btn-link p-0 ms-1 copy-iban" data-iban="<?= h($account->iban) ?>" title="IBAN kopieren">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </td>
                                <td class="text-end">
                                    <?php $balanceClass = $account->balance >= 0 ? 'text-success' : 'text-danger'; ?>
                                    <span class="<?= $balanceClass ?>"><?= $this->Number->currency($account->balance, 'EUR') ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <div class="card-footer bg-transparent">
                <div class="d-flex gap-2">
                    <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Bearbeiten', ['action' => 'edit', $user->id], ['class' => 'btn btn-outline-primary', 'escape' => false]) ?>
                    <?= $this->Form->postLink('<i class="bi bi-trash me-1"></i>Löschen', ['action' => 'delete', $user->id], ['class' => 'btn btn-outline-danger', 'escape' => false, 'confirm' => __('Übungsfirma "{0}" wirklich löschen?', $user->name)]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.copy-iban').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var iban = this.getAttribute('data-iban');
            navigator.clipboard.writeText(iban).then(function() {
                btn.innerHTML = '<i class="bi bi-check text-success"></i>';
                setTimeout(function() {
                    btn.innerHTML = '<i class="bi bi-clipboard"></i>';
                }, 2000);
            });
        });
    });
});
</script>
