<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Time;
use App\Model\Table\TimesTable;
use Cake\I18n\FrozenTime;
use App\Lib\Summaries;

/**
 * Times Controller
 *
 * @property \App\Model\Table\TimesTable $Times
 *
 * @method \App\Model\Entity\Time[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TimesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Users', 'Projects', 'Tasks']
        ];
        $times = $this->paginate($this->Times);

        $this->set(compact('times'));
    }

    /**
     * View method
     *
     * @param string|null $id Time id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $time = $this->Times->get($id, [
            'contain' => ['Users', 'Projects', 'Tasks']
        ]);

        $this->set('time', $time);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $time = $this->Times->newEntity();
        if ($this->request->is('post')) {
            $time = $this->Times->patchEntity($time, $this->request->getData());
            if ($this->Times->save($time)) {
                $this->Flash->success(__('The time has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The time could not be saved. Please, try again.'));
        }
        $users = $this->Times->Users->find('list', ['limit' => 200]);
        $projects = $this->Times->Projects->find('list', ['limit' => 200]);
        $tasks = $this->Times->Tasks->find('list', ['limit' => 200]);
        $this->set(compact('time', 'users', 'projects', 'tasks'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Time id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $time = $this->Times->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $time = $this->Times->patchEntity($time, $this->request->getData());
            if ($this->Times->save($time)) {
                $this->Flash->success(__('The time has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The time could not be saved. Please, try again.'));
        }
        $users = $this->Times->Users->find('list', ['limit' => 200]);
        $projects = $this->Times->Projects->find('list', ['limit' => 200]);
        $tasks = $this->Times->Tasks->find('list', ['limit' => 200]);
        $this->set(compact('time', 'users', 'projects', 'tasks'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Time id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $time = $this->Times->get($id);
        if ($this->Times->delete($time)) {
            $this->Flash->success(__('The time has been deleted.'));
        } else {
            $this->Flash->error(__('The time could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Main ui
     * @todo update
     */
    public function track($days = 1) {
        $result = $this->Times->find(
            'OpenRecords',
            ['user_id' => $this->userId, 'days' => $days])
            ->select(['id', 'time_in', 'time_out', 'activity', 'project_id', 'task_id', 'status', ]);
        $summarizer = new Summaries();
//        debug($summarizer->summarizeProjects($result));
        $report = $summarizer->summarizeUsers($result);

        $this->set(compact('result', 'report'));
        $this->setUiSelects('jobs');
    }

    /**
     * Request for new time record from main ui
     * @todo update
     */
    public function newTimeRow() {
        $this->layout = 'ajax';
        $time = new Time([
            'user_id' => $this->readUser(),
            'time_in' => new FrozenTime(time()),
            'time_out' => new FrozenTime(time()),
            'status' => OPEN
        ]);
        $result = $this->Times->save($time);

        $this->set('userId', $result->user_id);
        $this->set('index', 0);
        $this->set('record', $result);
        $this->setUiSelects('jobs');

        $this->render('/Element/track_row');
    }

    /**
     * Duplicate a recor for a new time record
     * @todo update
     */
    public function duplicateTimeRow($id) {
        $this->layout = 'ajax';
        $this->request->data = $this->Times->find('first', array('conditions' => array('Time.id' => $id)));
        $this->request->data('Time.user_id', $this->Auth->user('id'))
            ->data('Time.id', NULL)
            ->data('Time.time_in', date('Y-m-d H:i:s'))
            ->data('Time.time_out', date('Y-m-d H:i:s'))
            ->data('Time.duration', '00:00')
            ->data('Time.status', OPEN);
        $this->Times->create($this->request->data);
        $result = $this->Times->save($this->request->data);
        $this->request->data = array($result['Time']['id'] => $result);

        $this->set('userId', $result['Time']['user_id']);
        $this->set('index', $result['Time']['id']);
        $this->setUiSelects('jobs');

        $this->render('/Element/track_row');
    }

    /**
     * @todo update
     * @param $id
     */
    public function deleteRow($id) {
        $this->layout = 'ajax';
        $entity = $this->Times->get($id);
        $result = $this->Times->delete($entity);
        $this->set('result', ['result' => $result]);
        $this->render('/Element/json_return');
    }

    /**
     * @todo update
     *
     */
    public function saveField() {
        $result = array();
        $this->layout = 'ajax';
        $this->Times->id = $this->request->data['id'];
        if($this->request->getData('fieldName') == 'duration'){
            $this->saveDuration();
        } elseif ($this->request->getData('fieldname') == 'project_id') {
            $this->validateTask;
        } else {
            $this->saveStandard();
        }
        $entity = new Time($this->request->getData());
        $result = $this->Times->save($entity);
        $timeEntity = $this->Times->get($entity->id);

        $this->set('userId', $timeEntity->user_id);
        $this->set('index', 0);
        $this->set('record', $timeEntity);
        $this->setUiSelects('jobs');

        $this->render('/Element/track_row');
    }

    /**
     * @todo update
     *
     */
    private function saveDuration() {
        $time = explode(':', $this->request->data['value']);
        if (count($time) == 1) {
            $durSeconds = ($time[0] * MINUTE);
        }  else {
            $durSeconds = ($time[0] * HOUR + $time[1] * MINUTE);
        }
        $timeIn = date('Y-m-d H:i:s', time() - $durSeconds);
        $timeOut = date('Y-m-d H:i:s', time());
        $this->request->data= array(
            'id' => $this->request->data['id'],
                'time_in' => new FrozenTime($timeIn),
                'time_out' => new FrozenTime($timeOut)
            );
    }

    /**
     * @todo update
     *
     */
    private function saveStandard() {
        $this->request->data = [
            'id' => $this->request->data['id'],
            $this->request->data['fieldName'] => $this->request->data['value']
        ];
    }

    /**
     * Close a time record
     *
     * Same as pause, but with a different state
     *
     * @todo update
     * @param string $id
     * @param int $state
     */
    public function timeStop($id, $state = CLOSED) {
        $this->layout = 'ajax';
        $time = date('Y-m-d H:i:s');
        if($this->Times->getRecordStatus($id) != PAUSED){
            $this->request->data('Time.time_out', $time);
        }
        $this->request->data('Time.id', $id)
            ->data('Time.status', $state);
        $element = $this->saveTimeChange($id);
        $this->render($element);
    }

    /**
     * Pause a time record
     *
     * @todo update
     * @param string $id
     */
    public function timePause($id) {
        $this->timeStop($id, PAUSED);
    }

    /**
     * Restart a stopped or paused time record
     *
     * @todo update
     * @param string $id
     */
    public function timeRestart($id) {
        $this->layout = 'ajax';
        $duration = $this->Times->field('duration', array('Time.id' => $id));
        $this->request->data('id', $id)
            ->data('value', $duration);
        $this->saveDuration();
        $this->request->data('Time.status', OPEN);
        $element = $this->saveTimeChange($id);
        $this->render($element);
    }

    /**
     * Save time record and choose prepare view based on save result
     *
     * @todo update
     * @param string $id
     * @return string The element to render
     */
    private function saveTimeChange($id) {
        if(!$this->Times->save($this->request->data)){
            $this->Session->setFlash('The record update failed, please try again.');
            $element = '/Elements/ajax_flash';
        } else {
            $this->request->data[$id] = $this->Times->find('first', array('conditions' => array('Time.id' => $id)));
            $this->set('index', $id);
            $this->setUiSelects('jobs');
            $element = '/Elements/track_row';
        }
        return $element;
    }

    /**
     * Set the users, projects and tasks viewVars for UI forms
     *
     * @todo update
     * @param string $type filtering desired for project/task lists
     */
    private function setUiSelects($type = 'all') {
        $users = $this->Times->Users->find('list')->toArray();
        $projects = $this->Times->Projects->selectList($type)->toArray();
        $taskGroups = $this->Times->Tasks->groupedTaskList($type)->toArray();
        $statuses = [1 => 'Open', 'Review', 'Closed', 'Paused'];
            //"OPEN", 1);
            //define("REVIEW", 2);
            //define("CLOSED", 4);
            //define("PAUSED", 8
        $this->set(compact('users', 'projects', 'taskGroups', 'statuses'));

    }


    /**
     *
     * @todo update
     */
    public function search() {
        $times = [];
        if ($this->request->is('post')) {
            $times = $this->Times->search($this->postConditions());
        }
        $projects = $this->Times->Project->find('list');
        $tasks = $this->Times->Task->find('list');
        $this->Times->reindex($times);
        $this->set('report',
            isset($this->Times->reportData['Time'])
                ? $this->Report->summarizeUsers($this->Times->reportData['Time'])
                : array());
        $this->setUiSelects('jobs');
        $this->set(compact('tasks', 'projects', 'times'));
//		$this->render('edit');
    }
}
