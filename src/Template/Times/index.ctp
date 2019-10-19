<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Time[]|\Cake\Collection\CollectionInterface $times
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Time'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Tasks'), ['controller' => 'Tasks', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Task'), ['controller' => 'Tasks', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="times index large-9 medium-8 columns content">
    <h3><?= __('Times') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('project_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('time_in') ?></th>
                <th scope="col"><?= $this->Paginator->sort('time_out') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('task_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('os_billing_status') ?></th>
                <th scope="col"><?= $this->Paginator->sort('customer_billing_statusCopy') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($times as $time): ?>
            <tr>
                <td><?= $this->Number->format($time->id) ?></td>
                <td><?= h($time->created) ?></td>
                <td><?= h($time->modified) ?></td>
                <td><?= $time->has('user') ? $this->Html->link($time->user->name, ['controller' => 'Users', 'action' => 'view', $time->user->id]) : '' ?></td>
                <td><?= $time->has('project') ? $this->Html->link($time->project->name, ['controller' => 'Projects', 'action' => 'view', $time->project->id]) : '' ?></td>
                <td><?= h($time->time_in) ?></td>
                <td><?= h($time->time_out) ?></td>
                <td><?= $this->Number->format($time->status) ?></td>
                <td><?= $time->has('task') ? $this->Html->link($time->task->name, ['controller' => 'Tasks', 'action' => 'view', $time->task->id]) : '' ?></td>
                <td><?= $this->Number->format($time->os_billing_status) ?></td>
                <td><?= $this->Number->format($time->customer_billing_statusCopy) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $time->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $time->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $time->id], ['confirm' => __('Are you sure you want to delete # {0}?', $time->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
