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
            $this->Flash->success(__('The account has been deleted.'));
        } else {
            $this->Flash->error(__('The account could not be deleted. Please, try again.'));
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
        $this->loadModel('Schools');

        # Account laden
        $account = $this->Accounts->get($accountId);

        # System-Konten laden
        $systemSchool = $this->Schools->find()
            ->where(['kurzname' => 'system'])
            ->first();

        if (!$systemSchool) {
            return 0;
        }

        $systemAccounts = $this->Accounts->find()
            ->contain(['Users'])
            ->where(['Users.school_id' => $systemSchool->id])
            ->toArray();

        if (empty($systemAccounts)) {
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

        # Einnahmen-Templates mit firmenspezifischen Rechnungsnummern-Formaten
        $einnahmenTemplates = [
            'Bürobedarf Mustermann GmbH' => [
                ['min' => 150, 'max' => 400, 'format' => 'BM-%d/24'],
                ['min' => 200, 'max' => 600, 'format' => 'Gutschrift BM-%d'],
            ],
            'Druckerei Gutenberg OG' => [
                ['min' => 200, 'max' => 500, 'format' => 'DG-2024-%04d'],
                ['min' => 100, 'max' => 300, 'format' => 'Rg. %d Gutenberg'],
            ],
            'IT-Service Fischer KEG' => [
                ['min' => 300, 'max' => 800, 'format' => 'ISF-%d'],
                ['min' => 150, 'max' => 400, 'format' => 'Wartungsvertrag %d'],
            ],
            'Reinigung Sauber & Co' => [
                ['min' => 100, 'max' => 250, 'format' => 'RS/24/%d'],
                ['min' => 80, 'max' => 200, 'format' => 'Sauber-Nr. %d'],
            ],
            'Catering Lecker GmbH' => [
                ['min' => 200, 'max' => 600, 'format' => 'CL-2024-%d'],
                ['min' => 150, 'max' => 400, 'format' => 'Event-Abr. %d'],
            ],
            'Versicherung Sicher AG' => [
                ['min' => 100, 'max' => 300, 'format' => 'VS-POL-%06d'],
                ['min' => 150, 'max' => 400, 'format' => 'Schadensfall %d'],
            ],
            'Werbung Kreativ OG' => [
                ['min' => 200, 'max' => 500, 'format' => 'WK/%d/24'],
                ['min' => 100, 'max' => 300, 'format' => 'Kreativ-Ref %d'],
            ],
            'Möbel Modern GmbH' => [
                ['min' => 300, 'max' => 800, 'format' => 'MM-RG-%05d'],
                ['min' => 200, 'max' => 500, 'format' => 'Rückgabe %d'],
            ],
            'Elektro Blitz KEG' => [
                ['min' => 100, 'max' => 300, 'format' => 'EB-24-%d'],
                ['min' => 80, 'max' => 200, 'format' => 'Blitz-Service %d'],
            ],
            'Transport Schnell GmbH' => [
                ['min' => 80, 'max' => 200, 'format' => 'TS-FRACHT-%d'],
                ['min' => 100, 'max' => 300, 'format' => 'Schnell-Nr %d'],
            ],
        ];

        $created = 0;
        $numTransactions = rand(12, 18);
        $transactions = [];
        $summeAusgaben = 0;
        $summeEinnahmen = 0;

        # Erst alle Transaktionen generieren
        for ($i = 0; $i < $numTransactions; $i++) {
            $isAusgabe = (rand(1, 100) <= 70);
            $partner = $systemAccounts[array_rand($systemAccounts)];
            $partnerName = $partner->user->name;

            if ($isAusgabe) {
                # Passenden Verwendungszweck für diesen Partner wählen
                if (isset($partnerTemplates[$partnerName])) {
                    $templates = $partnerTemplates[$partnerName];
                    $template = $templates[array_rand($templates)];
                } else {
                    $template = ['min' => 50, 'max' => 200, 'text' => 'Rechnung'];
                }
                $betrag = rand($template['min'] * 100, $template['max'] * 100) / 100;
                $verwendung = $template['text'];

                $transactions[] = [
                    'type' => 'ausgabe',
                    'data' => [
                        'account_id' => $accountId,
                        'empfaenger_name' => $partner->user->name,
                        'empfaenger_iban' => $partner->iban,
                        'empfaenger_bic' => $partner->bic,
                        'betrag' => $betrag,
                        'zahlungszweck' => $verwendung,
                        'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                    ]
                ];
                $summeAusgaben += $betrag;
            } else {
                # Eingang: Firmenspezifische Rechnungsnummer
                if (isset($einnahmenTemplates[$partnerName])) {
                    $templates = $einnahmenTemplates[$partnerName];
                    $template = $templates[array_rand($templates)];
                    $betrag = rand($template['min'] * 100, $template['max'] * 100) / 100;
                    $verwendung = sprintf($template['format'], rand(1000, 9999));
                } else {
                    $betrag = rand(100, 500);
                    $verwendung = 'Zahlung ' . rand(1000, 9999);
                }

                $transactions[] = [
                    'type' => 'einnahme',
                    'partner' => $partner,
                    'data' => [
                        'account_id' => $partner->id,
                        'empfaenger_name' => $account->name,
                        'empfaenger_iban' => $account->iban,
                        'empfaenger_bic' => $account->bic ?? '',
                        'betrag' => $betrag,
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
            $partner = $systemAccounts[array_rand($systemAccounts)];
            $partnerName = $partner->user->name;

            if ($differenz > 0) {
                # Mehr Ausgaben als Einnahmen → eine Einnahme hinzufügen
                if (isset($einnahmenTemplates[$partnerName])) {
                    $templates = $einnahmenTemplates[$partnerName];
                    $template = $templates[array_rand($templates)];
                    $verwendung = sprintf($template['format'], rand(1000, 9999));
                } else {
                    $verwendung = 'Ausgleich ' . rand(1000, 9999);
                }

                $transactions[] = [
                    'type' => 'einnahme',
                    'partner' => $partner,
                    'data' => [
                        'account_id' => $partner->id,
                        'empfaenger_name' => $account->name,
                        'empfaenger_iban' => $account->iban,
                        'empfaenger_bic' => $account->bic ?? '',
                        'betrag' => $differenz,
                        'zahlungszweck' => $verwendung,
                        'datum' => new \DateTime('-' . rand(1, 90) . ' days'),
                    ]
                ];
            } else {
                # Mehr Einnahmen als Ausgaben → eine Ausgabe hinzufügen
                if (isset($partnerTemplates[$partnerName])) {
                    $templates = $partnerTemplates[$partnerName];
                    $template = $templates[array_rand($templates)];
                    $verwendung = $template['text'];
                } else {
                    $verwendung = 'Rechnung';
                }

                $transactions[] = [
                    'type' => 'ausgabe',
                    'data' => [
                        'account_id' => $accountId,
                        'empfaenger_name' => $partner->user->name,
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
        $this->loadModel('Schools');

        # Branchen-Mapping für Geschäftspartner
        $branchMapping = [
            'Bürobedarf Mustermann GmbH' => 'Büro & Einrichtung',
            'Möbel Modern GmbH' => 'Büro & Einrichtung',
            'IT-Service Fischer KEG' => 'Dienstleistungen',
            'Reinigung Sauber & Co' => 'Dienstleistungen',
            'Druckerei Gutenberg OG' => 'Druck & Werbung',
            'Werbung Kreativ OG' => 'Druck & Werbung',
            'Catering Lecker GmbH' => 'Gastronomie',
            'Elektro Blitz KEG' => 'Handwerk',
            'Transport Schnell GmbH' => 'Logistik',
            'Versicherung Sicher AG' => 'Versicherung',
        ];

        # System-Schule finden
        $systemSchool = $this->Schools->find()
            ->where(['kurzname' => 'system'])
            ->first();

        $groupedAccounts = [];
        if ($systemSchool) {
            $systemAccounts = $this->Accounts->find()
                ->contain(['Users'])
                ->where(['Users.school_id' => $systemSchool->id])
                ->order(['Users.name' => 'ASC'])
                ->toArray();

            # Nach Branchen gruppieren
            foreach ($systemAccounts as $account) {
                $branch = $branchMapping[$account->user->name] ?? 'Sonstige';
                if (!isset($groupedAccounts[$branch])) {
                    $groupedAccounts[$branch] = [];
                }
                $groupedAccounts[$branch][] = $account;
            }

            # Branchen alphabetisch sortieren
            ksort($groupedAccounts);
        }

        $this->set(compact('groupedAccounts'));
    }
}
