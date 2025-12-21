<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Email;
use Cake\Routing\Router;

/**
 * Schools Controller
 *
 * @property \App\Model\Table\SchoolsTable $Schools
 *
 * @method \App\Model\Entity\School[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class SchoolsController extends AppController
{
    /**
     * Public actions that don't require login
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        // Allow public registration, confirmation page, and email verification
        $this->Auth->allow(['register', 'registered', 'resendEmail', 'verify']);
    }

    /**
     * Authorization - who can do what?
     *
     * @param array $user The logged in user
     * @return bool
     */
    public function isAuthorized($user)
    {
        // Superadmin (username='admin') can do everything
        if (isset($user['username']) && $user['username'] === 'admin') {
            return true;
        }

        // Pending verification pages are always accessible for logged-in school admins
        if (in_array($this->request->getParam('action'), ['pendingVerification', 'resendVerification'])) {
            return true;
        }

        // School admins can only read (index, view)
        if (in_array($this->request->getParam('action'), ['index', 'view'])) {
            return true;
        }

        // Everything else forbidden
        return false;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->Schools->query();

        # Schuladmin sieht nur eigene Schule
        if ($this->school) {
            $query->where(['id' => $this->school['id']]);
        }

        $schools = $this->paginate($query);

        // For superadmin: Get school admins for impersonation
        $schoolAdmins = [];
        $currentUser = $this->Auth->user();
        if ($currentUser['username'] === 'admin') {
            $this->loadModel('Users');
            foreach ($schools as $school) {
                $admin = $this->Users->find()
                    ->where([
                        'school_id' => $school->id,
                        'role' => 'admin',
                        'username LIKE' => 'admin-%'
                    ])
                    ->first();
                if ($admin) {
                    $schoolAdmins[$school->id] = $admin;
                }
            }
        }

        $isSuperadmin = ($currentUser['username'] === 'admin');
        $this->set(compact('schools', 'schoolAdmins', 'isSuperadmin'));
    }

    /**
     * View method
     *
     * @param string|null $id School id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        # Schuladmin darf nur eigene Schule sehen
        if ($this->school && $id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Ihre eigene Schule einsehen.'));
            return $this->redirect(['action' => 'index']);
        }

        $school = $this->Schools->get($id, [
            'contain' => ['Users']
        ]);

        $this->set('school', $school);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add_disabled()
    {
        $school = $this->Schools->newEntity();
        if ($this->request->is('post')) {
            $school = $this->Schools->patchEntity($school, $this->request->getData());
            if ($this->Schools->save($school)) {
                $this->Flash->success(__('The school has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The school could not be saved. Please, try again.'));
        }
        $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $school->bic = substr(str_shuffle($permitted_chars), 0, 4) . "AT" . substr(str_shuffle($permitted_chars), 0, 2);
        $this->set(compact('school'));
    }

    /**
     * Edit method
     *
     * @param string|null $id School id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $school = $this->Schools->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $school = $this->Schools->patchEntity($school, $this->request->getData());
            if ($this->Schools->save($school)) {
                $this->Flash->success(__('The school has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The school could not be saved. Please, try again.'));
        }
        $this->set(compact('school'));
    }

    /**
     * Delete method
     *
     * @param string|null $id School id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $school = $this->Schools->get($id);

        # Statistiken für Lösch-Feedback sammeln
        $this->loadModel('Users');
        $this->loadModel('Accounts');
        $this->loadModel('Transactions');

        # Alle User dieser Schule finden
        $users = $this->Users->find('all')
            ->where(['school_id' => $id])
            ->toArray();

        $deletedUsers = 0;
        $deletedAccounts = 0;
        $deletedTransactions = 0;

        # Für jeden User: Konten und Transaktionen löschen
        foreach ($users as $user) {
            # Konten des Users finden
            $accounts = $this->Accounts->find('all')
                ->where(['user_id' => $user->id])
                ->toArray();

            foreach ($accounts as $account) {
                # Transaktionen löschen (als Auftraggeber)
                $deletedTransactions += $this->Transactions->deleteAll(['account_id' => $account->id]);
                # Transaktionen löschen (als Empfänger)
                $deletedTransactions += $this->Transactions->deleteAll(['empfaenger_iban' => $account->iban]);
                $deletedAccounts++;
            }

            # Konten des Users löschen
            $this->Accounts->deleteAll(['user_id' => $user->id]);
            $deletedUsers++;
        }

        # Alle User der Schule löschen
        $this->Users->deleteAll(['school_id' => $id]);

        # Schule löschen
        if ($this->Schools->delete($school)) {
            $this->Flash->success(__('Schule "{0}" gelöscht inkl. {1} Übungsfirmen, {2} Konten, {3} Transaktionen.',
                $school->name, $deletedUsers, $deletedAccounts, $deletedTransactions));
        } else {
            $this->Flash->error(__('Die Schule konnte nicht gelöscht werden.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Pending verification page
     * Shown to logged-in admins whose school is not yet verified
     *
     * @return \Cake\Http\Response|null
     */
    public function pendingVerification()
    {
        // Get school from session (set by AppController)
        $school = $this->school;

        if (!$school) {
            return $this->redirect(['controller' => 'Users', 'action' => 'logout']);
        }

        // If already verified, redirect to home
        if ($school->status === 'approved') {
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        $this->set(compact('school'));
    }

    /**
     * Resend verification email for logged-in admins
     *
     * @return \Cake\Http\Response|null
     */
    public function resendVerification()
    {
        $this->request->allowMethod(['post']);

        if (!$this->school || $this->school->status !== 'pending') {
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        // Reload school fresh from database to ensure proper ORM handling
        $school = $this->Schools->get($this->school->id);

        // Generate new token
        $school->verification_token = bin2hex(random_bytes(32));

        if (!$this->Schools->save($school)) {
            $errors = $school->getErrors();
            $this->log('ResendVerification: Save failed - ' . json_encode($errors), 'error');
            $this->Flash->error(__('Token konnte nicht gespeichert werden.'));
            return $this->redirect(['action' => 'pendingVerification']);
        }

        // Get admin user
        $this->loadModel('Users');
        $adminUser = $this->Users->find()
            ->where([
                'school_id' => $school->id,
                'role' => 'admin',
                'username LIKE' => 'admin-%'
            ])
            ->first();

        if ($adminUser) {
            $username = $adminUser->username;
            $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');

            // Use contact_email from school if available
            $email = $school->contact_email;

            if ($email) {
                $emailSent = $this->_sendWelcomeEmail($email, $school->name, $username, $password, $school->verification_token);

                if ($emailSent) {
                    $this->Flash->success(__('Die Bestätigungs-E-Mail wurde erneut an {0} gesendet.', $email));
                } else {
                    $this->Flash->error(__('Die E-Mail konnte nicht gesendet werden. Bitte versuchen Sie es später erneut.'));
                }
            } else {
                $this->Flash->error(__('Keine E-Mail-Adresse hinterlegt.'));
            }
        }

        return $this->redirect(['action' => 'pendingVerification']);
    }

    /**
     * Registration confirmation page
     * Shows credentials and allows email resend
     *
     * @return \Cake\Http\Response|null
     */
    public function registered()
    {
        $schoolId = $this->request->getQuery('school');
        $email = $this->request->getQuery('email');
        $emailSent = $this->request->getQuery('sent') === '1';

        if (!$schoolId) {
            return $this->redirect(['action' => 'register']);
        }

        try {
            $school = $this->Schools->get($schoolId);
        } catch (\Exception $e) {
            return $this->redirect(['action' => 'register']);
        }

        // Get admin user for this school
        $this->loadModel('Users');
        $adminUser = $this->Users->find()
            ->where([
                'school_id' => $school->id,
                'role' => 'admin',
                'username LIKE' => 'admin-%'
            ])
            ->first();

        if (!$adminUser) {
            $this->Flash->error(__('Admin-Benutzer konnte nicht gefunden werden.'));
            return $this->redirect(['action' => 'register']);
        }

        $username = $adminUser->username;
        $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');

        $this->set(compact('school', 'email', 'emailSent', 'username', 'password'));
    }

    /**
     * Resend welcome email
     *
     * @return \Cake\Http\Response|null
     */
    public function resendEmail()
    {
        $this->request->allowMethod(['post']);

        $schoolId = $this->request->getData('school_id');
        $email = $this->request->getData('email');

        if (!$schoolId || !$email) {
            $this->Flash->error(__('Ungültige Anfrage.'));
            return $this->redirect(['action' => 'register']);
        }

        try {
            $school = $this->Schools->get($schoolId);
        } catch (\Exception $e) {
            $this->Flash->error(__('Schule nicht gefunden.'));
            return $this->redirect(['action' => 'register']);
        }

        // Get admin user
        $this->loadModel('Users');
        $adminUser = $this->Users->find()
            ->where([
                'school_id' => $school->id,
                'role' => 'admin',
                'username LIKE' => 'admin-%'
            ])
            ->first();

        if (!$adminUser) {
            $this->Flash->error(__('Admin-Benutzer nicht gefunden.'));
            return $this->redirect(['action' => 'register']);
        }

        // Generate new token if school is still pending
        if ($school->status === 'pending') {
            $school->verification_token = bin2hex(random_bytes(32));
            $this->Schools->save($school);
        }

        $username = $adminUser->username;
        $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');

        $emailSent = $this->_sendWelcomeEmail($email, $school->name, $username, $password, $school->verification_token);

        return $this->redirect([
            'action' => 'registered',
            '?' => [
                'school' => $school->id,
                'email' => $email,
                'sent' => $emailSent ? '1' : '0',
                'resent' => '1'
            ]
        ]);
    }

    /**
     * Verify school email address
     * Called when user clicks the verification link in the email
     *
     * @return \Cake\Http\Response|null
     */
    public function verify()
    {
        try {
            $token = $this->request->getQuery('token');

            if (!$token) {
                $this->Flash->error(__('Ungültiger Bestätigungslink.'));
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            }

            // Find school by token
            $school = $this->Schools->find()
                ->where(['verification_token' => $token])
                ->first();

            if (!$school) {
                $this->Flash->error(__('Dieser Bestätigungslink ist ungültig oder wurde bereits verwendet.'));
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            }

            // Check if already verified
            if ($school->status === 'approved') {
                $this->Flash->info(__('Ihre Schule wurde bereits bestätigt. Sie können sich jetzt anmelden.'));
                return $this->redirect(['controller' => 'Users', 'action' => 'login']);
            }

            // Verify the school
            $school->status = 'approved';
            $school->verified_at = FrozenTime::now();
            $school->verification_token = null; // Clear token after use

            if ($this->Schools->save($school)) {
                $this->Flash->success(__('Ihre E-Mail-Adresse wurde bestätigt! Die Schule "{0}" ist jetzt freigeschaltet.', $school->name));
            } else {
                // Log validation errors
                $errors = $school->getErrors();
                $this->log('Verify: Save failed - ' . json_encode($errors), 'error');
                $this->Flash->error(__('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.'));
            }

            return $this->redirect(['controller' => 'Users', 'action' => 'login']);

        } catch (\Exception $e) {
            $this->log('Verify Exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(), 'error');
            $this->Flash->error(__('Es ist ein technischer Fehler aufgetreten.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }

    /**
     * Public registration for new schools
     * No login required - separate template for public access
     *
     * @return \Cake\Http\Response|null
     */
    public function register()
    {
        $school = $this->Schools->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            # Kurzname generieren falls leer (aus dem Schulnamen)
            if (empty($data['kurzname']) && !empty($data['name'])) {
                $data['kurzname'] = $this->_normalizeKurzname($data['name']);
            } elseif (!empty($data['kurzname'])) {
                $data['kurzname'] = $this->_normalizeKurzname($data['kurzname']);
            }

            // Make school name AND short name unique
            if (!empty($data['name']) && !empty($data['kurzname'])) {
                $result = $this->_makeSchoolnameAndKurznameUnique($data['name'], $data['kurzname']);
                $data['name'] = $result['name'];
                $data['kurzname'] = $result['kurzname'];
            }

            $school = $this->Schools->patchEntity(
                $school,
                $data,
                ['validate' => 'register']
            );

            // Auto-generate BIC
            $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $school->bic = substr(str_shuffle($permitted_chars), 0, 4) . "AT" . substr(str_shuffle($permitted_chars), 0, 2);

            // Auto-generate IBAN prefix
            $school->ibanprefix = $this->_generateIbanPrefix();

            // Generate verification token
            $school->verification_token = bin2hex(random_bytes(32));
            $school->status = 'pending';

            // Store contact email for later resend
            $email = $this->request->getData('email');
            $school->contact_email = $email;

            if ($this->Schools->save($school)) {

                # Admin-User für die Schule erstellen
                $password = $this->_createSchoolAdminOnRegister($school);

                if ($password) {
                    $username = 'admin-' . $school->kurzname;

                    # E-Mail mit Zugangsdaten und Verification-Link versenden
                    $emailSent = $this->_sendWelcomeEmail($email, $school->name, $username, $password, $school->verification_token);

                    # Zur Bestätigungsseite weiterleiten (zeigt Credentials + Resend-Option)
                    return $this->redirect([
                        'action' => 'registered',
                        '?' => [
                            'school' => $school->id,
                            'email' => $email,
                            'sent' => $emailSent ? '1' : '0'
                        ]
                    ]);
                } else {
                    $this->Flash->warning(__('Schule wurde erstellt, aber es gab ein Problem beim Erstellen des Admins. Bitte kontaktieren Sie den Support.'));
                    return $this->redirect(['controller' => 'Users', 'action' => 'login']);
                }
            }

            // Show validation errors
            $errors = $school->getErrors();
            if (!empty($errors)) {
                $errorMessages = [];
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errorMessages[] = "$field: $error";
                    }
                }
                $this->Flash->error(__('Fehler: {0}', implode(', ', $errorMessages)));
            } else {
                $this->Flash->error(__('Die Registrierung konnte nicht abgeschlossen werden. Bitte überprüfen Sie Ihre Eingaben.'));
            }
        }

        $this->set(compact('school'));
    }

    /**
     * Self-service registration for new schools (admin view)
     * Requires login - for admins adding schools
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $school = $this->Schools->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            # Kurzname generieren falls leer (aus dem Schulnamen)
            if (empty($data['kurzname']) && !empty($data['name'])) {
                $data['kurzname'] = $this->_normalizeKurzname($data['name']);
            } elseif (!empty($data['kurzname'])) {
                # Kurzname vom Frontend: Umlaute konvertieren und bereinigen
                $data['kurzname'] = $this->_normalizeKurzname($data['kurzname']);
            }

            // Make school name AND short name unique (synchronized numbering)
            if (!empty($data['name']) && !empty($data['kurzname'])) {
                $result = $this->_makeSchoolnameAndKurznameUnique($data['name'], $data['kurzname']);
                $data['name'] = $result['name'];
                $data['kurzname'] = $result['kurzname'];
            }

            // Use special validation for registration
            $school = $this->Schools->patchEntity(
                $school,
                $data,
                ['validate' => 'register']
            );

            // Automatically set status to 'pending'
            $school->status = 'pending';

            // Auto-generate BIC
            $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $school->bic = substr(str_shuffle($permitted_chars), 0, 4) . "AT" . substr(str_shuffle($permitted_chars), 0, 2);

            // Auto-generate IBAN prefix (next available ATxx, BTxx, ...)
            $school->ibanprefix = $this->_generateIbanPrefix();

            if ($this->Schools->save($school)) {
                # E-Mail-Adresse aus Formular holen
                $email = $this->request->getData('email');

                # Admin-User für die Schule erstellen
                $password = $this->_createSchoolAdminOnRegister($school);

                if ($password) {
                    $username = 'admin-' . $school->kurzname;

                    # E-Mail mit Zugangsdaten versenden
                    $emailSent = $this->_sendWelcomeEmail($email, $school->name, $username, $password);

                    if ($emailSent) {
                        $this->Flash->success(__('Schule erstellt! Die Zugangsdaten wurden an {0} gesendet.', $email));
                    } else {
                        $this->Flash->success(__('Schule erstellt! Admin: admin-{0}, Passwort: {1} (E-Mail konnte nicht gesendet werden)', $school->kurzname, $password));
                    }
                } else {
                    # Admin konnte nicht erstellt werden - Warnung anzeigen (Fehler kommt von _createSchoolAdminOnRegister)
                    $this->Flash->warning(__('Schule "{0}" erstellt, aber Admin-User konnte nicht erstellt werden. Kurzname: {1}, School-ID: {2}',
                        $school->name, $school->kurzname, $school->id));
                }
                return $this->redirect(['controller' => 'Schools', 'action' => 'index']);
            }

            // Debug: Show validation errors
            $errors = $school->getErrors();
            if (!empty($errors)) {
                $errorMessages = [];
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $errorMessages[] = "$field: $error";
                    }
                }
                $this->Flash->error(__('Fehler: {0}', implode(', ', $errorMessages)));
            } else {
                $this->Flash->error(__('Die Registrierung konnte nicht gespeichert werden. Bitte überprüfen Sie Ihre Eingaben.'));
            }
        }

        $this->set(compact('school'));
    }

    /**
     * Approve school (superadmin only)
     *
     * @param string|null $id School id.
     * @return \Cake\Http\Response|null
     */
    public function approve($id = null)
    {
        $this->request->allowMethod(['post']);

        $school = $this->Schools->get($id);
        $school->status = 'approved';

        if ($this->Schools->save($school)) {
            // Create school admin user
            $this->_createSchoolAdmin($school);

            $this->Flash->success(__('Die Schule "{0}" wurde genehmigt und ein Admin-Account erstellt.', $school->name));
        } else {
            $this->Flash->error(__('Die Schule konnte nicht genehmigt werden.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reject school (superadmin only)
     *
     * @param string|null $id School id.
     * @return \Cake\Http\Response|null
     */
    public function reject($id = null)
    {
        $this->request->allowMethod(['post']);

        $school = $this->Schools->get($id);
        $school->status = 'rejected';

        if ($this->Schools->save($school)) {
            $this->Flash->success(__('Die Registrierung wurde abgelehnt.'));
        } else {
            $this->Flash->error(__('Fehler beim Ablehnen der Registrierung.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Demo school management page (superadmin only)
     * Create, regenerate or delete demo data
     *
     * @return \Cake\Http\Response|null
     */
    public function demoSchool()
    {
        // Check if demo schools exist
        $demoSchools = $this->Schools->find()
            ->where(['kurzname LIKE' => '%demo%'])
            ->order(['kurzname' => 'ASC'])
            ->toArray();

        $demoStats = [];
        if (!empty($demoSchools)) {
            $this->loadModel('Users');
            $this->loadModel('Accounts');
            $this->loadModel('Transactions');

            $totalUsers = 0;
            $totalAccounts = 0;
            $totalTransactions = 0;

            foreach ($demoSchools as $school) {
                $userCount = $this->Users->find()
                    ->where(['school_id' => $school->id, 'role' => 'user'])
                    ->count();

                $accountCount = $this->Accounts->find()
                    ->matching('Users', function ($q) use ($school) {
                        return $q->where(['Users.school_id' => $school->id]);
                    })
                    ->count();

                $transactionCount = $this->Transactions->find()
                    ->matching('Accounts.Users', function ($q) use ($school) {
                        return $q->where(['Users.school_id' => $school->id]);
                    })
                    ->count();

                $totalUsers += $userCount;
                $totalAccounts += $accountCount;
                $totalTransactions += $transactionCount;
            }

            $demoStats = [
                'schools' => $demoSchools,
                'totalUsers' => $totalUsers,
                'totalAccounts' => $totalAccounts,
                'totalTransactions' => $totalTransactions
            ];
        }

        $this->set(compact('demoSchools', 'demoStats'));
    }

    /**
     * Create demo schools with sample data
     * Creates 3 schools with 5 companies each and cross-school transactions
     *
     * @return \Cake\Http\Response
     */
    public function createDemoSchool()
    {
        $this->request->allowMethod(['post']);

        // Check if any demo school already exists
        $existingDemo = $this->Schools->find()
            ->where(['kurzname LIKE' => '%demo%'])
            ->first();

        if ($existingDemo) {
            $this->Flash->error(__('Demoschulen existieren bereits. Bitte zuerst löschen.'));
            return $this->redirect(['action' => 'demoSchool']);
        }

        $this->loadModel('Users');
        $this->loadModel('Accounts');
        $this->loadModel('Transactions');

        // Define 3 demo schools
        $schoolsData = [
            [
                'name' => 'PTS Demo',
                'kurzname' => 'ptsdemo',
                'bic' => 'PTSDAT01',
                'ibanprefix' => 'PT01',
                'companies' => [
                    'Sonnenschein GmbH',
                    'Alpenblick Handels OG',
                    'Donauwind Services KG',
                    'Bergquell Getränke GmbH',
                    'Wienerwald IT Solutions'
                ]
            ],
            [
                'name' => 'HAK Demo',
                'kurzname' => 'hakdemo',
                'bic' => 'HAKDAT01',
                'ibanprefix' => 'HK01',
                'companies' => [
                    'Goldener Stern KG',
                    'Silbermond Trading GmbH',
                    'Kupferberg Logistics',
                    'Eisenhart Consulting OG',
                    'Diamant Solutions GmbH'
                ]
            ],
            [
                'name' => 'HTL Demo',
                'kurzname' => 'htldemo',
                'bic' => 'HTLDAT01',
                'ibanprefix' => 'HT01',
                'companies' => [
                    'TechNova Systems GmbH',
                    'CloudPeak Services KG',
                    'DataStream Analytics OG',
                    'CyberShield Security',
                    'GreenCode Software GmbH'
                ]
            ]
        ];

        $allAccounts = [];
        $totalCompanies = 0;
        $defaultPassword = env('DEFAULT_USER_PASSWORD', 'Schueler2024');
        $adminPassword = env('DEFAULT_ADMIN_PASSWORD', 'SchulAdmin2024');

        foreach ($schoolsData as $schoolIndex => $schoolData) {
            // Create school
            $school = $this->Schools->newEntity([
                'name' => $schoolData['name'],
                'kurzname' => $schoolData['kurzname'],
                'bic' => $schoolData['bic'],
                'ibanprefix' => $schoolData['ibanprefix'],
                'status' => 'approved',
                'contact_email' => $schoolData['kurzname'] . '@edubank.at'
            ]);

            if (!$this->Schools->save($school)) {
                continue;
            }

            // Create school admin
            $adminUser = $this->Users->newEntity([
                'username' => 'admin-' . $schoolData['kurzname'],
                'password' => $adminPassword,
                'name' => $schoolData['name'] . ' Admin',
                'role' => 'admin',
                'school_id' => $school->id,
                'active' => 1
            ]);
            $this->Users->save($adminUser);

            // Create companies for this school
            foreach ($schoolData['companies'] as $companyIndex => $companyName) {
                $username = $schoolData['kurzname'] . '-' . strtolower(preg_replace('/[^a-zA-Z]/', '', explode(' ', $companyName)[0]));

                $user = $this->Users->newEntity([
                    'username' => $username,
                    'password' => $defaultPassword,
                    'name' => $companyName,
                    'role' => 'user',
                    'school_id' => $school->id,
                    'active' => 1
                ]);

                if ($this->Users->save($user)) {
                    // Create account with unique IBAN
                    $ibanSuffix = str_pad(($schoolIndex * 10 + $companyIndex + 1) * 1111, 16, '0', STR_PAD_LEFT);
                    $account = $this->Accounts->newEntity([
                        'name' => $companyName . ' Geschäftskonto',
                        'user_id' => $user->id,
                        'iban' => $schoolData['ibanprefix'] . $ibanSuffix,
                        'bic' => $schoolData['bic'],
                        'balance' => 10000.00,
                        'maxlimit' => 2000.00
                    ]);

                    if ($this->Accounts->save($account)) {
                        $allAccounts[] = $account;
                        $totalCompanies++;
                    }
                }
            }
        }

        // Generate transactions (including cross-school)
        $transactionCount = $this->_generateDemoTransactions($allAccounts);

        $this->Flash->success(__('3 Demoschulen erstellt mit {0} Übungsfirmen und {1} Transaktionen (inkl. schulübergreifend).',
            $totalCompanies, $transactionCount));

        return $this->redirect(['action' => 'demoSchool']);
    }

    /**
     * Delete all demo schools and their data
     *
     * @return \Cake\Http\Response
     */
    public function deleteDemoSchool()
    {
        $this->request->allowMethod(['post']);

        $demoSchools = $this->Schools->find()
            ->where(['kurzname LIKE' => '%demo%'])
            ->toArray();

        if (empty($demoSchools)) {
            $this->Flash->error(__('Keine Demoschulen vorhanden.'));
            return $this->redirect(['action' => 'demoSchool']);
        }

        $this->loadModel('Users');
        $this->loadModel('Accounts');
        $this->loadModel('Transactions');

        $deletedSchools = 0;
        $deletedUsers = 0;
        $deletedAccounts = 0;
        $deletedTransactions = 0;

        foreach ($demoSchools as $demoSchool) {
            $users = $this->Users->find()
                ->where(['school_id' => $demoSchool->id])
                ->toArray();

            foreach ($users as $user) {
                $userAccounts = $this->Accounts->find()
                    ->where(['user_id' => $user->id])
                    ->toArray();

                foreach ($userAccounts as $account) {
                    $deletedTransactions += $this->Transactions->deleteAll(['account_id' => $account->id]);
                    $deletedTransactions += $this->Transactions->deleteAll(['empfaenger_iban' => $account->iban]);
                    $deletedAccounts++;
                }

                $this->Accounts->deleteAll(['user_id' => $user->id]);
                $deletedUsers++;
            }

            $this->Users->deleteAll(['school_id' => $demoSchool->id]);
            $this->Schools->delete($demoSchool);
            $deletedSchools++;
        }

        $this->Flash->success(__('{0} Demoschulen gelöscht inkl. {1} Benutzer, {2} Konten, {3} Transaktionen.',
            $deletedSchools, $deletedUsers, $deletedAccounts, $deletedTransactions));

        return $this->redirect(['action' => 'demoSchool']);
    }

    /**
     * Regenerate demo schools (delete and recreate)
     *
     * @return \Cake\Http\Response
     */
    public function regenerateDemoSchool()
    {
        $this->request->allowMethod(['post']);

        // First delete all demo schools
        $demoSchools = $this->Schools->find()
            ->where(['kurzname LIKE' => '%demo%'])
            ->toArray();

        if (!empty($demoSchools)) {
            $this->loadModel('Users');
            $this->loadModel('Accounts');
            $this->loadModel('Transactions');

            foreach ($demoSchools as $demoSchool) {
                $users = $this->Users->find()
                    ->where(['school_id' => $demoSchool->id])
                    ->toArray();

                foreach ($users as $user) {
                    $userAccounts = $this->Accounts->find()
                        ->where(['user_id' => $user->id])
                        ->toArray();

                    foreach ($userAccounts as $account) {
                        $this->Transactions->deleteAll(['account_id' => $account->id]);
                        $this->Transactions->deleteAll(['empfaenger_iban' => $account->iban]);
                    }
                    $this->Accounts->deleteAll(['user_id' => $user->id]);
                }

                $this->Users->deleteAll(['school_id' => $demoSchool->id]);
                $this->Schools->delete($demoSchool);
            }
        }

        // Now create fresh
        return $this->createDemoSchool();
    }

    /**
     * Generate demo transactions between accounts
     *
     * @param array $accounts List of account entities
     * @return int Number of transactions created
     */
    private function _generateDemoTransactions($accounts)
    {
        if (count($accounts) < 2) {
            return 0;
        }

        // Transaction purposes
        $purposes = [
            'Warenlieferung',
            'Dienstleistung',
            'Beratungshonorar',
            'Materialkosten',
            'Bürobedarf',
            'IT-Services',
            'Marketingkosten',
            'Logistikkosten',
            'Reparaturkosten',
            'Schulung',
            'Wartungsvertrag',
            'Projektabrechnung',
            'Anzahlung',
            'Restzahlung',
            'Monatsrechnung',
            'Quartalsabrechnung'
        ];

        $references = [
            'RE-2024-',
            'RG-',
            'AB-',
            'GS-',
            'PR-'
        ];

        $transactionCount = 0;
        $twoMonthsAgo = new FrozenTime('-2 months');
        $now = new FrozenTime();

        // Generate 25 transactions
        for ($i = 0; $i < 25; $i++) {
            // Random sender and receiver (must be different)
            $senderIndex = rand(0, count($accounts) - 1);
            do {
                $receiverIndex = rand(0, count($accounts) - 1);
            } while ($receiverIndex === $senderIndex);

            $sender = $accounts[$senderIndex];
            $receiver = $accounts[$receiverIndex];

            // Random date within last 2 months
            $daysAgo = rand(0, 60);
            $transactionDate = new FrozenTime("-{$daysAgo} days");

            // Random amount between 50 and 2000
            $amount = rand(50, 2000) + (rand(0, 99) / 100);

            // Random purpose
            $purpose = $purposes[array_rand($purposes)];
            $reference = $references[array_rand($references)] . rand(1000, 9999);

            $transaction = $this->Transactions->newEntity([
                'account_id' => $sender->id,
                'empfaenger_name' => str_replace(' Geschäftskonto', '', $receiver->name),
                'empfaenger_iban' => $receiver->iban,
                'empfaenger_bic' => $receiver->bic,
                'betrag' => $amount,
                'verwendungszweck' => $purpose . ' - ' . $reference,
                'datum' => $transactionDate,
                'created' => $transactionDate
            ]);

            if ($this->Transactions->save($transaction)) {
                // Update sender balance
                $sender->balance -= $amount;
                $this->Accounts->save($sender);

                // Update receiver balance
                $receiver->balance += $amount;
                $this->Accounts->save($receiver);

                $transactionCount++;
            }
        }

        return $transactionCount;
    }

    /**
     * Normalize short name (convert umlauts, remove special characters)
     *
     * @param string $kurzname The short name to normalize
     * @return string The normalized short name
     */
    private function _normalizeKurzname($kurzname)
    {
        // Convert umlauts
        $replacements = [
            'ä' => 'ae', 'Ä' => 'ae',
            'ö' => 'oe', 'Ö' => 'oe',
            'ü' => 'ue', 'Ü' => 'ue',
            'ß' => 'ss'
        ];

        $kurzname = strtr($kurzname, $replacements);

        // Convert to lowercase
        $kurzname = strtolower($kurzname);

        // Keep only alphanumeric characters
        $kurzname = preg_replace('/[^a-z0-9]/', '', $kurzname);

        return $kurzname;
    }

    /**
     * Schulname und Kurzname eindeutig machen mit synchroner Nummerierung
     * Beispiel: "Sonnental" + "sonnental" → "Sonnental 2" + "sonnental2"
     *
     * @param string $schoolname Schulname
     * @param string $kurzname Kurzname
     * @return array ['name' => '...', 'kurzname' => '...']
     */
    private function _makeSchoolnameAndKurznameUnique($schoolname, $kurzname)
    {
        $originalSchoolname = $schoolname;
        $originalKurzname = $kurzname;
        $counter = 2; # Erstes Duplikat bekommt "2"

        # Prüfen ob Schulname ODER Kurzname bereits existiert
        while (
            $this->Schools->exists(['name' => $schoolname]) ||
            $this->Schools->exists(['kurzname' => $kurzname])
        ) {
            # Schulname: "Sonnental" → "Sonnental 2"
            if (preg_match('/^(.+?)\s+(\d+)$/', $originalSchoolname, $matches)) {
                $base = $matches[1];
                $existingNumber = (int)$matches[2];
                $schoolname = $base . ' ' . ($existingNumber + ($counter - 1));
            } else {
                $schoolname = $originalSchoolname . ' ' . $counter;
            }

            # Kurzname: "sonnental" → "sonnental2"
            if (preg_match('/^(.+?)(\d+)$/', $originalKurzname, $matches)) {
                $base = $matches[1];
                $existingNumber = (int)$matches[2];
                $kurzname = $base . ($existingNumber + ($counter - 1));
            } else {
                $kurzname = $originalKurzname . $counter;
            }

            $counter++;

            # Sicherheitsabbruch (sollte nie erreicht werden)
            if ($counter > 100) {
                break;
            }
        }

        return [
            'name' => $schoolname,
            'kurzname' => $kurzname
        ];
    }

    /**
     * Generate next available IBAN prefix
     * Format: AT99 → AT98 → ... → AT01 → BT99 → BT98 → ... → ZT01
     *
     * @return string
     */
    private function _generateIbanPrefix()
    {
        // Get all used prefixes
        $usedPrefixes = $this->Schools->find()
            ->select(['ibanprefix'])
            ->order(['ibanprefix' => 'ASC'])
            ->extract('ibanprefix')
            ->toArray();

        // Iterate through letters A-Z
        for ($letter = 'A'; $letter <= 'Z'; $letter++) {
            // Iterate through numbers 99 to 01 (descending)
            for ($number = 99; $number >= 1; $number--) {
                $prefix = $letter . 'T' . str_pad($number, 2, '0', STR_PAD_LEFT);

                // If this prefix is not yet used, return it
                if (!in_array($prefix, $usedPrefixes)) {
                    return $prefix;
                }
            }
        }

        // Fallback (should never be reached, as 26*99 = 2574 possibilities)
        return 'ZT01';
    }

    /**
     * Create school admin user directly on registration
     *
     * @param \App\Model\Entity\School $school
     * @return string|bool Password if successful, false on error
     */
    private function _createSchoolAdminOnRegister($school)
    {
        $this->loadModel('Users');

        # Username: admin-{kurzname}
        $username = 'admin-' . $school->kurzname;

        # Prüfen ob User bereits existiert
        $existingUser = $this->Users->find()
            ->where(['username' => $username])
            ->first();

        if ($existingUser) {
            $this->log("SchoolAdmin: User '$username' existiert bereits", 'info');
            return false;
        }

        # Standard-Passwort für Schuladmins
        $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');

        # User erstellen
        $user = $this->Users->newEntity([
            'username' => $username,
            'password' => $password,
            'name' => $school->name,
            'role' => 'admin',
            'school_id' => $school->id,
            'active' => 1
        ]);

        # Debug: Validierungsfehler prüfen
        $errors = $user->getErrors();
        if (!empty($errors)) {
            $this->log("SchoolAdmin Validierungsfehler: " . json_encode($errors), 'error');
            $this->Flash->error(__('Admin-Fehler: {0}', json_encode($errors)));
            return false;
        }

        if ($this->Users->save($user)) {
            $this->log("SchoolAdmin '$username' erfolgreich erstellt", 'info');
            return $password;
        }

        # Debug: Save-Fehler
        $saveErrors = $user->getErrors();
        $this->log("SchoolAdmin Save-Fehler: " . json_encode($saveErrors), 'error');
        $this->Flash->error(__('Admin konnte nicht erstellt werden: {0}', json_encode($saveErrors)));
        return false;
    }

    /**
     * Create school admin user for approved school (used by approve())
     *
     * @param \App\Model\Entity\School $school
     * @return bool
     */
    private function _createSchoolAdmin($school)
    {
        $this->loadModel('Users');

        // Username: admin-{kurzname}
        $username = 'admin-' . $school->kurzname;

        // Check if user already exists
        $existingUser = $this->Users->find()
            ->where(['username' => $username])
            ->first();

        if ($existingUser) {
            return false; // User already exists
        }

        // Default password for school admins from environment variable
        $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');
        // Create user
        $user = $this->Users->newEntity([
            'username' => $username,
            'password' => $password,
            'name' => $school->contact_person ?: 'Schuladministrator',
            'role' => 'admin',
            'school_id' => $school->id
        ]);

        if ($this->Users->save($user)) {
            // TODO: Send email with credentials
            // For now: store password in Flash message
            $this->Flash->success(
                __('Admin-Account erstellt: Username = {0}, Passwort = {1}', $username, $password)
            );
            return true;
        }

        return false;
    }

    /**
     * Send welcome email with login credentials
     *
     * @param string $toEmail Recipient email address
     * @param string $schoolName Name of the school
     * @param string $username Admin username
     * @param string $password Admin password
     * @return bool True if email was sent successfully
     */
    private function _sendWelcomeEmail($toEmail, $schoolName, $username, $password, $verificationToken = null)
    {
        if (empty($toEmail)) {
            $this->log('WelcomeEmail: Keine E-Mail-Adresse angegeben', 'warning');
            return false;
        }

        try {
            // Login-URL dynamisch generieren
            $loginUrl = Router::url(['controller' => 'Users', 'action' => 'login'], true);

            // Verification-URL generieren
            $verifyUrl = null;
            if ($verificationToken) {
                $verifyUrl = Router::url([
                    'controller' => 'Schools',
                    'action' => 'verify',
                    '?' => ['token' => $verificationToken]
                ], true);
            }

            // Email mit Default-Profil (nutzt Konfiguration aus app.php/.env)
            $email = new Email('default');
            $email
                ->setFrom(['no-reply@edubank.solidcode.at' => 'EduBank'])
                ->setEmailFormat('html')
                ->setTo($toEmail)
                ->setSubject('Willkommen bei EduBank - Bitte bestätigen Sie Ihre E-Mail')
                ->setViewVars([
                    'schoolName' => $schoolName,
                    'username' => $username,
                    'password' => $password,
                    'loginUrl' => $loginUrl,
                    'verifyUrl' => $verifyUrl
                ])
                ->setTemplate('welcome_school')
                ->setLayout('welcome');

            $email->send();

            $this->log("WelcomeEmail: E-Mail an $toEmail gesendet", 'info');
            return true;

        } catch (\Exception $e) {
            $this->log('WelcomeEmail Fehler: ' . $e->getMessage(), 'error');
            return false;
        }
    }

}
