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
            ['user_id' => $this->userId, 'days' => $days]);
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
        $this->request->data('Time.user_id', $this->Auth->user('id'))
            ->data('Time.time_in', date('Y-m-d H:i:s'))
            ->data('Time.time_out', date('Y-m-d H:i:s'))
            ->data('Time.project_id', NULL)
            ->data('Time.duration', '00:00')
            ->data('Time.status', OPEN);
        $this->Time->create($this->request->data);
        $result = $this->Time->save($this->request->data);
        $this->request->data = array($result['Time']['id'] => $result);

        $this->set('userId', $result['Time']['user_id']);
        $this->set('index', $result['Time']['id']);
        $this->setUiSelects('jobs');

        $this->render('/Elements/track_row');
    }

    /**
     * Duplicate a recor for a new time record
     * @todo update
     */
    public function duplicateTimeRow($id) {
        $this->layout = 'ajax';
        $this->request->data = $this->Time->find('first', array('conditions' => array('Time.id' => $id)));
        $this->request->data('Time.user_id', $this->Auth->user('id'))
            ->data('Time.id', NULL)
            ->data('Time.time_in', date('Y-m-d H:i:s'))
            ->data('Time.time_out', date('Y-m-d H:i:s'))
            ->data('Time.duration', '00:00')
            ->data('Time.status', OPEN);
        $this->Time->create($this->request->data);
        $result = $this->Time->save($this->request->data);
        $this->request->data = array($result['Time']['id'] => $result);

        $this->set('userId', $result['Time']['user_id']);
        $this->set('index', $result['Time']['id']);
        $this->setUiSelects('jobs');

        $this->render('/Elements/track_row');
    }

    /**
     * @todo update
     * @param $id
     */
    public function deleteRow($id) {
        $this->layout = 'ajax';
        $result = $this->Time->delete($id);
        $this->set('result', array('result' => $result));
        $this->render('/Elements/json_return');
    }

    /**
     * @todo update
     *
     */
    public function saveField() {
        $result = array();
        $this->layout = 'ajax';
        $this->Time->id = $this->request->data['id'];
        if($this->request->data['fieldName'] == 'duration'){
            $this->saveDuration();
        } else {
            $this->saveStandard();
        }
        $result['result'] = $this->Time->save($this->request->data);
        $result['duration'] = substr($this->Time->field('Time.duration', array('Time.id' => $this->request->data['Time']['id'])),0,5);
        $this->set('result', $result);
        $this->render('/Elements/json_return');
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
            'Time' => array(
                'id' => $this->request->data['id'],
                'time_in' => $timeIn,
                'time_out' => $timeOut
            )
        );
    }

    /**
     * @todo update
     *
     */
    private function saveStandard() {
        $this->request->data = array(
            'Time' => array(
                'id' => $this->request->data['id'],
                $this->request->data['fieldName'] => $this->request->data['value']
            ));
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
        if($this->Time->getRecordStatus($id) != PAUSED){
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
        $duration = $this->Time->field('duration', array('Time.id' => $id));
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
        if(!$this->Time->save($this->request->data)){
            $this->Session->setFlash('The record update failed, please try again.');
            $element = '/Elements/ajax_flash';
        } else {
            $this->request->data[$id] = $this->Time->find('first', array('conditions' => array('Time.id' => $id)));
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
        $tasks = $this->Times->Tasks->groupedTaskList($type)->toArray();
        $this->set(compact('users', 'projects', 'tasks'));

    }


    /**
     *
     * @todo update
     */
    public function search() {
        $times = [];
        if ($this->request->is('post')) {
            $times = $this->Time->search($this->postConditions());
        }
        $projects = $this->Time->Project->find('list');
        $tasks = $this->Time->Task->find('list');
        $this->Time->reindex($times);
        $this->set('report',
            isset($this->Time->reportData['Time'])
                ? $this->Report->summarizeUsers($this->Time->reportData['Time'])
                : array());
        $this->setUiSelects('jobs');
        $this->set(compact('tasks', 'projects', 'times'));
//		$this->render('edit');
    }
}
