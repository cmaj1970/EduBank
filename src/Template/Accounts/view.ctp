<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Account $account
 */
?>
<nav class="large-3 medium-4 columns hide-for-print" id="actions-sidebar">
    <ul class="side-nav">
        <?php if ($authuser['role'] == 'admin'): ?>
            <li><?= $this->Html->link(__('List Accounts'), ['action' => 'index']) ?> </li>
            <li><?= $this->Form->postLink(__('Delete Account'), ['action' => 'delete', $account->id], ['confirm' => __('Are you sure you want to delete # {0}?', $account->id)]) ?> </li>
            <li><strong><?= $this->Html->link(__('Edit Account'), ['action' => 'edit', $account->id]) ?></strong></li>
        <?php else: ?>
            <li><?= $this->Html->link(__('Umsätze'), ['action' => 'view', $account->id]) ?> </li>
            <li><?= $this->Html->link(__('Auftragshistorie'), ['action' => 'history', $account->id]) ?> </li>
            <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add', $account->id]) ?> </li>
        <?php endif; ?>


    </ul>
</nav>
<div class="accounts view large-9 medium-8 columns content">
    <h3><?= h($account->name) ?></h3>
    <table class="vertical-table" style="max-width: 900px">
        <?php if ($authuser['role'] == 'admin'): ?>
            <tr>
                <th scope="row"><?= __('User') ?></th>
                <td><?= $account->has('user') ? $this->Html->link($account->user->name, ['controller' => 'Users', 'action' => 'view', $account->user->id]) : '' ?></td>
            </tr>
        <?php endif; ?>
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($account->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Iban') ?></th>
            <td><?= h($account->iban) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Bic') ?></th>
            <td><?= h($account->bic) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Maxlimit') ?></th>
            <td><?= $this->Number->currency($account->maxlimit, 'EUR', ['useIntlCode' => true]) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Balance') ?></th>
            <td><?= $this->Number->currency($account->balance, 'EUR', ['useIntlCode' => true]) ?></td>
        </tr>
    </table>

    <div class="related">
        <h4><?= __('Umsätze') ?></h4>
        <?php if (!empty($account->transactions->toArray())): ?>
            <table cellpadding="0" cellspacing="0" style="width: 100%; max-width: 900px;">
                <tr>
                    <td colspan="2"></td>
                <tr>
                    <?php foreach ($account->transactions as $transactions): ?>
                <tr>
                    <?php if ($transactions->account->id != $account->id) : # wenn die Überweisung von einem anderen Konto kommt  ?>
                <td>
                <strong><?= h($transactions->account->user->name) ?></strong> <?= h($transactions->account->user->adresse) ?>
                <br/>
                <strong>IBAN:</strong> <?= h($transactions->account->iban) ?> |
                <strong>BIC:</strong> <?= h($transactions->account->bic) ?><br/>
                <?php else : ?>
                    <td>
                        <strong><?= h($transactions->empfaenger_name) ?></strong> <?= h($transactions->empfaenger_adresse) ?>
                        <br/>
                        <strong>IBAN:</strong> <?= h($transactions->empfaenger_iban) ?>
                        <?php if ($transactions->empfaenger_bic): ?> |
                            <strong>BIC:</strong> <?= h($transactions->empfaenger_bic) ?><?php endif; ?>
                        <br/>
                        <?php endif; ?>
                        <strong>Verwendungszweck:</strong> <?= h($transactions->zahlungszweck) ?><br/>
                        <i><?= h($transactions->datum) ?></i>
                    </td>
                    <td style="float:right; padding-top: 20px;">

                        <?php if ($transactions->account_id == $account->id) : ?>
                            <span style="color: red;"><span
                                        style="font-size: 26px">-<?= $this->Number->format($transactions->betrag, ['places' => 2]); ?></span> EUR</span>
                        <?php else : ?>
                            <span style="color: green;"><span
                                        style="font-size: 26px"><?= $this->Number->format($transactions->betrag, ['places' => 2]); ?></span> EUR</span>
                        <?php endif; ?>
                        </span>

                    </td>
                    <?php if ($authuser['role'] == 'admin'): ?>
                        <td class="actions">
                            <?= $this->Html->link(__('View'), ['controller' => 'Transactions', 'action' => 'view', $transactions->id]) ?>

                            <?= $this->Html->link(__('Edit'), ['controller' => 'Transactions', 'action' => 'edit', $transactions->id]) ?>
                            <?= $this->Form->postLink(__('Delete'), ['controller' => 'Transactions', 'action' => 'delete', $transactions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transactions->id)]) ?>

                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
            <a class="button hide-for-print" href="#" onclick="window.print();return false;">Drucken</a>

        <?php else: ?>
            Es sind noch keine Umsätze vorhanden
        <?php endif; ?>
    </div>
</div>
