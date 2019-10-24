<?php
/*
 * Establish variables for the output
 */
$tasks = $taskGroups[$record->project_id] ?? [];
$recordId = $record->id;
$duration = $this->Html->tag("span", $record->duration('h'), array(
    'id' => $recordId . 'duration',
    'class' => "toggle {$recordId}duration"));
$duration .= $this->Form->control("$index.Time.duration", [
    'value' => $record->duration('h'),
    'class' => $recordId . 'duration hide',
    'label' => FALSE,
    'bind' => 'change.saveField blur.hideDurationInput',
    'fieldName' => 'duration',
    'index' => $recordId,
    'id' => $this->Tk->id('Duration', $record),
    'div' => [
        'id' => "durdiv$recordId"
    ]]);
$rowAttr = ['id' => 'row_' . $recordId];
switch ($record->status) {
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

/*
 * Output a row of table cells for one time record
 */

$html = $this->Html->tableCells(
    [
        [
            $users[$record->user_id],

            $this->Form->control("$index.id", [
                'value' => $record->id,
                'type' => 'hidden',
                'id' => $this->Tk->id('Id', $record)
            ]) .
            $this->Form->control("$index.time_out", [
                'value' => $record->time_out,
                'type' => 'hidden',
                'id' => $this->Tk->id('TimeOut', $record)
            ]) .
            $this->Form->control("$index.user_id", [
                'value' => $record->user_id,
                'type' => 'hidden',
                'id' => $this->Tk->id('UserId', $record)
            ]) .
            $this->Form->control("$index.project_id", [
                'value' => $record->project_id,
                'options' => $projects,
                'label' => FALSE,
                'div' => FALSE,
                'bind' => 'change.saveField',
                'empty' => 'Choose a project',
                'fieldName' => 'project_id',
                'index' => $recordId,
                'id' => $this->Tk->id('ProjectId', $record)
            ]),

            $this->Tk->taskSelect(
                "$index.task_id",
                $record,
                [
                    'label' => FALSE,
                    'div' => FALSE,
                    'tasks' => $tasks ?? [],
                    'id' => $this->Tk->id('TaskId', $record)
                ]),

            $record->time_in->i18nFormat('MM/dd HH:mm', 'America/Los_Angeles'), // outputs '2014-04-20 22:10', '%m.%d - %I:%M %p'), //09.30 9:14

            $duration,

            $this->Form->control("$index.activity", [
                'value' => $record->activity,
                'type' => 'textarea',
                'label' => FALSE,
                'div' => FALSE,
                'bind' => 'change.saveField',
                'fieldName' => 'activity',
                'index' => $recordId,
                'id' => $this->Tk->id('Activity', $record)
            ]),

            $this->Tk->timeFormActionButtons($recordId, $record->status)]

    ], $rowAttr, $rowAttr);

if ($this->request->is('ajax')) {
    $jsonObject = [
        'projects' => $projects,
        'taskGroups' => $taskGroups,
        'id' => $recordId,
        'html' => $html
    ];
    echo json_encode($jsonObject);
} else {
    echo $html;
}
