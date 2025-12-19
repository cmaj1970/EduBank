<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Transaction $transaction
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
		<li><?= $this->Html->link(__('Zurück'), 'javascript:history.back();') ?> </li>
		<?php if($authuser['role'] == 'admin'): ?>
	        <li><?= $this->Html->link(__('Edit Transaction'), ['action' => 'edit', $transaction->id]) ?> </li>
	        <li><?= $this->Form->postLink(__('Delete Transaction'), ['action' => 'delete', $transaction->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transaction->id)]) ?> </li>
	        <li><?= $this->Html->link(__('List Transactions'), ['action' => 'index']) ?> </li>
	        <li><?= $this->Html->link(__('New Transaction'), ['action' => 'add']) ?> </li>
	        <li><?= $this->Html->link(__('List Accounts'), ['controller' => 'Accounts', 'action' => 'index']) ?> </li>
	        <li><?= $this->Html->link(__('New Account'), ['controller' => 'Accounts', 'action' => 'add']) ?> </li>
		<?php endif; ?>
    </ul>
</nav>
<div class="transactions view large-9 medium-8 columns content">
    <h3>Details Überweisung</h3>
    <table class="vertical-table">
        <?php if($authuser['role'] == 'admin'): ?>
			<tr>
	            <th scope="row"><?= __('Account') ?></th>
	            <td><?= $transaction->has('account') ? $this->Html->link($transaction->account->name, ['controller' => 'Accounts', 'action' => 'view', $transaction->account->id]) : '' ?></td>
	        </tr>
		<?php endif; ?>
        <tr>
            <th scope="row"><?= __('Empfaenger Name') ?></th>
            <td><?= h($transaction->empfaenger_name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Empfaenger Adresse') ?></th>
            <td><?= h($transaction->empfaenger_adresse) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Empfaenger Iban') ?></th>
            <td><?= h($transaction->empfaenger_iban) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Empfaenger Bic') ?></th>
            <td><?= h($transaction->empfaenger_bic) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Betrag') ?></th>
            <td>EUR <?= $this->Number->format($transaction->betrag) ?></td>
        </tr>
		<tr>
		    <th scope="row"><?= __('Zahlungszweck') ?></th>
		    <td><?= $this->Text->autoParagraph(h($transaction->zahlungszweck)); ?></td>
		</tr>
        <tr>
            <th scope="row"><?= __('Datum') ?></th>
            <td><?= h($transaction->datum) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($transaction->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($transaction->modified) ?></td>
        </tr>
    </table>
</div>
