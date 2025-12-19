<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School[]|\Cake\Collection\CollectionInterface $schools
 */
?>
<?= $this->element('nav'); ?>
<div class="schools index large-9 medium-8 columns content">
    <h3><?= __('Schools') ?></h3>
    <div class="table-responsive">
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('kurzname') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schools as $school): ?>
            <tr>
                <td><?= $this->Number->format($school->id) ?></td>
                <td>
                    <?= h($school->name) ?><br />
                    <small style="color: #666;">
                        IBAN: <?= h($school->ibanprefix) ?> | BIC: <?= h($school->bic) ?>
                    </small>
                </td>
                <td><?= h($school->kurzname) ?></td>

                
                <td><?= h($school->created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $school->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $school->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $school->id], ['confirm' => __('Are you sure you want to delete # {0}?', $school->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
