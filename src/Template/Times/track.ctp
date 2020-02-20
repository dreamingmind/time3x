<?php
echo "\n" . $this->element('scale_textarea_ui');
echo $this->Form->create($result, ['id' => 'TimeTrackForm']);
echo $this->Html->tag('Table', NULL, array('class' => 'striped tight sortable'));
echo $this->Html->tableHeaders(array('Name', 'Project', 'Task', 'Time In', 'Duration', 'Activity', 'Tools'), array('class' => 'thead'));
if (!empty($result)) {
    foreach ($result as $index => $record) {
        echo $this->element('track_row', [
            'projects' => $projects,
            'record' => $record,
            'taskGroup' => $taskGroups,
            'index' => $index,
            'users' => $users
        ]);
    }
}
echo '</table>';
echo '</form>';

echo $this->Form->button(
    $this->Html->tag(
        'i',
        '',
        ['class' => 'icon-plus-sign'])
    . ' New', ['class' => 'orange', 'bind' => 'click.newTimeRow']
);

echo $this->Form->create(null, ['id' => 'reveal']);
echo $this->Form->control('all', ['type' => 'hidden', 'value' => true]);
echo $this->Form->button('Show All Users', ['class' => 'blue']);
echo $this->Form->end();

echo '<p> </p>';
debug($report);
