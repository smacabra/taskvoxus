<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Files Controller
 *
 * @property \App\Model\Table\FilesTable $Files
 *
 * @method \App\Model\Entity\File[] paginate($object = null, array $settings = [])
 */
class FilesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);

        /*$files = $this->paginate($this->Files);

        $this->set(compact('files'));
        $this->set('_serialize', ['files']);*/
    }

    /**
     * View method
     *
     * @param string|null $id File id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);

        /*$file = $this->Files->get($id, [
            'contain' => []
        ]);

        $this->set('file', $file);
        $this->set('_serialize', ['file']);*/
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($task_id = null)
    {
        if($task_id == null) {
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        $this->loadModel('Tasks');

        $task = $this->Tasks->find()->where(['id' => $task_id])->first();

        if($task == null) {
            return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        }

        $this->set(['taskTitle' => $task->title]);

        $file = $this->Files->newEntity();
        if ($this->request->is('post')) {
            //$file = $this->Files->patchEntity($file, $this->request->getData());

            $file = $this->Files->patchEntity($file, $this->request->getParsedBody());
            $file->file_of = $task_id;

            if ($this->Files->save($file)) {
                $this->Flash->success(__('The file has been saved.'));

                //return $this->redirect(['action' => 'index']);
                return $this->redirect(['controller' => 'Tasks', 'action' => 'view', $task_id]);
            }
            $this->Flash->error(__('The file could not be saved. Please, try again.'));
        }
        $this->set(compact('file'));
        $this->set('_serialize', ['file']);
    }

    /**
     * Edit method
     *
     * @param string|null $id File id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);

        /*$file = $this->Files->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $file = $this->Files->patchEntity($file, $this->request->getData());
            if ($this->Files->save($file)) {
                $this->Flash->success(__('The file has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The file could not be saved. Please, try again.'));
        }
        $this->set(compact('file'));
        $this->set('_serialize', ['file']);*/
    }

    /**
     * Delete method
     *
     * @param string|null $id File id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        return $this->redirect(['controller' => 'Tasks', 'action' => 'index']);
        
        /*$this->request->allowMethod(['post', 'delete']);
        $file = $this->Files->get($id);
        if ($this->Files->delete($file)) {
            $this->Flash->success(__('The file has been deleted.'));
        } else {
            $this->Flash->error(__('The file could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);*/        
    }
}
