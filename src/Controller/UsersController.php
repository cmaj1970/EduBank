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
     * Authorization - allow stopImpersonating for impersonating users
     *
     * @param array $user The logged in user
     * @return bool
     */
    public function isAuthorized($user)
    {
        // Allow stopImpersonating for users who are impersonating
        if ($this->request->getParam('action') === 'stopImpersonating') {
            $originalAdmin = $this->request->getSession()->read('Auth.OriginalAdmin');
            if ($originalAdmin) {
                return true;
            }
        }

        // Fall back to parent authorization
        return parent::isAuthorized($user);
    }

    /**
     * Dashboard - Einstiegsseite für Schuladmin
     * Zeigt Infos und Quick-Links
     *
     * @return \Cake\Http\Response|void
     */
    public function dashboard()
    {
        # Nur für Schuladmin
        if (!$this->school) {
            return $this->redirect(['action' => 'index']);
        }

        $defaultPassword = env('DEFAULT_USER_PASSWORD', 'Schueler2024');

        $this->set(compact('defaultPassword'));
    }

    /**
     * Index method - list all users
     *
     * @return void
     */
     public function index()
     {
         $query = $this->Users->query();
         # Sortierung: Alphabetisch nach Firmenname
         $this->paginate = [
             'contain' => ['Accounts', 'Schools'],
             'order' => ['Users.name' => 'ASC']
         ];

         # Filter: Nur Übungsfirmen anzeigen (role=user)
         $query->where(['Users.role' => 'user']);

         if ($this->school) {
             # Schuladmin: Nur eigene Schule
             $query->where(['Users.school_id' => $this->school['id']]);
         } else {
             # Superadmin: Filter nach Schule (Dropdown)
             $selectedSchool = $this->request->getQuery('school_id');
             if ($selectedSchool) {
                 $query->where(['Users.school_id' => $selectedSchool]);
             }
         }

         # Textsuche (Name, Benutzername, Schulname)
         $search = $this->request->getQuery('search');
         if ($search) {
             $query->matching('Schools', function ($q) use ($search) {
                 return $q;
             })->where([
                 'OR' => [
                     'Users.name LIKE' => '%' . $search . '%',
                     'Users.username LIKE' => '%' . $search . '%',
                     'Schools.name LIKE' => '%' . $search . '%'
                 ]
             ]);
         }

         $users = $this->paginate($query);

         # Letzte Transaktionen aller Übungsfirmen laden (für Aktivitäts-Feed)
         $recentTransactions = [];
         if ($this->school) {
             $this->loadModel('Transactions');
             $this->loadModel('Accounts');

             # Alle Account-IDs der Schule sammeln (unabhängig von Pagination)
             $allSchoolAccounts = $this->Accounts->find()
                 ->contain(['Users'])
                 ->where(['Users.school_id' => $this->school['id']])
                 ->toArray();

             $accountIds = [];
             foreach ($allSchoolAccounts as $account) {
                 $accountIds[] = $account->id;
             }

             if (!empty($accountIds)) {
                 $recentTransactions = $this->Transactions->find()
                     ->contain(['Accounts.Users.Schools'])
                     ->where(['Transactions.account_id IN' => $accountIds])
                     ->order(['Transactions.created' => 'DESC'])
                     ->limit(100)
                     ->toArray();

                 # Empfänger-Schulen anhand IBAN ermitteln
                 $recipientIbans = array_unique(array_map(function($tx) {
                     return $tx->empfaenger_iban;
                 }, $recentTransactions));

                 $recipientAccounts = [];
                 if (!empty($recipientIbans)) {
                     $recipientData = $this->Accounts->find()
                         ->contain(['Users.Schools'])
                         ->where(['Accounts.iban IN' => $recipientIbans])
                         ->toArray();

                     foreach ($recipientData as $acc) {
                         $recipientAccounts[$acc->iban] = $acc;
                     }
                 }
             }
         }

         # Empfänger-Konten für Template bereitstellen
         if (!isset($recipientAccounts)) {
             $recipientAccounts = [];
         }

         # Schulen für Dropdown (nur Superadmin)
         $schoolList = [];
         $isSuperadmin = !$this->school;
         if ($isSuperadmin) {
             $this->loadModel('Schools');
             $schoolList = $this->Schools->find('list')
                 ->where(['status' => 'approved'])
                 ->order('name')
                 ->toArray();
         }

         $defaultPassword = env('DEFAULT_USER_PASSWORD', 'Schueler2024');
         $isSchoolAdmin = ($this->school !== null);
         $selectedSchool = $this->request->getQuery('school_id');

         $this->set(compact('users', 'defaultPassword', 'isSchoolAdmin', 'isSuperadmin', 'schoolList', 'selectedSchool', 'search', 'recentTransactions', 'recipientAccounts'));
    }

    /**
     * AJAX: Transaktionen für Live-Feed laden
     * Gibt JSON zurück für Auto-Refresh
     *
     * @return \Cake\Http\Response
     */
    public function ajaxTransactions()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');

        if (!$this->school) {
            return $this->response->withStringBody(json_encode(['error' => 'Unauthorized']));
        }

        $this->loadModel('Transactions');
        $this->loadModel('Accounts');

        # Alle Account-IDs der Schule
        $allSchoolAccounts = $this->Accounts->find()
            ->contain(['Users'])
            ->where(['Users.school_id' => $this->school['id']])
            ->toArray();

        $accountIds = [];
        foreach ($allSchoolAccounts as $account) {
            $accountIds[] = $account->id;
        }

        $transactions = [];
        if (!empty($accountIds)) {
            $recentTransactions = $this->Transactions->find()
                ->contain(['Accounts.Users.Schools'])
                ->where(['Transactions.account_id IN' => $accountIds])
                ->order(['Transactions.created' => 'DESC'])
                ->limit(100)
                ->toArray();

            # Empfänger-Schulen ermitteln
            $recipientIbans = array_unique(array_map(function($tx) {
                return $tx->empfaenger_iban;
            }, $recentTransactions));

            $recipientAccounts = [];
            if (!empty($recipientIbans)) {
                $recipientData = $this->Accounts->find()
                    ->contain(['Users.Schools'])
                    ->where(['Accounts.iban IN' => $recipientIbans])
                    ->toArray();

                foreach ($recipientData as $acc) {
                    $recipientAccounts[$acc->iban] = $acc;
                }
            }

            # Transaktionen für JSON aufbereiten
            foreach ($recentTransactions as $tx) {
                $senderSchoolId = $tx->account->user->school_id ?? 0;
                $recipientSchool = null;

                if (isset($recipientAccounts[$tx->empfaenger_iban])) {
                    $recipientAcc = $recipientAccounts[$tx->empfaenger_iban];
                    if (!empty($recipientAcc->user->school) && $recipientAcc->user->school_id != $senderSchoolId) {
                        $recipientSchool = $recipientAcc->user->school->name;
                    }
                }

                $transactions[] = [
                    'id' => $tx->id,
                    'sender_id' => $tx->account->user->id ?? 0,
                    'sender_name' => $tx->account->user->name ?? 'Unbekannt',
                    'recipient_name' => $tx->empfaenger_name,
                    'recipient_school' => $recipientSchool,
                    'amount' => $tx->betrag,
                    'purpose' => $tx->zahlungszweck,
                    'created' => $tx->created->format('d.m.Y H:i')
                ];
            }
        }

        return $this->response->withStringBody(json_encode([
            'transactions' => $transactions,
            'count' => count($transactions)
        ]));
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
            $this->Flash->error(__('Sie können nur Übungsfirmen Ihrer eigenen Schule einsehen.'));
            return $this->redirect(['action' => 'index']);
        }

        $defaultPassword = env('DEFAULT_USER_PASSWORD', 'Schueler2024');
        $isSchoolAdmin = ($this->school !== null);

        $this->set(compact('user', 'defaultPassword', 'isSchoolAdmin'));
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
                // Auto-create account for school admin created users
                if ($this->school && $user->role === 'user') {
                    $this->loadModel('Accounts');
                    $account = $this->Accounts->newEntity();

                    // Account name from form or default to user name
                    $accountName = $this->request->getData('account_name');
                    $account->name = !empty($accountName) ? $accountName : $user->name;

                    $account->user_id = $user->id;
                    $account->iban = $this->school['ibanprefix'] . rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
                    $account->bic = $this->school['bic'];
                    $account->balance = 10000; // Default starting balance
                    $account->maxlimit = 2000; // Default overdraft limit

                    if ($this->Accounts->save($account)) {
                        # Prefill mit Beispieltransaktionen wenn gewünscht
                        if ($this->request->getData('prefill_sample_data')) {
                            $txCount = $this->_prefillAccountWithSampleData($account->id);
                            $this->Flash->success(__('Übungsfirma und Konto wurden erstellt ({0} Beispieltransaktionen).', $txCount));
                        } else {
                            $this->Flash->success(__('Übungsfirma und Konto wurden erstellt.'));
                        }
                    } else {
                        $this->Flash->success(__('Übungsfirma wurde erstellt, aber Konto konnte nicht angelegt werden.'));
                    }
                } else {
                    $this->Flash->success(__('Übungsfirma wurde erstellt.'));
                }

                # Schuladmin: Zur Detailseite, Superadmin: zur Liste
                if ($this->school) {
                    return $this->redirect(['action' => 'view', $user->id]);
                }
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Die Übungsfirma konnte nicht erstellt werden.'));
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
            $this->Flash->error(__('Sie können nur Übungsfirmen Ihrer eigenen Schule bearbeiten.'));
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
                $this->Flash->success(__('Übungsfirma wurde gespeichert.'));
                # Schuladmin: Zurück zur Detailseite, Superadmin: zur Liste
                if ($this->school) {
                    return $this->redirect(['action' => 'view', $id]);
                }
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Die Übungsfirma konnte nicht gespeichert werden.'));
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
            $this->Flash->error(__('Sie können nur Übungsfirmen Ihrer eigenen Schule löschen.'));
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
            $this->Flash->success(__('Übungsfirma "{0}" gelöscht inkl. {1} Konten, {2} Transaktionen.',
                $user->name, $deletedAccounts, $deletedTransactions));
        } else {
            $this->Flash->error(__('Die Übungsfirma konnte nicht gelöscht werden.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Login method - authenticate user
     * - Superadmin (username="admin"): gehashtes Passwort aus DB
     * - Schuladmins (admin-*): DEFAULT_ADMIN_PASSWORD aus .env
     * - Übungsfirmen: DEFAULT_USER_PASSWORD aus .env
     *
     * @return \Cake\Http\Response|null Redirects on successful login
     */
    public function login()
    {
        # Bereits eingeloggt? Weiterleiten zur Einstiegsseite
        $currentUser = $this->Auth->user();
        if ($currentUser) {
            return $this->redirect($this->_getLoginRedirect($currentUser));
        }

        if ($this->request->is('post')) {
            $username = $this->request->getData('username');
            $password = $this->request->getData('password');

            # User aus DB laden
            $user = $this->Users->find()
                ->where(['username' => $username])
                ->contain(['Schools'])
                ->first();

            if ($user) {
                $authenticated = false;

                # Superadmin: gehashtes Passwort aus DB prüfen
                if ($username === 'admin') {
                    $hasher = new \Cake\Auth\DefaultPasswordHasher();
                    $authenticated = $hasher->check($password, $user->password);
                }
                # Schuladmins (admin-*): Passwort aus .env
                elseif (strpos($username, 'admin-') === 0) {
                    $envPassword = env('DEFAULT_ADMIN_PASSWORD', 'SchulAdmin2024');
                    $authenticated = ($password === $envPassword);
                }
                # Übungsfirmen: Passwort aus .env
                else {
                    $envPassword = env('DEFAULT_USER_PASSWORD', 'Schueler2024');
                    $authenticated = ($password === $envPassword);
                }

                if ($authenticated) {
                    # Prüfen ob Übungsfirma aktiv ist
                    if ($user->role === 'user' && !$user->active) {
                        $this->Flash->error(__('Diese Übungsfirma wurde deaktiviert. Bitte wenden Sie sich an Ihren Schuladministrator.'));
                        return;
                    }

                    $this->Auth->setUser($user->toArray());

                    # Last login aktualisieren
                    $user->last_login = new \DateTime();
                    $this->Users->save($user);

                    # Role-based redirect after login
                    $redirectUrl = $this->_getLoginRedirect($user->toArray());
                    return $this->redirect($redirectUrl);
                }
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

        // Schuladmin (admin role with 'admin-' prefix): Dashboard
        if ($user['role'] === 'admin' && strpos($user['username'], 'admin-') === 0) {
            return ['controller' => 'Users', 'action' => 'dashboard'];
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
        // Clear impersonation session on logout
        $this->request->getSession()->delete('Auth.OriginalAdmin');
        return $this->redirect($this->Auth->logout());
    }

    /**
     * Impersonate a user (for superadmin and school admins)
     * Superadmin can impersonate anyone, school admins only their own Übungsfirmen
     *
     * @param int $id User ID to impersonate
     * @return \Cake\Http\Response
     */
    public function impersonate($id = null)
    {
        $currentUser = $this->Auth->user();
        $isSuperadmin = ($currentUser['username'] === 'admin');

        // Only superadmin or school admins can impersonate
        if (!$isSuperadmin && !$this->school) {
            $this->Flash->error(__('Keine Berechtigung für diese Funktion.'));
            return $this->redirect(['action' => 'index']);
        }

        $targetUser = $this->Users->get($id, ['contain' => ['Schools']]);

        // Cannot impersonate the superadmin
        if ($targetUser->username === 'admin') {
            $this->Flash->error(__('Der Superadmin kann nicht angemeldet werden.'));
            return $this->redirect(['action' => 'index']);
        }

        // School admin restrictions
        if (!$isSuperadmin) {
            // Can only impersonate users from own school
            if ($targetUser->school_id != $this->school['id']) {
                $this->Flash->error(__('Sie können nur Übungsfirmen Ihrer eigenen Schule anzeigen.'));
                return $this->redirect(['action' => 'index']);
            }

            // Can only impersonate regular users, not admins
            if ($targetUser->role !== 'user') {
                $this->Flash->error(__('Diese Funktion ist nur für Übungsfirmen verfügbar.'));
                return $this->redirect(['action' => 'index']);
            }
        }

        // Store original admin in session
        $session = $this->request->getSession();
        $session->write('Auth.OriginalAdmin', $currentUser);

        // Switch to target user
        $this->Auth->setUser($targetUser->toArray());

        // Redirect based on target user role
        if ($targetUser->role === 'admin' && strpos($targetUser->username, 'admin-') === 0) {
            return $this->redirect(['controller' => 'Users', 'action' => 'dashboard']);
        } else {
            return $this->redirect(['controller' => 'Accounts', 'action' => 'index']);
        }
    }

    /**
     * Stop impersonating and return to admin session
     *
     * @return \Cake\Http\Response
     */
    public function stopImpersonating()
    {
        $session = $this->request->getSession();
        $originalAdmin = $session->read('Auth.OriginalAdmin');

        if (!$originalAdmin) {
            $this->Flash->error(__('Keine aktive Ansicht als Übungsfirma.'));
            return $this->redirect(['action' => 'index']);
        }

        // Restore original admin session
        $this->Auth->setUser($originalAdmin);
        $session->delete('Auth.OriginalAdmin');

        $this->Flash->success(__('Zurück zur Admin-Ansicht.'));
        return $this->redirect(['action' => 'index']);
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

    /**
     * Prefill account with sample transactions
     * Uses partners table for transaction partners
     *
     * @param int $accountId The account to prefill
     * @return int Number of transactions created
     */
    private function _prefillAccountWithSampleData($accountId)
    {
        $this->loadModel('Accounts');
        $this->loadModel('Transactions');
        $this->loadModel('Partners');

        # Partnerunternehmen laden
        $partners = $this->Partners->find()->toArray();

        if (empty($partners)) {
            return 0;
        }

        # Transaktions-Templates (Ausgaben) - passend zu Partnerunternehmen
        $ausgabenTemplates = [
            ['min' => 50, 'max' => 200, 'text' => 'Büromaterial'],
            ['min' => 100, 'max' => 400, 'text' => 'Rechnung'],
            ['min' => 200, 'max' => 800, 'text' => 'Warenlieferung'],
            ['min' => 80, 'max' => 250, 'text' => 'Dienstleistung'],
            ['min' => 150, 'max' => 500, 'text' => 'Druckkosten'],
            ['min' => 100, 'max' => 350, 'text' => 'IT-Service'],
            ['min' => 50, 'max' => 180, 'text' => 'Werbekosten'],
            ['min' => 200, 'max' => 600, 'text' => 'Versicherungsprämie'],
        ];

        # Transaktions-Templates (Einnahmen)
        $einnahmenTemplates = [
            ['min' => 150, 'max' => 800, 'format' => 'Zahlung RE-%d'],
            ['min' => 200, 'max' => 600, 'format' => 'Rechnung %d'],
            ['min' => 100, 'max' => 500, 'format' => 'Zahlungseingang %d'],
            ['min' => 150, 'max' => 400, 'format' => 'Überweisung'],
        ];

        $created = 0;
        $numTransactions = rand(12, 18);

        for ($i = 0; $i < $numTransactions; $i++) {
            # 85% Ausgaben, 15% Einnahmen
            $isAusgabe = (rand(1, 100) <= 85);
            $partner = $partners[array_rand($partners)];

            if ($isAusgabe) {
                $template = $ausgabenTemplates[array_rand($ausgabenTemplates)];
                $betrag = rand($template['min'] * 100, $template['max'] * 100) / 100;

                # Transaktion: Von uns an Partner
                $transaction = $this->Transactions->newEntity([
                    'account_id' => $accountId,
                    'empfaenger_name' => $partner->name,
                    'empfaenger_iban' => $partner->iban,
                    'empfaenger_bic' => $partner->bic,
                    'betrag' => $betrag,
                    'zahlungszweck' => $template['text'],
                    'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                ]);
            } else {
                $template = $einnahmenTemplates[array_rand($einnahmenTemplates)];
                $betrag = rand($template['min'] * 100, $template['max'] * 100) / 100;
                if (strpos($template['format'], '%d') !== false) {
                    $verwendung = sprintf($template['format'], rand(1000, 9999));
                } else {
                    $verwendung = $template['format'];
                }

                # Einnahme: Negativer Betrag = Gutschrift
                $transaction = $this->Transactions->newEntity([
                    'account_id' => $accountId,
                    'empfaenger_name' => 'Zahlungseingang',
                    'empfaenger_iban' => '',
                    'empfaenger_bic' => '',
                    'betrag' => -$betrag,
                    'zahlungszweck' => $verwendung,
                    'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                ]);
            }

            if ($this->Transactions->save($transaction)) {
                $created++;
            }
        }

        return $created;
    }

}
