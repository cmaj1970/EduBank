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
     * Keine öffentlichen Actions - alles erfordert Login
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        parent::beforeFilter($event);
        # Keine öffentlichen Actions mehr
    }

    /**
     * Authorization - wer darf was?
     */
    public function isAuthorized($user)
    {
        # Superadmin (username='admin') darf alles
        if (isset($user['username']) && $user['username'] === 'admin') {
            return true;
        }

        # Schuladmins dürfen nur lesen (index, view)
        if (in_array($this->request->getParam('action'), ['index', 'view'])) {
            return true;
        }

        # Alles andere verboten
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
        if ($this->Schools->delete($school)) {
            $this->Flash->success(__('The school has been deleted.'));
        } else {
            $this->Flash->error(__('The school could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Self-Service Registrierung für neue Schulen
     * Öffentlich zugänglich (ohne Login)
     *
     * @return \Cake\Http\Response|null
     */
    public function add()
    {
        $school = $this->Schools->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            # Schulname: "PTS" voranstellen
            if (!empty($data['name'])) {
                $data['name'] = 'PTS ' . $data['name'];
            }

            # Kurzname: Aus Schulname generieren falls leer
            if (empty($data['kurzname']) && !empty($data['name'])) {
                # Von "PTS Gänserndorf" → "ptsgaenserndorf"
                $nameWithoutPTS = preg_replace('/^PTS\s*/i', '', $data['name']);
                $data['kurzname'] = 'pts' . $this->_normalizeKurzname($nameWithoutPTS);
            } elseif (!empty($data['kurzname'])) {
                # Kurzname vom Frontend: Umlaute konvertieren und bereinigen
                $data['kurzname'] = $this->_normalizeKurzname($data['kurzname']);
            }

            # Schulname UND Kurzname eindeutig machen (synchrone Nummerierung)
            if (!empty($data['name']) && !empty($data['kurzname'])) {
                $result = $this->_makeSchoolnameAndKurznameUnique($data['name'], $data['kurzname']);
                $data['name'] = $result['name'];
                $data['kurzname'] = $result['kurzname'];
            }

            # Verwende spezielle Validierung für Registrierung
            $school = $this->Schools->patchEntity(
                $school,
                $data,
                ['validate' => 'register']
            );

            # Status automatisch auf 'pending' setzen
            $school->status = 'pending';

            # BIC automatisch generieren
            $permitted_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $school->bic = substr(str_shuffle($permitted_chars), 0, 4) . "AT" . substr(str_shuffle($permitted_chars), 0, 2);

            # IBAN-Prefix automatisch generieren (nächstes freies ATxx, BTxx, ...)
            $school->ibanprefix = $this->_generateIbanPrefix();

            if ($this->Schools->save($school)) {
                # Admin-User für die Schule erstellen
                $password = $this->_createSchoolAdminOnRegister($school);

                if ($password) {
                    $this->Flash->success(__('Ihre Registrierung wurde erfolgreich übermittelt. Benutzername: admin-{0}, Passwort: {1}', $school->kurzname, $password));
                } else {
                    $this->Flash->success(__('Ihre Registrierung wurde erfolgreich übermittelt.'));
                }
                return $this->redirect(['controller' => 'Schools', 'action' => 'index']);
            }

            # Debug: Validation-Fehler anzeigen
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
     * Schule genehmigen (nur für Superadmin)
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
            # Schuladmin-User erstellen
            $this->_createSchoolAdmin($school);

            $this->Flash->success(__('Die Schule "{0}" wurde genehmigt und ein Admin-Account erstellt.', $school->name));
        } else {
            $this->Flash->error(__('Die Schule konnte nicht genehmigt werden.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Schule ablehnen (nur für Superadmin)
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
     * Normalisiert Kurzname (Umlaute konvertieren, Sonderzeichen entfernen)
     *
     * @param string $kurzname
     * @return string
     */
    private function _normalizeKurzname($kurzname)
    {
        # Umlaute konvertieren
        $replacements = [
            'ä' => 'ae', 'Ä' => 'ae',
            'ö' => 'oe', 'Ö' => 'oe',
            'ü' => 'ue', 'Ü' => 'ue',
            'ß' => 'ss'
        ];

        $kurzname = strtr($kurzname, $replacements);

        # Zu lowercase
        $kurzname = strtolower($kurzname);

        # Nur alphanumerische Zeichen behalten
        $kurzname = preg_replace('/[^a-z0-9]/', '', $kurzname);

        return $kurzname;
    }

    /**
     * Macht Schulname UND Kurzname eindeutig mit synchroner Nummerierung
     * Beispiel: "PTS Gänserndorf" + "ptsgaenserndorf"
     *           → "PTS Gänserndorf 2" + "ptsgaenserndorf2"
     *
     * @param string $schoolname
     * @param string $kurzname
     * @return array ['name' => '...', 'kurzname' => '...']
     */
    private function _makeSchoolnameAndKurznameUnique($schoolname, $kurzname)
    {
        $originalSchoolname = $schoolname;
        $originalKurzname = $kurzname;
        $counter = 2; // Erste Duplikat bekommt "2"

        # Prüfe ob Schulname ODER Kurzname bereits existiert
        while (
            $this->Schools->exists(['name' => $schoolname]) ||
            $this->Schools->exists(['kurzname' => $kurzname])
        ) {
            # Schulname: "PTS Gänserndorf" → "PTS Gänserndorf 2"
            # Extrahiere bestehende Nummer falls vorhanden
            if (preg_match('/^(.+?)\s+(\d+)$/', $originalSchoolname, $matches)) {
                $base = $matches[1];
                $existingNumber = (int)$matches[2];
                $schoolname = $base . ' ' . ($existingNumber + ($counter - 1));
            } else {
                $schoolname = $originalSchoolname . ' ' . $counter;
            }

            # Kurzname: "ptsgaenserndorf" → "ptsgaenserndorf2"
            if (preg_match('/^(.+?)(\d+)$/', $originalKurzname, $matches)) {
                $base = $matches[1];
                $existingNumber = (int)$matches[2];
                $kurzname = $base . ($existingNumber + ($counter - 1));
            } else {
                $kurzname = $originalKurzname . $counter;
            }

            $counter++;

            # Sicherheits-Break (sollte nie nötig sein)
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
     * Generiert nächstes freies IBAN-Prefix
     * Format: AT99 → AT98 → ... → AT01 → BT99 → BT98 → ... → ZT01
     *
     * @return string
     */
    private function _generateIbanPrefix()
    {
        # Alle verwendeten Prefixes holen
        $usedPrefixes = $this->Schools->find()
            ->select(['ibanprefix'])
            ->order(['ibanprefix' => 'ASC'])
            ->extract('ibanprefix')
            ->toArray();

        # Durchlaufe Buchstaben A-Z
        for ($letter = 'A'; $letter <= 'Z'; $letter++) {
            # Durchlaufe Nummern 99 bis 01 (absteigend)
            for ($number = 99; $number >= 1; $number--) {
                $prefix = $letter . 'T' . str_pad($number, 2, '0', STR_PAD_LEFT);

                # Wenn dieser Prefix noch nicht verwendet wird, zurückgeben
                if (!in_array($prefix, $usedPrefixes)) {
                    return $prefix;
                }
            }
        }

        # Fallback (sollte nie erreicht werden, da 26*99 = 2574 Möglichkeiten)
        return 'ZT01';
    }

    /**
     * Erstellt Schuladmin-User direkt bei Registrierung
     *
     * @param \App\Model\Entity\School $school
     * @return string|bool Passwort wenn erfolgreich, false bei Fehler
     */
    private function _createSchoolAdminOnRegister($school)
    {
        $this->loadModel('Users');

        # Username: admin-{kurzname}
        $username = 'admin-' . $school->kurzname;

        # Check ob User bereits existiert
        $existingUser = $this->Users->find()
            ->where(['username' => $username])
            ->first();

        if ($existingUser) {
            return false; // User existiert bereits
        }

        # Standard-Passwort für Schuladmins aus Umgebungsvariable
        $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');

        # User erstellen
        $user = $this->Users->newEntity([
            'username' => $username,
            'password' => $password,
            'name' => $school->name,  // Schulname (z.B. "PTS Gänserndörf")
            'role' => 'admin',
            'school_id' => $school->id,
            'active' => 1,
            'admin' => 0  // Nicht Superadmin, nur Schuladmin
        ]);

        if ($this->Users->save($user)) {
            return $password;
        }

        return false;
    }

    /**
     * Erstellt Schuladmin-User für genehmigte Schule (wird bei approve() verwendet)
     *
     * @param \App\Model\Entity\School $school
     * @return bool
     */
    private function _createSchoolAdmin($school)
    {
        $this->loadModel('Users');

        # Username: admin-{kurzname}
        $username = 'admin-' . $school->kurzname;

        # Check ob User bereits existiert
        $existingUser = $this->Users->find()
            ->where(['username' => $username])
            ->first();

        if ($existingUser) {
            return false; // User existiert bereits
        }

        # Standard-Passwort für Schuladmins aus Umgebungsvariable
        $password = env('DEFAULT_ADMIN_PASSWORD', 'ChangeMe123');
        # User erstellen
        $user = $this->Users->newEntity([
            'username' => $username,
            'password' => $password,
            'name' => $school->contact_person ?: 'Schuladministrator',
            'role' => 'admin',
            'school_id' => $school->id
        ]);

        if ($this->Users->save($user)) {
            # TODO: E-Mail mit Zugangsdaten senden
            # Für jetzt: Passwort im Flash speichern
            $this->Flash->success(
                __('Admin-Account erstellt: Username = {0}, Passwort = {1}', $username, $password)
            );
            return true;
        }

        return false;
    }

}
