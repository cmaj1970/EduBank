<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<nav class="large-3 medium-4 columns hide-for-print" id="actions-sidebar">
    <ul class="side-nav">
        <?php if($authuser['role'] == 'admin'): ?>
			<li><?= $this->Html->link(__('Edit Account'), ['action' => 'edit', $account->id]) ?> </li>
	        <li><?= $this->Form->postLink(__('Delete Account'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?> </li>

	        <li><?= $this->Html->link(__('New Account'), ['action' => 'add']) ?> </li>
            <li><?= $this->Html->link(__('List Accounts'), ['action' => 'index']) ?> </li>
        <?php endif; ?>

        <li><?= $this->Html->link(__('Umsätze'), ['action' => 'view', $account->id]) ?> </li>
        <li><?= $this->Html->link(__('Auftragshistorie'), ['action' => 'history', $account->id]) ?> </li>
        <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="accounts view large-9 medium-8 columns content">
    <h3>Auftragshistorie <?= h($account->name) ?></h3>

	<?php if (!empty($account->transactions->toArray())): ?>
    <div class="related">

        <table cellpadding="0" cellspacing="0" style="width: 100%; max-width: 900px;">
			<tr>
				<td>Empfänger</td>
                <td>Gesendet am</td>
                <td>Auftragsstatus</td>
                <td>Betrag</td>
			<tr>
            <?php foreach ($account->transactions as $transactions): ?>
            <tr>

                <td><strong><?= h($transactions->empfaenger_name) ?></strong></td>
                <td nowrap="nowrap"><?= h($transactions->created) ?></td>
                <td nowrap="nowrap">
                    <?php if($transactions->datum <= date('d.m.y')): ?>
                        <span style="color: #027c15">Durchgeführt am </span>
                    <?php else: ?>
                        <span style="color: #d77611">Geplant für </span>
                    <?php endif; ?>
                            <?= h($transactions->datum) ?>
                </td>
                <td><?= $this->Number->format($transactions->betrag, ['places' => 2]); ?></span> EUR</span></td>
                <td>
                    <?php if($transactions->datum > date('d.m.y')): ?>
                        <?= $this->Form->postLink(__('Stornieren'), ['controller' => 'Transactions', 'action' => 'storno', $transactions->id], ['confirm' => __('Sind Sie sicher, dass Sie den Auftrag stornieren möchten?', $transactions->id)]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

    </div>
	<?php endif; ?>
    <a class="button hide-for-print" href="#" onclick="window.print();return false;">Drucken</a>
</div>
