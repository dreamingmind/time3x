<?php
//$task = $this->Tk->task($this->request->data[$index], $tasks);
$index = $record->id;
$duration = $this->Html->tag('span', substr($record->duration,0,5), array(
    'id' => $index.'duration',
    'class' => "toggle {$index}duration"));
$duration .= $this->Form->input("$index.Time.duration", array(
    'class' => $index.'duration hide',
    'label' => FALSE,
    'bind' => 'change.saveField blur.hideDurationInput',
    'fieldName' => 'duration',
    'index' => $index,
    'div' => array(
        'id' => "durdiv$index"
    )));
$rowAttr = array('id' => 'row_'.$index);
switch ($this->request->data("$index.Time.status")) {
    case OPEN:
        $rowAttr['class'] = 'open';
        break;
    case CLOSED:
        $rowAttr['class'] = 'closed';
        break;
    case REVIEW:
        $rowAttr['class'] = 'review';
        break;
    case PAUSED:
        $rowAttr['class'] = 'paused';
        break;
    default:
        break;
}
//dmDebug::ddd($this->request->data('{n}.Time.status'), 'status');

echo $this->Html->tableCells(array(
    array(
        $this->Form->input("$index.Time.id", array('type' => 'hidden')) .
        $this->Form->input("$index.Time.time_out", array('type' => 'hidden')) .
        $this->Form->input("$index.Time.user_id", array('type' => 'hidden')) .
        $this->Form->input("$index.Time.project_id", array(
            'options' => $projects,
            'label' => FALSE,
            'div' => FALSE,
            'bind' => 'change.saveField',
            'empty' => 'Choose a project',
            'fieldName' => 'project_id',
            'index' => $index
        )),
//        . '&nbsp;'
//        . $this->Tk->setProjectDefaultButton($this->request->data[$index]['Time']['project_id']),

//		$this->Form->input("$index.Time.task_id", array(
//			'options' => $task,
//			'label' => FALSE,
//			'div' => FALSE,
//			'empty' => 'Choose a task',
//			'bind' => 'change.taskChoice',
//			'project_id' => $this->request->data[$index]['Time']['project_id']
//		))
		$this->Tk->taskSelect("$index.Time.task_id", $record, ['label' => FALSE, 'div' => FALSE, 'tasks' => $tasks]),

        $this->Time->format($record->time_in, '%m.%d - %I:%M %p'),

        $duration,

        $this->Form->input("$index.Time.activity", array(
            'label' => FALSE,
            'div' => FALSE,
            'bind' => 'change.saveField',
            'fieldName' => 'activity',
            'index' => $index
        )),

        $this->Tk->timeFormActionButtons($index, $record->status))

), $rowAttr, $rowAttr);
