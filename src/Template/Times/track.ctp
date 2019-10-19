<?php
//debug($users);
//debug($projects);
//debug($tasks);
//foreach ($times as $time) {
//    $s = $time->time_in;
//    $e = $time->time_out;
//    echo '<p>' . "{$s->day}-{$s->month}-{$s->year}" . ' to ' . "{$e->day}-{$e->month}-{$e->year}" . ' = ' . $time->duration();
//}
echo "\n" . $this->element('scale_textarea_ui');
echo $this->Form->create('Time');
echo $this->Html->tag('Table', NULL, array('class' => 'striped tight sortable'));
echo $this->Html->tableHeaders(array('Project', 'Task', 'Time In', 'Duration', 'Activity', 'Tools'), array('class' => 'thead'));
if (!empty($result)) {
    foreach ($result as $index => $record) {
        echo $this->element('track_row', array(
            'projects' => $projects,
            'record' => $record
        ));
    }
}
echo '</table>';
echo '</form>';

echo $this->Form->button($this->Html->tag('i', '', array('class' => 'icon-plus-sign')) . ' New', array('class' => 'orange', 'bind' => 'click.newTimeRow'));
echo $this->Tk->nestedList($report, array('class' => 'timereport'));
