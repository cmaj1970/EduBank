<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

class UsersController extends AppController
{
    /**
     * Before filter - allow login/logout without authentication
     *
     * @param \Cake\Event\Event $event The event
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['login', 'logout']);
    }

    /**
     * Index method - list all users
     *
     * @return void
     */
     public function index()
     {
         $query = $this->Users->query();
         $this->paginate['contain'] = [
             'Accounts', 'Schools'
         ];
         if($this->school) {
             $query->where(['school_id' => $this->school['id'], 'role' => 'user']);
         }
         $users = $this->paginate($query);
         $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Accounts']
        ]);

        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $user->roles = ['admin' => 'Admin', 'user' => 'User'];
        $conditions = [];
        $passworddefault = '';
        if($this->school) {
            $conditions = ['id' => $this->school['id']];
            $schoolusers = $this->Users->find('all')->where(['school_id' => $this->school['id']]);
            $user->username = $this->school['kurzname'] . "-" . $schoolusers->count();
            $user->name = $this->school['name'] . " " . $schoolusers->count();
            $user->roles = ['user' => 'User'];
            // Default password for students from environment variable
            $user->password = env('DEFAULT_USER_PASSWORD', 'ChangeMe123');
        	$passworddefault = env('DEFAULT_USER_PASSWORD', 'ChangeMe123');
        }
        $user->active = 1;
        $schools = $this->Users->Schools->find('list')->where($conditions);
        $this->set(compact('user', 'schools', 'passworddefault'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Accounts', 'Schools']
        ]);

        # Schuladmin darf nur User seiner Schule bearbeiten
        if ($this->school && $user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Benutzer Ihrer eigenen Schule bearbeiten.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            # Schuladmin darf school_id nicht ändern
            $data = $this->request->getData();
            if ($this->school) {
                $data['school_id'] = $this->school['id'];
            }

            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('Benutzer wurde gespeichert.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Der Benutzer konnte nicht gespeichert werden.'));
        }

        $conditions = [];
        if ($this->school) {
            $conditions = ['id' => $this->school['id']];
        }
        $schools = $this->Users->Schools->find('list')->where($conditions);
        $this->set(compact('user', 'schools'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);

        # Superadmin "admin" darf nicht gelöscht werden
        if ($user->username === 'admin') {
            $this->Flash->error(__('Der Superadmin "admin" kann nicht gelöscht werden.'));
            return $this->redirect(['action' => 'index']);
        }

        # Konten und Transaktionen des Users löschen
        $this->loadModel('Accounts');
        $this->loadModel('Transactions');

        $deletedAccounts = 0;
        $deletedTransactions = 0;

        $accounts = $this->Accounts->find('all')
            ->where(['user_id' => $user->id])
            ->toArray();

        foreach ($accounts as $account) {
            $deletedTransactions += $this->Transactions->deleteAll(['account_id' => $account->id]);
            $deletedTransactions += $this->Transactions->deleteAll(['empfaenger_iban' => $account->iban]);
            $deletedAccounts++;
        }

        $this->Accounts->deleteAll(['user_id' => $user->id]);

        if ($this->Users->delete($user)) {
            $this->Flash->success(__('Benutzer "{0}" gelöscht inkl. {1} Konten, {2} Transaktionen.',
                $user->name, $deletedAccounts, $deletedTransactions));
        } else {
            $this->Flash->error(__('Der Benutzer konnte nicht gelöscht werden.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login method - authenticate user
     *
     * @return \Cake\Http\Response|null Redirects on successful login
     */
	    public function login()
	    {
	        if ($this->request->is('post')) {
	            $user = $this->Auth->identify();
	            if ($user) {
	                $this->Auth->setUser($user);
	                return $this->redirect($this->Auth->redirectUrl());
	            }
	            $this->Flash->error(__('Bitte überprüfen Sie den Benutzernamen und das Passwort'));
	        }
	    }

    /**
     * Logout method - end user session
     *
     * @return \Cake\Http\Response Redirects to login page
     */
	    public function logout()
	    {
	        return $this->redirect($this->Auth->logout());
	    }

}
