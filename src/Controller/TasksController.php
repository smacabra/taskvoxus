<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Controller\Configure;
/**
 * Tasks Controller
 *
 * @property \App\Model\Table\TasksTable $Tasks
 *
 * @method \App\Model\Entity\Task[] paginate($object = null, array $settings = [])
 */
class TasksController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Categories']
        ];
        $tasks = $this->paginate($this->Tasks);

        foreach ($tasks as $task) {
            $task['created_by_id'] = $task['created_by'];
            $task['created_by'] = $this->getRelatedUser($task['created_by']);

            if ($task['done_by'] != 0) {
                $task['done_by_id'] = $task['done_by'];
                $task['done_by'] = $this->getRelatedUser($task['done_by']);
            }
        }
        
        $this->set(compact('tasks'));
        $this->set('_serialize', ['tasks']);
    }

    /**
     * View method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $task = $this->Tasks->get($id, [
            'contain' => ['Categories']
        ]);

        $task['created_by_id'] = $task['created_by'];
        $task['created_by'] = $this->getRelatedUser($task['created_by']);

        if ($task['done_by'] != 0) {
            $task['done_by_id'] = $task['done_by'];
            $task['done_by'] = $this->getRelatedUser($task['done_by']);
        }

        $files = $this->getRelatedFiles($id);

        //$taskarray = (array) $task;
        //debug($taskarray);

        $this->set('files', $files);
        $this->set('task', $task);
        $this->set('_serialize', ['task']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $task = $this->Tasks->newEntity();
        if ($this->request->is('post')) {
            $task = $this->Tasks->patchEntity($task, $this->request->getData());
            $task->created_by = $this->Auth->user('id');
            
            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('The task has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The task could not be saved. Please, try again.'));
        }
        $categories = $this->Tasks->Categories->find('list', ['limit' => 200]);
        $this->set(compact('task', 'categories'));
        $this->set('_serialize', ['task']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $task = $this->Tasks->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $task = $this->Tasks->patchEntity($task, $this->request->getData());
            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('The task has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The task could not be saved. Please, try again.'));
        }
        $categories = $this->Tasks->Categories->find('list', ['limit' => 200]);
        $this->set(compact('task', 'categories'));
        $this->set('_serialize', ['task']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Task id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $task = $this->Tasks->get($id);
        if ($this->Tasks->delete($task)) {
            $this->Flash->success(__('The task has been deleted.'));
        } else {
            $this->Flash->error(__('The task could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function markDone($id = null) {
        $task = $this->Tasks->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = ['status' => 'done', 'done_by' => $this->Auth->user('id')];

            $task = $this->Tasks->patchEntity($task, $data);

            if ($this->Tasks->save($task)) {
                $this->Flash->success(__('The task has been marked done! Congrats!'));

                return $this->redirect(['action' => 'view', $id]);
            }
            $this->Flash->error(__('The task could not be marked done. Please, try again.'));
        }
        return $this->redirect(['action' => 'view', $id]);
    }

    //funcao para formatar horarios das tasks antes de exibicao
    private function formatDatesToDisplay($dateToDisplay) {
        
        if ($dateToDisplay == null) {
            $dateToDisplay = '--';
        } else {
            $dateToDisplay = $dateToDisplay->format('H:i:s d-m-Y');
        }

        return $dateToDisplay;
    }

    private function getRelatedUser($user_id) {
        $this->loadModel('Users');

        $user = $this->Users->find()->where(['id' => $user_id])->first();
        
        return $user['username'];
    }

    private function getRelatedFiles($task_id) {
        $this->loadModel('Files');

        $files = $this->Files->find()->where(['file_of' => $task_id]);

        return $files;
    }
}
