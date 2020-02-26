<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Projects Controller
 *
 * @property \App\Model\Table\ProjectsTable $Projects
 *
 * @method \App\Model\Entity\Project[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProjectsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index($state = null)
    {
        $this->paginate = [
            'contain' => ['Clients']
        ];
        if (is_null($state)) {
            $projects = $this->paginate($this->Projects);
        } else {
            $q = $this->Projects
                ->find('all')
                ->where(['state' => $state]);
            $projects = $this->paginate($q);
        }

        $this->set(compact('projects'));
    }

    /**
     * View method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $project = $this->Projects->get($id, [
            'contain' => ['Clients', 'Tasks', 'Times']
        ]);

        $this->set('project', $project);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $project = $this->Projects->newEntity();
        if ($this->request->is('post')) {
            $post = $this->request->getData();
            if (!empty($post['tasks'])) {
//                $taskList = explode(PHP_EOL, $post['tasks']);
                $starterSet = collection(explode(PHP_EOL, $post['tasks']));
                $tasks = $starterSet->map(function($value) {
                    list($name, $note) = explode('::', $value);
                    return ['name' => $name, 'note' => trim($note), 'state' => 'active'];
                })
                ->toArray();
                $post['tasks'] = $tasks;
            }
            $project = $this->Projects->patchEntity($project, $post);
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The project could not be saved. Please, try again.'));
        }
        $tasks = [
            'Upgrade::Fix deprecations and language changes',
            'Migrations::db creation/modification',
            'Project Management::Repo management, Project Cards, Issue review, etc',
            'Planning/Analysis::UMLs and planning work',
            'Dev::write code',
            'Test::Write and run tests',
            'Study/R&D::learn new stuff',
            'Documentation::Document the code and system'
        ];
        $clients = $this->Projects->Clients->find('list', ['limit' => 200]);
        $this->set(compact('project', 'clients', 'tasks'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $project = $this->Projects->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $project = $this->Projects->patchEntity($project, $this->request->getData());
            if ($this->Projects->save($project)) {
                $this->Flash->success(__('The project has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The project could not be saved. Please, try again.'));
        }
        $clients = $this->Projects->Clients->find('list', ['limit' => 200]);
        $this->set(compact('project', 'clients'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Project id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $project = $this->Projects->get($id);
        if ($this->Projects->delete($project)) {
            $this->Flash->success(__('The project has been deleted.'));
        } else {
            $this->Flash->error(__('The project could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
