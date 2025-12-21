<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Mailer\Email;
use Cake\Routing\Router;

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
        $this->Auth->allow(['login', 'logout', 'requestCredentials']);
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
            'contain' => ['Accounts', 'Schools']
        ]);

        # Schuladmin darf nur User seiner Schule sehen
        if ($this->school && $user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Benutzer Ihrer eigenen Schule einsehen.'));
            return $this->redirect(['action' => 'index']);
        }

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
                $this->Flash->success(__('Benutzer wurde erstellt.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Der Benutzer konnte nicht erstellt werden.'));
        }

        # Standard-Passwörter aus .env
        $defaultAdminPassword = env('DEFAULT_ADMIN_PASSWORD', 'SchulAdmin2024');
        $defaultUserPassword = env('DEFAULT_USER_PASSWORD', 'Schueler2024');

        $user->roles = ['admin' => 'Admin', 'user' => 'User'];
        $conditions = [];

        if($this->school) {
            # Schuladmin erstellt Übungsfirma
            $conditions = ['id' => $this->school['id']];
            $schoolusers = $this->Users->find('all')->where(['school_id' => $this->school['id']]);
            $user->username = $this->school['kurzname'] . "-" . $schoolusers->count();
            $user->name = $this->school['name'] . " " . $schoolusers->count();
            $user->roles = ['user' => 'User'];
            $user->password = $defaultUserPassword;
            $passworddefault = $defaultUserPassword;
        } else {
            # Superadmin erstellt User - Standard-Passwort je nach Rolle
            # Default ist User-Passwort, wird im Template per JS aktualisiert
            $passworddefault = $defaultUserPassword;
        }

        $user->active = 1;
        $schools = $this->Users->Schools->find('list')->where($conditions);
        $this->set(compact('user', 'schools', 'passworddefault', 'defaultAdminPassword', 'defaultUserPassword'));
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

            # Leeres Passwort entfernen (nicht ändern)
            if (empty($data['password'])) {
                unset($data['password']);
            }

            # Update-Validierung verwenden (Passwort optional)
            $user = $this->Users->patchEntity($user, $data, ['validate' => 'update']);
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

        # Schuladmin darf nur User seiner Schule löschen
        if ($this->school && $user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Benutzer Ihrer eigenen Schule löschen.'));
            return $this->redirect(['action' => 'index']);
        }

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

                // Role-based redirect after login
                $redirectUrl = $this->_getLoginRedirect($user);
                return $this->redirect($redirectUrl);
            }
            $this->Flash->error(__('Bitte überprüfen Sie den Benutzernamen und das Passwort'));
        }
    }

    /**
     * Determine redirect URL based on user role
     *
     * @param array $user The authenticated user
     * @return array The redirect URL
     */
    private function _getLoginRedirect($user)
    {
        // Superadmin (username='admin'): Schools overview
        if ($user['username'] === 'admin') {
            return ['controller' => 'Schools', 'action' => 'index'];
        }

        // Schuladmin (admin role with 'admin-' prefix): Übungsfirmen overview
        if ($user['role'] === 'admin' && strpos($user['username'], 'admin-') === 0) {
            return ['controller' => 'Users', 'action' => 'index'];
        }

        // Übungsfirma (user role): Account page
        if ($user['role'] === 'user') {
            return ['controller' => 'Accounts', 'action' => 'index'];
        }

        // Fallback: Home page
        return ['controller' => 'Pages', 'action' => 'display', 'home'];
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

    /**
     * Request credentials - send login info to email
     * User enters email + school name, receives credentials by email
     *
     * @return \Cake\Http\Response|null
     */
    public function requestCredentials()
    {
        if ($this->request->is('post')) {
            $email = $this->request->getData('email');
            $schoolName = trim($this->request->getData('school_name'));

            if (empty($email) || empty($schoolName)) {
                $this->Flash->error(__('Bitte geben Sie E-Mail-Adresse und Schulname ein.'));
                return null;
            }

            // Find school by name (case-insensitive)
            $this->loadModel('Schools');
            $school = $this->Schools->find()
                ->where(['LOWER(name) LIKE' => '%' . strtolower($schoolName) . '%'])
                ->first();

            if (!$school) {
                // Try kurzname
                $school = $this->Schools->find()
                    ->where(['LOWER(kurzname) LIKE' => '%' . strtolower($schoolName) . '%'])
                    ->first();
            }

            if (!$school) {
                // Don't reveal if school exists or not (security)
                $this->Flash->success(__('Falls Ihre Daten korrekt sind, erhalten Sie in Kürze eine E-Mail mit Ihren Zugangsdaten.'));
                return $this->redirect(['action' => 'login']);
            }

            // Find admin user for this school
            $adminUser = $this->Users->find()
                ->where([
                    'school_id' => $school->id,
                    'role' => 'admin',
                    'username LIKE' => 'admin-%'
                ])
                ->first();

            if ($adminUser) {
                // Get password from env (can't retrieve hashed password)
                $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');

                // Send email
                $this->_sendCredentialsEmail($email, $school->name, $adminUser->username, $password);
            }

            // Always show success (don't reveal if user exists)
            $this->Flash->success(__('Falls Ihre Daten korrekt sind, erhalten Sie in Kürze eine E-Mail mit Ihren Zugangsdaten.'));
            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * Send credentials email
     *
     * @param string $toEmail Recipient
     * @param string $schoolName School name
     * @param string $username Username
     * @param string $password Password
     * @return bool
     */
    private function _sendCredentialsEmail($toEmail, $schoolName, $username, $password)
    {
        if (empty($toEmail)) {
            return false;
        }

        try {
            $loginUrl = Router::url(['controller' => 'Users', 'action' => 'login'], true);

            $email = new Email('default');
            $email
                ->setEmailFormat('html')
                ->setTo($toEmail)
                ->setSubject('EduBank - Ihre Zugangsdaten')
                ->setViewVars([
                    'schoolName' => $schoolName,
                    'username' => $username,
                    'password' => $password,
                    'loginUrl' => $loginUrl
                ])
                ->setTemplate('credentials_reminder')
                ->setLayout('welcome');

            $email->send();
            return true;

        } catch (\Exception $e) {
            $this->log('CredentialsEmail Fehler: ' . $e->getMessage(), 'error');
            return false;
        }
    }

}
