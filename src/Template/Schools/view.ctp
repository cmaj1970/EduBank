<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 */
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i><?= h($school->name) ?></h5>
                <?= $this->Html->link('<i class="bi bi-arrow-left"></i> Zurück', ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Schulname') ?></label>
                        <div><strong><?= h($school->name) ?></strong></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Kurzname') ?></label>
                        <div><code><?= h($school->kurzname) ?></code></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('IBAN-Prefix') ?></label>
                        <div class="font-monospace"><?= h($school->ibanprefix) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('BIC') ?></label>
                        <div class="font-monospace"><?= h($school->bic) ?></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Erstellt am') ?></label>
                        <div><?= h($school->created->format('d.m.Y H:i')) ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small"><?= __('Schul-Admin') ?></label>
                        <div><code>admin-<?= h($school->kurzname) ?></code></div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-transparent">
                <div class="d-flex gap-2">
                    <?= $this->Html->link('<i class="bi bi-pencil me-1"></i>Bearbeiten', ['action' => 'edit', $school->id], ['class' => 'btn btn-outline-primary', 'escape' => false]) ?>
                    <?= $this->Form->postLink('<i class="bi bi-trash me-1"></i>Löschen', ['action' => 'delete', $school->id], ['class' => 'btn btn-outline-danger', 'escape' => false, 'confirm' => __('ACHTUNG: Schule "{0}" löschen?\n\nDies löscht ALLE zugehörigen:\n- Admins und Übungsfirmen\n- Konten\n- Transaktionen\n\nDieser Vorgang kann nicht rückgängig gemacht werden!', $school->name)]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
