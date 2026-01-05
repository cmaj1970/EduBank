<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Tans Controller
 *
 * @property \App\Model\Table\TansTable $Tans
 *
 * @method \App\Model\Entity\Tan[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TansController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Accounts']
        ];
        $tans = $this->paginate($this->Tans);

        $this->set(compact('tans'));
    }

    /**
     * View method
     *
     * @param string|null $id Tan id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $tan = $this->Tans->get($id, [
            'contain' => ['Accounts']
        ]);

        $this->set('tan', $tan);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $tan = $this->Tans->newEntity();
        if ($this->request->is('post')) {
            $tan = $this->Tans->patchEntity($tan, $this->request->getData());
            if ($this->Tans->save($tan)) {
                $this->Flash->success(__('The tan has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tan could not be saved. Please, try again.'));
        }
        $accounts = $this->Tans->Accounts->find('list', ['limit' => 200]);
        $this->set(compact('tan', 'accounts'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Tan id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $tan = $this->Tans->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $tan = $this->Tans->patchEntity($tan, $this->request->getData());
            if ($this->Tans->save($tan)) {
                $this->Flash->success(__('The tan has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The tan could not be saved. Please, try again.'));
        }
        $accounts = $this->Tans->Accounts->find('list', ['limit' => 200]);
        $this->set(compact('tan', 'accounts'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Tan id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $tan = $this->Tans->get($id);
        if ($this->Tans->delete($tan)) {
            $this->Flash->success(__('The tan has been deleted.'));
        } else {
            $this->Flash->error(__('The tan could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    public function generate($account_id, $count)
    {
    	$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789";
    	for ($x = 1; $x <= $count; $x++) {
    		$tan = $this->Tans->newEntity();
    		$tan->account_id = $account_id;
    		$tan->tan = substr(str_shuffle($chars),0,8);
    		$this->Tans->save($tan);
   		}
    }

}
