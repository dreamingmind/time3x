<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Time $time
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Time'), ['action' => 'edit', $time->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Time'), ['action' => 'delete', $time->id], ['confirm' => __('Are you sure you want to delete # {0}?', $time->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Times'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Time'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Projects'), ['controller' => 'Projects', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Project'), ['controller' => 'Projects', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Tasks'), ['controller' => 'Tasks', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Task'), ['controller' => 'Tasks', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="times view large-9 medium-8 columns content">
    <h3><?= h($time->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('User') ?></th>
            <td><?= $time->has('user') ? $this->Html->link($time->user->name, ['controller' => 'Users', 'action' => 'view', $time->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Project') ?></th>
            <td><?= $time->has('project') ? $this->Html->link($time->project->name, ['controller' => 'Projects', 'action' => 'view', $time->project->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Task') ?></th>
            <td><?= $time->has('task') ? $this->Html->link($time->task->name, ['controller' => 'Tasks', 'action' => 'view', $time->task->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($time->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Status') ?></th>
            <td><?= $this->Number->format($time->status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Os Billing Status') ?></th>
            <td><?= $this->Number->format($time->os_billing_status) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Customer Billing StatusCopy') ?></th>
            <td><?= $this->Number->format($time->customer_billing_statusCopy) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($time->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($time->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Time In') ?></th>
            <td><?= h($time->time_in) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Time Out') ?></th>
            <td><?= h($time->time_out) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Activity') ?></h4>
        <?= $this->Text->autoParagraph(h($time->activity)); ?>
    </div>
</div>
