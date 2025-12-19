<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\School $school
 */
?>
<?= $this->element('nav'); ?>
<div class="schools view large-9 medium-8 columns content">
    <h3><?= h($school->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Name') ?></th>
            <td><?= h($school->name) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Kurzname') ?></th>
            <td><?= h($school->kurzname) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Ibanprefix') ?></th>
            <td><?= h($school->ibanprefix) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Bic') ?></th>
            <td><?= h($school->bic) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($school->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($school->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($school->modified) ?></td>
        </tr>
    </table>

</div>
