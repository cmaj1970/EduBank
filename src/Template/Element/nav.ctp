<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">

        <?php if($this->request->action == 'edit') : ?>
            <li><?= $this->Html->link(__('List ' . ucfirst($this->request->controller)), ['action' => 'index']) ?></li>
            <li><?= $this->Form->postLink(
                    __('Delete'),
                    ['action' => 'delete', $account->id],
                    ['confirm' => __('Are you sure you want to delete # {0}?', $account->id)]
                )
                ?></li>
        <?php elseif($this->request->action == 'view') : ?>
            <li><?= $this->Html->link(__('List ' . ucfirst($this->request->controller)), ['action' => 'index']) ?></li>
        <?php elseif($this->request->action == 'add') : ?>
            <li><?= $this->Html->link(__('List ' . ucfirst($this->request->controller)), ['action' => 'index']) ?></li>
        <?php elseif($this->request->action == 'index') : ?>
        <?php if($authuser['role'] == 'admin' && !isset($loggedinschool)): ?>
            <li><?= $this->Html->link(__('List Schools'), ['controller' => 'Schools','action' => 'index']) ?></li>
        <?php endif; ?>
            <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>

            <li><?= $this->Html->link(__('List Accounts'), ['controller' => 'Accounts','action' => 'index']) ?></li>
            <li><strong><?= $this->Html->link(__('Add ' . ucfirst(\Cake\Utility\Inflector::singularize($this->request->controller))), ['action' => 'add']) ?></strong></li>

        <?php else: ?>
            <li><?= $this->Html->link(__('List Schools'), ['controller' => 'Schools','action' => 'index']) ?></li>
            <li><?= $this->Html->link(__('New School'), ['controller' => 'Schools','action' => 'add']) ?></li>
            <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
            <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>

            <?php if($authuser['role'] == 'admin'): ?>
                <li><?= $this->Html->link(__('New Account'), ['action' => 'add']) ?></li>
            <?php endif; ?>

            <li><?= $this->Html->link(__('List Transactions'), ['controller' => 'Transactions', 'action' => 'index']) ?></li>
            <li><?= $this->Html->link(__('New Transaction'), ['controller' => 'Transactions', 'action' => 'add']) ?></li>

        <?php endif; ?>
    </ul>
</nav>
