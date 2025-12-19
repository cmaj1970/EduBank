<?php
namespace App\Controller;

use App\Controller\AppController;

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
     * No public actions - all require login
     * Self-service registration will be added later
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        // No public actions
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
        $schools = $this->paginate($this->Schools);

        $this->set(compact('schools'));
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
            $this->Flash->success(__('Schule "{0}" gelöscht inkl. {1} Benutzer, {2} Konten, {3} Transaktionen.',
                $school->name, $deletedUsers, $deletedAccounts, $deletedTransactions));
        } else {
            $this->Flash->error(__('Die Schule konnte nicht gelöscht werden.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Self-service registration for new schools
     * Publicly accessible (no login required)
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
                # Admin-User für die Schule erstellen
                $password = $this->_createSchoolAdminOnRegister($school);

                if ($password) {
                    $this->Flash->success(__('Schule erstellt! Admin: admin-{0}, Passwort: {1}', $school->kurzname, $password));
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

}
