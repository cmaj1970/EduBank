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
            if (in_array($this->request->getParam('action'), ['index', 'view', 'history', 'directory', 'statement', 'partners'])) {
                return true;
            } else {
                return false;
            }
        }
        # Admins dürfen auch CSV exportieren
        if (isset($user['role']) && $user['role'] === 'admin') {
            if (in_array($this->request->getParam('action'), ['exportPartnersCsv'])) {
                return true;
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

                # Filter nach Übungsfirma (Dropdown)
                $selectedUser = $this->request->getQuery('user_id');
                if ($selectedUser) {
                    $query->where(['Accounts.user_id' => $selectedUser]);
                }
            }

            # Textsuche (Übungsfirma, Kontoname, IBAN)
            $search = $this->request->getQuery('search');
            if ($search) {
                $query->matching('Users', function ($q) use ($search) {
                    return $q;
                })->where([
                    'OR' => [
                        'Users.name LIKE' => '%' . $search . '%',
                        'Accounts.name LIKE' => '%' . $search . '%',
                        'Accounts.iban LIKE' => '%' . $search . '%'
                    ]
                ]);
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

        $accounts = $this->paginate($query);

        # Übungsfirmen für Dropdown (nur für Superadmin)
        $userList = [];
        $isSuperadmin = !$this->school;
        if ($isSuperadmin) {
            $userList = $this->Accounts->Users->find('list')
                ->where(['role' => 'user'])
                ->order('name')
                ->toArray();
        }

        $selectedUser = $this->request->getQuery('user_id');
        $search = $this->request->getQuery('search');

        $this->set(compact('accounts', 'isSuperadmin', 'userList', 'selectedUser', 'search'));
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
     * Statement method - show printable bank statement
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|void
     */
    public function statement($id = null) {
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
            if ($to->account_id == $account->id) {
                $account->balance -= $to->betrag;
            } else {
                $account->balance += $to->betrag;
            }
        }

        $this->set('account', $account);
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
        $fixedUserId = $this->request->getQuery('user_id');
        $fixedUser = null;

        if ($this->request->is('post')) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData());
            if ($this->Accounts->save($account)) {
                # Beispieldaten erstellen wenn gewünscht
                if ($this->request->getData('prefill_sample_data')) {
                    $txCount = $this->_prefillAccountWithSampleData($account->id);
                    $this->Flash->success(__('Konto wurde erstellt ({0} Beispieltransaktionen).', $txCount));
                } else {
                    $this->Flash->success(__('Konto wurde erstellt.'));
                }

                # Zurück zur Übungsfirma wenn user_id übergeben wurde
                $redirectUserId = $this->request->getData('redirect_user_id');
                if ($redirectUserId && $this->school) {
                    return $this->redirect(['controller' => 'Users', 'action' => 'view', $redirectUserId]);
                }
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Konto konnte nicht erstellt werden.'));
        }

        $conditions = [];
        if ($this->school) {
            $conditions = ['school_id' => $this->school['id'], 'role' => 'user'];
            $account->iban = $this->school['ibanprefix'] . rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
            $account->bic = $this->school['bic'];

            # Fixierte Übungsfirma laden wenn user_id Parameter vorhanden
            if ($fixedUserId) {
                $fixedUser = $this->Accounts->Users->find()
                    ->where(['id' => $fixedUserId, 'school_id' => $this->school['id']])
                    ->first();
                if ($fixedUser) {
                    $account->user_id = $fixedUserId;
                }
            }
        }

        $all_users = $this->Accounts->Users->find('all', ['contain' => ['Accounts'], 'limit' => 200])->where($conditions);
        $users = [];
        foreach ($all_users as $user) {
            if (empty($user->accounts)) {
                $users[$user->id] = $user->name;
            }
        }

        # Bei fixierter Übungsfirma auch diese in die Liste aufnehmen (auch wenn schon Konto vorhanden)
        if ($fixedUser && !isset($users[$fixedUser->id])) {
            $users[$fixedUser->id] = $fixedUser->name;
        }

        $this->set(compact('account', 'users', 'fixedUser', 'fixedUserId'));
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

        # Anzahl der Transaktionen für dieses Konto zählen
        $transactionCount = $this->Accounts->Transactions->find()
            ->where([
                'OR' => [
                    'account_id' => $id,
                    'empfaenger_iban' => $account->iban
                ]
            ])
            ->count();

        if ($this->request->is(['patch', 'post', 'put'])) {
            # Balance-Änderung nur erlauben wenn keine Transaktionen existieren
            $data = $this->request->getData();
            if ($transactionCount > 0 && isset($data['balance'])) {
                unset($data['balance']);
            }
            $account = $this->Accounts->patchEntity($account, $data);
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('Konto wurde gespeichert.'));

                # Zurück zur Übungsfirma wenn redirect_user_id übergeben wurde
                $redirectUserId = $this->request->getQuery('redirect_user_id');
                if ($redirectUserId && $this->school) {
                    return $this->redirect(['controller' => 'Users', 'action' => 'view', $redirectUserId]);
                }
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Konto konnte nicht gespeichert werden.'));
        }
        $conditions = [];
        if($this->school) {
            $conditions = ['school_id' => $this->school['id'], 'role' => 'user'];
        }
        $users = $this->Accounts->Users->find('list', ['limit' => 200])->where($conditions);
        $this->set(compact('account', 'users', 'transactionCount'));
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
            $this->Flash->success(__('Konto wurde gelöscht.'));
        } else {
            $this->Flash->error(__('Konto konnte nicht gelöscht werden.'));
        }

        # Zurück zur Übungsfirma wenn redirect_user_id übergeben wurde
        $redirectUserId = $this->request->getQuery('redirect_user_id');
        if ($redirectUserId && $this->school) {
            return $this->redirect(['controller' => 'Users', 'action' => 'view', $redirectUserId]);
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reset method - reset account to initial state
     * Deletes all transactions and resets balance/limit
     * Optional: prefill with sample transactions
     *
     * @param string|null $id Account id.
     * @return \Cake\Http\Response|null Redirects to edit page.
     */
    public function reset($id = null) {
        $this->request->allowMethod(['post']);
        $account = $this->Accounts->find('all', ['contain' => ['Transactions', 'Users']])->where(['Accounts.id' => $id])->first();

        if (!$account) {
            $this->Flash->error(__('Konto nicht gefunden.'));
            return $this->redirect(['action' => 'index']);
        }

        # Schuladmin darf nur Konten seiner Schule zurücksetzen
        if ($this->school && $account->user->school_id != $this->school['id']) {
            $this->Flash->error(__('Sie können nur Konten Ihrer eigenen Schule zurücksetzen.'));
            return $this->redirect(['action' => 'index']);
        }

        # Alle Transaktionen löschen
        $this->Accounts->Transactions->deleteAll(['account_id' => $id]);
        $this->Accounts->Transactions->deleteAll(['empfaenger_iban' => $account->iban]);

        # Balance zurücksetzen
        $account->balance = 10000;
        $account->maxlimit = 2000;

        if ($this->Accounts->save($account)) {
            # Prefill wenn gewünscht
            $prefill = $this->request->getData('prefill');
            if ($prefill) {
                $txCount = $this->_prefillAccountWithSampleData($account->id);
                $this->Flash->success(__('Konto zurückgesetzt und mit {0} Beispieltransaktionen befüllt.', $txCount));
            } else {
                $this->Flash->success(__('Konto wurde auf Startwerte zurückgesetzt.'));
            }
        } else {
            $this->Flash->error(__('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es noch einmal.'));
        }

        return $this->redirect(['action' => 'edit', $id]);
    }

    /**
     * Prefill account with sample transactions
     * Uses system accounts as transaction partners
     *
     * @param int $accountId The account to prefill
     * @return int Number of transactions created
     */
    private function _prefillAccountWithSampleData($accountId)
    {
        $this->loadModel('Partners');

        # Account laden
        $account = $this->Accounts->get($accountId);

        # Partnerunternehmen laden
        $partners = $this->Partners->find()->toArray();

        if (empty($partners)) {
            return 0;
        }

        # Verwendungszwecke passend zu den Geschäftspartnern
        $partnerTemplates = [
            'Bürobedarf Mustermann GmbH' => [
                ['min' => 30, 'max' => 120, 'text' => 'Druckerpapier A4'],
                ['min' => 50, 'max' => 200, 'text' => 'Büromaterial Bestellung'],
                ['min' => 20, 'max' => 80, 'text' => 'Schreibwaren'],
                ['min' => 40, 'max' => 150, 'text' => 'Ordner und Mappen'],
            ],
            'Druckerei Gutenberg OG' => [
                ['min' => 150, 'max' => 400, 'text' => 'Visitenkarten 500 Stk'],
                ['min' => 200, 'max' => 600, 'text' => 'Flyer Druck'],
                ['min' => 100, 'max' => 300, 'text' => 'Briefpapier Bestellung'],
                ['min' => 80, 'max' => 250, 'text' => 'Stempel und Formulare'],
            ],
            'IT-Service Fischer KEG' => [
                ['min' => 80, 'max' => 200, 'text' => 'PC-Wartung'],
                ['min' => 150, 'max' => 400, 'text' => 'Software-Lizenz'],
                ['min' => 100, 'max' => 350, 'text' => 'Netzwerk-Support'],
                ['min' => 200, 'max' => 800, 'text' => 'Hardware-Reparatur'],
            ],
            'Reinigung Sauber & Co' => [
                ['min' => 120, 'max' => 280, 'text' => 'Büroreinigung Monat'],
                ['min' => 80, 'max' => 180, 'text' => 'Fensterreinigung'],
                ['min' => 150, 'max' => 350, 'text' => 'Grundreinigung'],
            ],
            'Catering Lecker GmbH' => [
                ['min' => 100, 'max' => 300, 'text' => 'Mittagsmenüs'],
                ['min' => 200, 'max' => 500, 'text' => 'Firmenfeier Catering'],
                ['min' => 50, 'max' => 150, 'text' => 'Besprechungs-Snacks'],
            ],
            'Versicherung Sicher AG' => [
                ['min' => 200, 'max' => 500, 'text' => 'Betriebshaftpflicht Quartal'],
                ['min' => 150, 'max' => 400, 'text' => 'Inventarversicherung'],
                ['min' => 100, 'max' => 300, 'text' => 'Rechtsschutz Prämie'],
            ],
            'Werbung Kreativ OG' => [
                ['min' => 150, 'max' => 400, 'text' => 'Logo-Design'],
                ['min' => 100, 'max' => 300, 'text' => 'Social Media Kampagne'],
                ['min' => 80, 'max' => 250, 'text' => 'Werbebanner'],
                ['min' => 50, 'max' => 180, 'text' => 'Werbegeschenke'],
            ],
            'Möbel Modern GmbH' => [
                ['min' => 200, 'max' => 600, 'text' => 'Bürostuhl ergonomisch'],
                ['min' => 300, 'max' => 800, 'text' => 'Schreibtisch'],
                ['min' => 150, 'max' => 400, 'text' => 'Aktenschrank'],
                ['min' => 100, 'max' => 300, 'text' => 'Besprechungstisch'],
            ],
            'Elektro Blitz KEG' => [
                ['min' => 80, 'max' => 200, 'text' => 'Leuchtmittel'],
                ['min' => 150, 'max' => 400, 'text' => 'Steckdosen-Installation'],
                ['min' => 100, 'max' => 300, 'text' => 'Elektro-Reparatur'],
            ],
            'Transport Schnell GmbH' => [
                ['min' => 50, 'max' => 150, 'text' => 'Paketversand'],
                ['min' => 100, 'max' => 300, 'text' => 'Warenlieferung'],
                ['min' => 80, 'max' => 250, 'text' => 'Express-Transport'],
            ],
        ];

        # Generische Einnahmen-Templates (neutrale Verwendungszwecke)
        $einnahmenTemplates = [
            ['min' => 150, 'max' => 800, 'format' => 'Zahlung RE-%d'],
            ['min' => 200, 'max' => 600, 'format' => 'Rechnung %d'],
            ['min' => 100, 'max' => 500, 'format' => 'Zahlungseingang %d'],
            ['min' => 150, 'max' => 400, 'format' => 'Überweisung'],
            ['min' => 200, 'max' => 700, 'format' => 'Zahlung Auftrag %d'],
            ['min' => 100, 'max' => 350, 'format' => 'Bestellung %d'],
        ];

        $created = 0;
        $numTransactions = rand(12, 18);
        $transactions = [];
        $summeAusgaben = 0;
        $summeEinnahmen = 0;

        # Erst alle Transaktionen generieren (85% Ausgaben, 15% Einnahmen)
        for ($i = 0; $i < $numTransactions; $i++) {
            $isAusgabe = (rand(1, 100) <= 85);
            $partner = $partners[array_rand($partners)];
            $partnerName = $partner->name;

            if ($isAusgabe) {
                # Passenden Verwendungszweck für diesen Partner wählen
                if (isset($partnerTemplates[$partnerName])) {
                    $templates = $partnerTemplates[$partnerName];
                    $template = $templates[array_rand($templates)];
                } else {
                    $template = ['min' => 50, 'max' => 200, 'text' => 'Rechnung ' . $partner->description];
                }
                $betrag = rand($template['min'] * 100, $template['max'] * 100) / 100;
                $verwendung = $template['text'];

                $transactions[] = [
                    'type' => 'ausgabe',
                    'data' => [
                        'account_id' => $accountId,
                        'empfaenger_name' => $partner->name,
                        'empfaenger_iban' => $partner->iban,
                        'empfaenger_bic' => $partner->bic,
                        'betrag' => $betrag,
                        'zahlungszweck' => $verwendung,
                        'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                    ]
                ];
                $summeAusgaben += $betrag;
            } else {
                # Eingang: Generische Verwendungszwecke
                $template = $einnahmenTemplates[array_rand($einnahmenTemplates)];
                $betrag = rand($template['min'] * 100, $template['max'] * 100) / 100;
                # Format mit oder ohne Nummer
                if (strpos($template['format'], '%d') !== false) {
                    $verwendung = sprintf($template['format'], rand(1000, 9999));
                } else {
                    $verwendung = $template['format'];
                }

                # Einnahme: Eingang auf dem Konto (negativer Betrag = Gutschrift)
                $transactions[] = [
                    'type' => 'einnahme',
                    'data' => [
                        'account_id' => $accountId,
                        'empfaenger_name' => 'Zahlungseingang',
                        'empfaenger_iban' => '',
                        'empfaenger_bic' => '',
                        'betrag' => -$betrag, # Negativ = Eingang
                        'zahlungszweck' => $verwendung,
                        'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                    ]
                ];
                $summeEinnahmen += $betrag;
            }
        }

        # Differenz berechnen und Ausgleichstransaktion hinzufügen
        $differenz = round($summeAusgaben - $summeEinnahmen, 2);
        if (abs($differenz) > 0.01) {
            if ($differenz > 0) {
                # Mehr Ausgaben als Einnahmen → eine Einnahme hinzufügen
                $transactions[] = [
                    'type' => 'einnahme',
                    'data' => [
                        'account_id' => $accountId,
                        'empfaenger_name' => 'Zahlungseingang',
                        'empfaenger_iban' => '',
                        'empfaenger_bic' => '',
                        'betrag' => -$differenz, # Negativ = Eingang
                        'zahlungszweck' => 'Zahlung RE-' . rand(1000, 9999),
                        'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                    ]
                ];
            } else {
                # Mehr Einnahmen als Ausgaben → eine Ausgabe hinzufügen
                $partner = $partners[array_rand($partners)];
                $partnerName = $partner->name;

                if (isset($partnerTemplates[$partnerName])) {
                    $templates = $partnerTemplates[$partnerName];
                    $template = $templates[array_rand($templates)];
                    $verwendung = $template['text'];
                } else {
                    $verwendung = 'Rechnung ' . $partner->description;
                }

                $transactions[] = [
                    'type' => 'ausgabe',
                    'data' => [
                        'account_id' => $accountId,
                        'empfaenger_name' => $partner->name,
                        'empfaenger_iban' => $partner->iban,
                        'empfaenger_bic' => $partner->bic,
                        'betrag' => abs($differenz),
                        'zahlungszweck' => $verwendung,
                        'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                    ]
                ];
            }
        }

        # Alle Transaktionen speichern
        foreach ($transactions as $tx) {
            $entity = $this->Accounts->Transactions->newEntity($tx['data']);
            if ($this->Accounts->Transactions->save($entity)) {
                $created++;
            }
        }

        return $created;
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

        // Get all approved schools for dropdown
        $schools = $this->Schools->find('all')
            ->where(['status' => 'approved'])
            ->order(['name' => 'ASC'])
            ->toArray();
        $schoolList = $this->Schools->find('list')
            ->where(['status' => 'approved'])
            ->order('name')
            ->toArray();

        // Get filter values
        $selectedSchool = $this->request->getQuery('school_id');
        $search = $this->request->getQuery('search');

        // Get all practice companies (users with role 'user') with their accounts
        $query = $this->Users->find('all')
            ->contain(['Accounts', 'Schools'])
            ->where(['Users.role' => 'user', 'Users.active' => 1])
            ->order(['Schools.name' => 'ASC', 'Users.name' => 'ASC']);

        // For non-admins, only show companies from approved schools
        $query->matching('Schools', function ($q) {
            return $q->where(['Schools.status' => 'approved']);
        });

        # Filter nach Schule (Dropdown)
        if ($selectedSchool) {
            $query->where(['Users.school_id' => $selectedSchool]);
        }

        # Textsuche (Schulname, Firmenname, IBAN)
        if ($search) {
            # Suche in: Schulname, Firmenname (User), Kontoname, IBAN
            $query->leftJoinWith('Accounts');
            $query->where([
                'OR' => [
                    'Users.name LIKE' => '%' . $search . '%',
                    'Schools.name LIKE' => '%' . $search . '%',
                    'Accounts.iban LIKE' => '%' . $search . '%',
                    'Accounts.name LIKE' => '%' . $search . '%'
                ]
            ]);
        }

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

        // AJAX Request: JSON zurückgeben
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->response = $this->response->withType('application/json');

            $results = [];
            foreach ($companiesBySchool as $schoolId => $schoolData) {
                $schoolResult = [
                    'school_id' => $schoolId,
                    'school_name' => $schoolData['school']->name,
                    'companies' => []
                ];
                foreach ($schoolData['companies'] as $company) {
                    $companyData = [
                        'name' => $company->name,
                        'accounts' => []
                    ];
                    if (!empty($company->accounts)) {
                        foreach ($company->accounts as $account) {
                            $companyData['accounts'][] = [
                                'name' => $account->name,
                                'iban' => $account->iban,
                                'bic' => $account->bic
                            ];
                        }
                    }
                    $schoolResult['companies'][] = $companyData;
                }
                $results[] = $schoolResult;
            }

            $this->response = $this->response->withStringBody(json_encode(['results' => $results]));
            return $this->response;
        }

        $this->set(compact('companiesBySchool', 'schoolList', 'selectedSchool', 'search'));
    }

    /**
     * Partners method - show all system accounts (business partners)
     * Available to all users for transfer recipients
     *
     * @return \Cake\Http\Response|void
     */
    public function partners()
    {
        $this->loadModel('Partners');

        # Partner nach Branchen gruppiert laden
        $groupedPartners = $this->Partners->getGroupedByBranch();

        # Statistik für Admin-Block
        $partnerCount = $this->Partners->find()->count();

        # Berechtigungen
        $isSuperadmin = ($this->Auth->user('username') === 'admin');
        $isAdmin = ($this->Auth->user('role') === 'admin');

        $this->set(compact('groupedPartners', 'partnerCount', 'isSuperadmin', 'isAdmin'));
    }

    /**
     * Export partners as CSV for Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportPartnersCsv()
    {
        $this->loadModel('Partners');

        $partners = $this->Partners->find()
            ->order(['branch' => 'ASC', 'name' => 'ASC'])
            ->toArray();

        # CSV Header (Excel-kompatibel mit BOM für UTF-8)
        $csv = "\xEF\xBB\xBF"; # UTF-8 BOM für Excel
        $csv .= "Branche;Firmenname;IBAN;BIC;Beschreibung\n";

        foreach ($partners as $partner) {
            $csv .= sprintf(
                "%s;%s;%s;%s;%s\n",
                $this->_csvEscape($partner->branch),
                $this->_csvEscape($partner->name),
                $partner->iban,
                $partner->bic,
                $this->_csvEscape($partner->description ?? '')
            );
        }

        $this->response = $this->response
            ->withType('text/csv')
            ->withCharset('UTF-8')
            ->withHeader('Content-Disposition', 'attachment; filename="Partnerunternehmen.csv"')
            ->withStringBody($csv);

        return $this->response;
    }

    /**
     * Escape CSV value
     */
    private function _csvEscape($value)
    {
        # Semikolons und Anführungszeichen escapen
        if (strpos($value, ';') !== false || strpos($value, '"') !== false) {
            return '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    /**
     * Create default partners (Superadmin only)
     * Creates 25 partner companies in 5 branches
     *
     * @return \Cake\Http\Response
     */
    public function createPartners()
    {
        $this->request->allowMethod(['post']);

        # Nur Superadmin
        if ($this->Auth->user('username') !== 'admin') {
            $this->Flash->error(__('Keine Berechtigung.'));
            return $this->redirect(['action' => 'partners']);
        }

        $this->loadModel('Partners');

        # Prüfen ob bereits Partner existieren
        if ($this->Partners->find()->count() > 0) {
            $this->Flash->error(__('Partnerunternehmen existieren bereits.'));
            return $this->redirect(['action' => 'partners']);
        }

        # 25 Partnerunternehmen (5 Branchen × 5 Firmen)
        $partnersData = [
            # Büro & Ausstattung
            ['name' => 'Bürobedarf Mustermann GmbH', 'branch' => 'Büro & Ausstattung', 'description' => 'Büromaterial, Schreibwaren'],
            ['name' => 'Möbel Modern GmbH', 'branch' => 'Büro & Ausstattung', 'description' => 'Büroeinrichtung, Möbel'],
            ['name' => 'Technik-Partner KEG', 'branch' => 'Büro & Ausstattung', 'description' => 'Computer, Hardware'],
            ['name' => 'Papier & Druck OG', 'branch' => 'Büro & Ausstattung', 'description' => 'Kopierpapier, Formulare'],
            ['name' => 'Kantinenbedarf Weber', 'branch' => 'Büro & Ausstattung', 'description' => 'Kaffee, Getränke'],
            # Dienstleistungen
            ['name' => 'IT-Service Fischer KEG', 'branch' => 'Dienstleistungen', 'description' => 'EDV-Support, Software'],
            ['name' => 'Steuerberatung Huber', 'branch' => 'Dienstleistungen', 'description' => 'Buchhaltung, Jahresabschluss'],
            ['name' => 'Reinigung Sauber & Co', 'branch' => 'Dienstleistungen', 'description' => 'Büroreinigung'],
            ['name' => 'Personalberatung Schmidt', 'branch' => 'Dienstleistungen', 'description' => 'Recruiting, Schulungen'],
            ['name' => 'Gebäudeservice Müller', 'branch' => 'Dienstleistungen', 'description' => 'Reparaturen, Wartung'],
            # Marketing & Kommunikation
            ['name' => 'Druckerei Gutenberg OG', 'branch' => 'Marketing & Kommunikation', 'description' => 'Visitenkarten, Flyer'],
            ['name' => 'Werbeagentur Kreativ', 'branch' => 'Marketing & Kommunikation', 'description' => 'Kampagnen, Design'],
            ['name' => 'Web & Media Solutions', 'branch' => 'Marketing & Kommunikation', 'description' => 'Website, Social Media'],
            ['name' => 'Messebau Express', 'branch' => 'Marketing & Kommunikation', 'description' => 'Messestände, Roll-ups'],
            ['name' => 'Fotostudio Bildschön', 'branch' => 'Marketing & Kommunikation', 'description' => 'Produktfotos, Teamfotos'],
            # Versicherungen & Finanzen
            ['name' => 'Betriebsversicherung Austria AG', 'branch' => 'Versicherungen & Finanzen', 'description' => 'Betriebshaftpflicht'],
            ['name' => 'Sachversicherung Sicher GmbH', 'branch' => 'Versicherungen & Finanzen', 'description' => 'Inventar, Gebäude'],
            ['name' => 'Rechtsschutz Direkt', 'branch' => 'Versicherungen & Finanzen', 'description' => 'Rechtsschutzversicherung'],
            ['name' => 'Unfallversicherung Plus', 'branch' => 'Versicherungen & Finanzen', 'description' => 'Mitarbeiter-Unfallschutz'],
            ['name' => 'Kreditversicherung Trust', 'branch' => 'Versicherungen & Finanzen', 'description' => 'Forderungsausfall'],
            # Logistik & Infrastruktur
            ['name' => 'Transport Schnell GmbH', 'branch' => 'Logistik & Infrastruktur', 'description' => 'Paketversand, Spedition'],
            ['name' => 'Elektro Blitz KEG', 'branch' => 'Logistik & Infrastruktur', 'description' => 'Elektroinstallation'],
            ['name' => 'Telekom Business', 'branch' => 'Logistik & Infrastruktur', 'description' => 'Internet, Telefon'],
            ['name' => 'Energie Austria', 'branch' => 'Logistik & Infrastruktur', 'description' => 'Strom, Gas'],
            ['name' => 'Entsorgung Grün', 'branch' => 'Logistik & Infrastruktur', 'description' => 'Müllabfuhr, Recycling'],
        ];

        $created = 0;
        foreach ($partnersData as $data) {
            # Zufällige IBAN generieren (SY + 18 Ziffern)
            $randomPart = '';
            for ($i = 0; $i < 18; $i++) {
                $randomPart .= mt_rand(0, 9);
            }
            $randomPart[0] = mt_rand(1, 9); # Erste Ziffer nicht 0

            $partner = $this->Partners->newEntity([
                'name' => $data['name'],
                'iban' => 'SY' . $randomPart,
                'bic' => 'EDUBANKSYS',
                'branch' => $data['branch'],
                'description' => $data['description']
            ]);

            if ($this->Partners->save($partner)) {
                $created++;
            }
        }

        $this->Flash->success(__('Partnerunternehmen erstellt: {0} Firmen in 5 Branchen.', $created));
        return $this->redirect(['action' => 'partners']);
    }

    /**
     * Delete all partners (Superadmin only)
     *
     * @return \Cake\Http\Response
     */
    public function deletePartners()
    {
        $this->request->allowMethod(['post']);

        # Nur Superadmin
        if ($this->Auth->user('username') !== 'admin') {
            $this->Flash->error(__('Keine Berechtigung.'));
            return $this->redirect(['action' => 'partners']);
        }

        $this->loadModel('Partners');

        $count = $this->Partners->find()->count();
        if ($count === 0) {
            $this->Flash->error(__('Keine Partnerunternehmen vorhanden.'));
            return $this->redirect(['action' => 'partners']);
        }

        # Alle Partner löschen
        $this->Partners->deleteAll([]);

        $this->Flash->success(__('Alle {0} Partnerunternehmen wurden gelöscht.', $count));
        return $this->redirect(['action' => 'partners']);
    }
}
