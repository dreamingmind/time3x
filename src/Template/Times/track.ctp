<?php
echo "\n" . $this->element('scale_textarea_ui');
echo $this->Form->create($result, ['id' => 'TimeTrackForm']);
echo $this->Html->tag('Table', NULL, array('class' => 'striped tight sortable'));
echo $this->Html->tableHeaders(array('Project', 'Task', 'Time In', 'Duration', 'Activity', 'Tools'), array('class' => 'thead'));
if (!empty($result)) {
    foreach ($result as $index => $record) {
        echo $this->element('track_row', array(
            'projects' => $projects,
            'record' => $record,
            'taskGroup' => $taskGroups,
            'index' => $index
        ));
    }
}
echo '</table>';
echo '</form>';

echo $this->Form->button($this->Html->tag('i', '', array('class' => 'icon-plus-sign')) . ' New', array('class' => 'orange', 'bind' => 'click.newTimeRow'));
echo $this->Tk->nestedList($report, array('class' => 'timereport'));
