<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Project Entity
 *
 * @property int $id
 * @property int|null $client_id
 * @property string $name
 * @property string|null $note
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property string $state
 *
 * @property \App\Model\Entity\Client $client
 * @property \App\Model\Entity\Task[] $tasks
 * @property \App\Model\Entity\Time[] $times
 */
class Project extends Entity
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
        'client_id' => true,
        'name' => true,
        'note' => true,
        'created' => true,
        'modified' => true,
        'state' => true,
        'client' => true,
        'tasks' => true,
        'times' => true
    ];
}
