<?php

namespace App\Controller;

use App\Controller\AppController;

/**
 * Accounts Controller
 *
 * @property \App\Model\Table\AccountsTable $Accounts
 *
 * @method \App\Model\Entity\Account[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AccountsController extends AppController
{
    /**
     * Authorization - who can do what?
     * Users can only view their own accounts
     *
     * @param array $user The logged in user
     * @return bool
     */
    public function isAuthorized($user) {
        if (isset($user['role']) && $user['role'] === 'user') {
            if (in_array($this->request->getParam('action'), ['index', 'view', 'history', 'directory'])) {
                return true;
            } else {
                return false;
            }
        }
        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index() {
        # Neueste zuerst (als Default, kann durch Paginator überschrieben werden)
        $this->paginate = [
            'contain' => ['Users'],
            'order' => ['Accounts.created' => 'DESC']
        ];
        if ($this->Auth->user()['role'] != 'admin') {
            # Schüler: Eigenes Konto suchen und anzeigen
            $account = $this->Accounts->find('all', ['contain' => ['Transactions']])->where(['user_id' => $this->Auth->user()['id']])->first();
            if ($account) {
                return $this->redirect(['action' => 'view', $account->id]);
            } else {
                # Kein Konto vorhanden - leere Seite mit Hinweis
                $this->Flash->warning(__('Sie haben noch kein Konto. Bitte wenden Sie sich an Ihren Schuladministrator.'));
                $this->set('accounts', []);
                return;
            }
        } else {
            if ($this->school) {
                # Schuladmin: Nur Konten von Benutzern dieser Schule anzeigen
                $query = $this->Accounts->find('all')
                    ->contain(['Transactions', 'Users'])
                    ->matching('Users', function ($q) {
                        return $q->where(['Users.school_id' => $this->school['id']]);
                    });
            } else {
                # Superadmin: Alle Konten anzeigen
                $query = $this->Accounts->find('all')
                    ->contain(['Transactions', 'Users']);
            }
        }
        $query->formatResults(function (\Cake\Collection\CollectionInterface $results) {
            return $results->map(function ($row) {
                $row->transactions = $this->Accounts->Transactions->find('all')->where(["datum <= '" . date('Y-m-d') . "'", 'or' => ['empfaenger_iban' => $row->iban, 'account_id' => $row->id]])->order(['created desc']);
                foreach ($row->transactions as $to) {
                    if ($to->account_id == $row->id) {
                        $row['balance'] -= $to->betrag;
                    } else {
                        $row['balance'] += $to->betrag;
                    }
                }
                return $row;
            });
        });
        // Find transfers to this account
        // foreach($result as $k => $account) {
        //     debug($k);
        // }

        $accounts = $this->paginate($query);
        $this->set(compact('accounts'));
    }

    /**
     * View method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null) {
        $account = $this->Accounts->get($id, [
            'contain' => [
                'Users',
                'Transactions' => function ($query) {
                    return $query->order(['created desc']);
                }
            ]
        ]);

        # Zugriffsschutz: Übungsfirma darf nur eigenes Konto sehen
        if ($this->Auth->user()['role'] !== 'admin' && $account->user_id != $this->Auth->user()['id']) {
            return $this->redirect(['action' => 'index']);
        }

        # Schuladmin darf nur Konten seiner Schule sehen
        if ($this->school && $account->user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Konten Ihrer eigenen Schule einsehen.'));
            return $this->redirect(['action' => 'index']);
        }
        // Find transfers to this account
        $account->transactions = $this->Accounts->Transactions->find('all', ['contain' => ['Accounts.Users']])->where(["datum <= '" . date('Y-m-d') . "'", 'or' => ['and' => ['empfaenger_iban' => $account->iban], 'account_id' => $account->id]])->order(['Transactions.datum desc', 'Transactions.id desc']);
        foreach ($account->transactions as $k => $to) {
            $account_transactions[$k] = $to;
            if ($to->account_id == $account->id) {
                $account->balance -= $to->betrag;
            } else {
                $account->balance += $to->betrag;
                // $account_transactions[$k]->auftraggeber_name = $to->account->user->name;
                // $account_transactions[$k]->auftraggeber_adresse = $to->empfaenger_adresse;
                // $account_transactions[$k]->auftraggeber_iban = $to->empfaenger_iban;
                // $account_transactions[$k]->auftraggeber_bic = $to->empfaenger_bic;
            }
        }

        $this->set('account', $account);
        // $this->set('account_transactions', $account_transactions);
    }

    /**
     * History method - show all transactions (including future dated)
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|void
     */
    public function history($id = null) {
        $account = $this->Accounts->get($id, [
            'contain' => [
                'Users',
                'Transactions' => function ($query) {
                    return $query->order(['created desc']);
                }
            ]
        ]);

        # Zugriffsschutz: Übungsfirma darf nur eigene Historie sehen
        if ($this->Auth->user()['role'] !== 'admin' && $account->user_id != $this->Auth->user()['id']) {
            return $this->redirect(['action' => 'index']);
        }

        # Schuladmin darf nur Konten seiner Schule sehen
        if ($this->school && $account->user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Konten Ihrer eigenen Schule einsehen.'));
            return $this->redirect(['action' => 'index']);
        }

        # Kontostand berechnen: Alle durchgeführten Transaktionen (eingehend + ausgehend)
        $allTransactions = $this->Accounts->Transactions->find('all')
            ->where([
                "datum <= '" . date('Y-m-d') . "'",
                'or' => [
                    'empfaenger_iban' => $account->iban,
                    'account_id' => $account->id
                ]
            ]);
        foreach ($allTransactions as $to) {
            if ($to->account_id == $account->id) {
                $account->balance -= $to->betrag;
            } else {
                $account->balance += $to->betrag;
            }
        }

        # Auftragshistorie: Nur ausgehende Transaktionen (alle, auch zukünftige)
        $account->transactions = $this->Accounts->Transactions->find('all', ['contain' => ['Accounts.Users']])
            ->where(['account_id' => $account->id])
            ->order(['Transactions.created desc']);

        $this->set('account', $account);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add() {
        $account = $this->Accounts->newEntity();
        if ($this->request->is('post')) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData());
            // $account->balance = 10000;
            // $account->limit = 2000;
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('The account has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
        $conditions = [];
        if($this->school) {
            $conditions = ['school_id' => $this->school['id'], 'role' => 'user'];
            $account->iban = $this->school['ibanprefix'] . rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
            $account->bic = $this->school['bic'];
        }
        $all_users = $this->Accounts->Users->find('all', ['contain' => ['Accounts'], 'limit' => 200])->where($conditions);
        $users =[];
        foreach($all_users as $user) {
            if(empty($user->accounts)) {
                $users[$user->id] = $user->name;
            }
        }
        $this->set(compact('account', 'users'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null) {
        $account = $this->Accounts->get($id, [
            'contain' => ['Users']
        ]);

        # Schuladmin darf nur Konten seiner Schule bearbeiten
        if ($this->school && $account->user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Konten Ihrer eigenen Schule bearbeiten.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['patch', 'post', 'put'])) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData());
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('The account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
        $conditions = [];
        if($this->school) {
            $conditions = ['school_id' => $this->school['id'], 'role' => 'user'];
        }
        $users = $this->Accounts->Users->find('list', ['limit' => 200])->where($conditions);
        $this->set(compact('account', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $account = $this->Accounts->get($id, [
            'contain' => ['Users']
        ]);

        # Schuladmin darf nur Konten seiner Schule löschen
        if ($this->school && $account->user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Konten Ihrer eigenen Schule löschen.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->Accounts->delete($account)) {
            $this->Flash->success(__('The account has been deleted.'));
        } else {
            $this->Flash->error(__('The account could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reset method - reset account to initial state
     * Deletes all transactions and resets balance/limit
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null Redirects to index.
     */
    public function reset($id = null) {
        $this->request->allowMethod(['post', 'reset']);
        $account = $this->Accounts->find('all', ['contain' => ['Transactions', 'Users']])->where(['Accounts.id' => $id])->first();

        # Schuladmin darf nur Konten seiner Schule zurücksetzen
        if ($this->school && $account->user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Konten Ihrer eigenen Schule zurücksetzen.'));
            return $this->redirect(['action' => 'index']);
        }

        $account->balance = 10000;
        $account->maxlimit = 2000;

        if ($this->Accounts->save($account)) {
            $this->Accounts->Transactions->deleteAll(['account_id' => $id]);
            $this->Accounts->Transactions->deleteAll(['empfaenger_iban' => $account->iban]);
            $this->Flash->success(__('Das Konto wurde zurückgesetzt.'));

        } else {
            $this->Flash->error(__('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es noch einmal.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Directory method - show all practice companies from all schools
     * Shows company names and IBAN/BIC without account balances
     *
     * @return \Cake\Http\Response|void
     */
    public function directory() {
        $this->loadModel('Users');
        $this->loadModel('Schools');

        // Get all approved schools
        $schools = $this->Schools->find('all')
            ->where(['status' => 'approved'])
            ->order(['name' => 'ASC'])
            ->toArray();

        // Get all practice companies (users with role 'user') with their accounts
        $query = $this->Users->find('all')
            ->contain(['Accounts', 'Schools'])
            ->where(['Users.role' => 'user', 'Users.active' => 1])
            ->order(['Schools.name' => 'ASC', 'Users.name' => 'ASC']);

        // For non-admins, only show companies from approved schools
        $query->matching('Schools', function ($q) {
            return $q->where(['Schools.status' => 'approved']);
        });

        $companies = $query->toArray();

        // Group companies by school
        $companiesBySchool = [];
        foreach ($companies as $company) {
            $schoolId = $company->school_id;
            if (!isset($companiesBySchool[$schoolId])) {
                $companiesBySchool[$schoolId] = [
                    'school' => $company->school,
                    'companies' => []
                ];
            }
            $companiesBySchool[$schoolId]['companies'][] = $company;
        }

        $this->set(compact('companiesBySchool'));
    }
}
