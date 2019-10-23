<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Times Model
 *
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @property \App\Model\Table\ProjectsTable&\Cake\ORM\Association\BelongsTo $Projects
 * @property \App\Model\Table\TasksTable&\Cake\ORM\Association\BelongsTo $Tasks
 *
 * @method \App\Model\Entity\Time get($primaryKey, $options = [])
 * @method \App\Model\Entity\Time newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Time[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Time|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Time saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Time patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Time[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Time findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TimesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('times');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id'
        ]);
        $this->belongsTo('Projects', [
            'foreignKey' => 'project_id'
        ]);
        $this->belongsTo('Tasks', [
            'foreignKey' => 'task_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->dateTime('time_in')
            ->notEmptyDateTime('time_in');

        $validator
            ->dateTime('time_out')
            ->notEmptyDateTime('time_out');

        $validator
            ->scalar('activity')
            ->allowEmptyString('activity');

        $validator
            ->allowEmptyString('status');

        $validator
            ->allowEmptyString('os_billing_status');

        $validator
            ->allowEmptyString('customer_billing_statusCopy');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['project_id'], 'Projects'));
        $rules->add($rules->existsIn(['task_id'], 'Tasks'));

        return $rules;
    }

    public function findOpenRecords(Query $query, $options)
    {
        $days = $options['days'] ?? 1;
        $user_id = $options['user_id'] ?? FALSE;

        if ($user_id) {
            $query = $query->where([
                'user_id' => $user_id
                ]);
        }
        return $query->where([
            'OR' => [
                'status' => OPEN,
                'time_in >' => date('Y:m:d H:i:s', time() - $days * DAY)
            ]
        ]);

//        'conditions' => array(
//        'user_id' => $userId,
//        'OR' => array(
//            'status' => OPEN,
//
//        )

    }
}
