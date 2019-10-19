<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Time Entity
 *
 * @property int $id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $user_id
 * @property int|null $project_id
 * @property \Cake\I18n\FrozenTime $time_in
 * @property \Cake\I18n\FrozenTime $time_out
 * @property string|null $activity
 * @property int|null $status
 * @property int|null $task_id
 * @property int|null $os_billing_status
 * @property int|null $customer_billing_statusCopy
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Project $project
 * @property \App\Model\Entity\Task $task
 */
class Time extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'created' => true,
        'modified' => true,
        'user_id' => true,
        'project_id' => true,
        'time_in' => true,
        'time_out' => true,
        'activity' => true,
        'status' => true,
        'task_id' => true,
        'os_billing_status' => true,
        'customer_billing_statusCopy' => true,
        'user' => true,
        'project' => true,
        'task' => true
    ];

    public function duration()
    {
        return $this->time_out->timestamp - $this->time_in->timestamp;
    }

    public function userId()
    {
        return $this->user_id;
    }

    public function taskId()
    {
        return $this->task_id;
    }

    public function projectId()
    {
        return $this->project_id;
    }
}
