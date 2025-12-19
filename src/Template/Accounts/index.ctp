<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account[]|\Cake\Collection\CollectionInterface $accounts
 */
?>
<?= $this->element('nav'); ?>
<div class="accounts index large-9 medium-8 columns content">
    <h3><?= __('Accounts') ?></h3>
    <div class="table-responsive">
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <?php if($authuser['role'] == 'admin'): ?><th scope="col"><?= $this->Paginator->sort('user_id') ?></th><?php endif; ?>
                <th scope="col"  nowrap="nowrap"><?= $this->Paginator->sort('name') ?> | <?= $this->Paginator->sort('iban') ?> | <?= $this->Paginator->sort('bic') ?></th>

                <?php if($authuser['role'] == 'admin'): ?>
					<th scope="col"><?= $this->Paginator->sort('created') ?></th>
					<th scope="col"><?= $this->Paginator->sort('modified') ?></th>
				<?php endif; ?>
                <th scope="col" class="actions"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accounts as $account): ?>
            <tr>
                <?php if($authuser['role'] == 'admin'): ?><td><?= $account->has('user') ? $account->user->name : '' ?></td><?php endif; ?>
                <td nowrap="nowrap"><?= h($account->name) ?><br />IBAN: <?= h($account->iban) ?><br />BIC: <?= h($account->bic) ?><br />
			Kontostand: <?= $this->Number->currency($account->balance, 'EUR', ['useIntlCode' => true]) ?><br />
				Überziehungsrahmen: <?= $this->Number->currency($account->maxlimit, 'EUR', ['useIntlCode' => true]) ?></td>

                <?php if($authuser['role'] == 'admin'): ?>
					<td><?= h($account->created) ?></td>
					<td><?= h($account->modified) ?></td>
				<?php endif; ?>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $account->id]) ?>
					<?php if($authuser['role'] == 'admin'): ?>
                    	<?= $this->Html->link(__('Edit'), ['action' => 'edit', $account->id]) ?>
						<?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?>
                        <?= $this->Form->postLink(__('Zurücksetzen'), ['action' => 'reset', $account->id], ['confirm' => __('Möchten Sie Konto # {0} auf die Standardwerte zurücksetzen und alle Transaktionen löschen?', $account->id)]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('<<')) ?>
            <?= $this->Paginator->prev('< ' . __('<')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('>') . ' >') ?>
            <?= $this->Paginator->last(__('<<') . ' >>') ?>
        </ul>
        <!--<p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>-->
    </div>
</div>
