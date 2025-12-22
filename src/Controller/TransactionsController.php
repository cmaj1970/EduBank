<?php

	namespace App\Controller;

	use App\Controller\AppController;
	use Cake\I18n\Date;

	/**
	 * Transactions Controller
	 *
	 * @property \App\Model\Table\TransactionsTable $Transactions
	 *
	 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
	 */
	class TransactionsController extends AppController
	{
		/**
		 * Authorization - who can do what?
		 * Users can view, add, storno and check IBAN
		 *
		 * @param array $user The logged in user
		 * @return bool
		 */
		public function isAuthorized($user)
		{
			if (isset($user['role']) && $user['role'] === 'user') {
				if (in_array($this->request->getParam('action'), ['view', 'add', 'storno', 'checkiban', 'searchRecipients'])) {
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
		public function index()
		{
			# Neueste zuerst (als Default, kann durch Paginator überschrieben werden)
			$this->paginate = [
				'contain' => ['Accounts.Users'],
				'order' => ['Transactions.created' => 'DESC']
			];

			if ($this->school) {
				# Schuladmin: Nur Transaktionen von Konten dieser Schule
				$query = $this->Transactions->find('all')
					->contain(['Accounts.Users'])
					->matching('Accounts.Users', function ($q) {
						return $q->where(['Users.school_id' => $this->school['id']]);
					});
				$transactions = $this->paginate($query);
			} else {
				# Superadmin: Alle Transaktionen
				$transactions = $this->paginate($this->Transactions);
			}

			$this->set(compact('transactions'));
		}

		/**
		 * View method
		 *
		 * @param string|null $id Transaction id.
		 * @return \Cake\Http\Response|void
		 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
		 */
		public function view($id = null)
		{
			$transaction = $this->Transactions->get($id, [
				'contain' => ['Accounts.Users']
			]);

			# Zugriffsschutz: Übungsfirma darf nur eigene Transaktionen sehen
			if ($this->Auth->user()['role'] !== 'admin' && $transaction->account->user_id != $this->Auth->user()['id']) {
				return $this->redirect(['action' => 'index']);
			}

			# Schuladmin darf nur Transaktionen seiner Schule sehen
			if ($this->school && $transaction->account->user->school_id != $this->school['id']) {
				$this->Flash->error(__('Sie können nur Transaktionen Ihrer eigenen Schule einsehen.'));
				return $this->redirect(['action' => 'index']);
			}

			$this->set('transaction', $transaction);
		}

		/**
		 * Add method
		 *
		 * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
		 */
		public function add()
		{
			$transaction = $this->Transactions->newEntity();
			// Check transfer limit
			$account = $this->Transactions->Accounts->find('all')->where(['user_id' => $this->Auth->user()['id']])->first();
			$account->transactions = $this->Transactions->find('all')->where(["datum <= '" . date('Y-m-d') . "'", 'or' => ['and' => ['empfaenger_iban' => $account->iban], 'account_id' => $account->id]]);
            foreach ($account->transactions as $k => $to) {
				$account_transactions[$k] = $to;
				if ($to->account_id == $account->id) {
					$account->balance -= $to->betrag;
				} else {
					$account->balance += $to->betrag;
				}
			}
			$max_betrag = round($account->balance + $account->maxlimit, 2);
			$date = new Date();
			$valid_tan = false;
			if ($this->request->is('post')) {
				if($this->request->getData()['tan']%7 == 0) {
					$valid_tan = true;
				}
				if($valid_tan == true) {
                    $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
                    // debug($transaction->betrag);
                    $transaction->betrag = str_replace('.', '', $transaction->betrag);
                    // debug($transaction->betrag);
                    $transaction->betrag = str_replace(',', '.', $transaction->betrag);
                    // debug($transaction->betrag); die();

					if ($transaction->betrag <= 0) {
						$this->Flash->error(__('Bitte einen gültigen Betrag eingeben.'));
					} elseif (($account->balance - $transaction->betrag) < ($account->maxlimit * (-1))) {
						$this->Flash->error(__('Die Überweisung ist nicht möglich, da der Überziehungsrahmen überschritten wird.'));
					} elseif ($transaction->datum < $date) {
						$this->Flash->error(__('Das Datum darf nicht in der Vergangenheit liegen.'));
					} else {
						if ($this->Transactions->save($transaction)) {
							$this->Flash->success(__('The transaction has been saved.'));
							return $this->redirect(['controller' => 'accounts', 'action' => 'view', $transaction->account_id]);
						}
						$this->Flash->error(__('The transaction could not be saved. Please, try again.'));
					}
				} else {
					$this->Flash->error(__('Ungültige TAN, bitte versuchen Sie es nochmal.'));
				}
			}
			$accounts = $this->Transactions->Accounts->find('list', ['limit' => 200])->where(['user_id' => $this->Auth->user()['id']]);
			$this->set(compact('transaction', 'accounts', 'max_betrag', 'account'));
		}

		/**
		 * Edit method
		 *
		 * @param string|null $id Transaction id.
		 * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
		 * @throws \Cake\Network\Exception\NotFoundException When record not found.
		 */
		public function edit($id = null)
		{
			$transaction = $this->Transactions->get($id, [
				'contain' => ['Accounts.Users']
			]);

			# Schuladmin darf nur Transaktionen seiner Schule bearbeiten
			if ($this->school && $transaction->account->user->school_id != $this->school['id']) {
				$this->Flash->error(__('Sie können nur Transaktionen Ihrer eigenen Schule bearbeiten.'));
				return $this->redirect(['action' => 'index']);
			}

			if ($this->request->is(['patch', 'post', 'put'])) {
				$transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
				if ($this->Transactions->save($transaction)) {
					$this->Flash->success(__('Die Transaktion wurde gespeichert.'));

					return $this->redirect(['action' => 'index']);
				}
				$this->Flash->error(__('Die Transaktion konnte nicht gespeichert werden.'));
			}

			$conditions = [];
			if ($this->school) {
				$conditions = ['school_id' => $this->school['id']];
			}
			$accounts = $this->Transactions->Accounts->find('list', ['limit' => 200])
				->matching('Users', function ($q) use ($conditions) {
					return empty($conditions) ? $q : $q->where($conditions);
				});
			$this->set(compact('transaction', 'accounts'));
		}

		/**
		 * Delete method
		 *
		 * @param string|null $id Transaction id.
		 * @return \Cake\Http\Response|null Redirects to index.
		 * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
		 */
		public function delete($id = null)
		{
			$this->request->allowMethod(['post', 'delete']);
			$transaction = $this->Transactions->get($id, [
				'contain' => ['Accounts.Users']
			]);

			# Schuladmin darf nur Transaktionen seiner Schule löschen
			if ($this->school && $transaction->account->user->school_id != $this->school['id']) {
				$this->Flash->error(__('Sie können nur Transaktionen Ihrer eigenen Schule löschen.'));
				return $this->redirect(['action' => 'index']);
			}

			if ($this->Transactions->delete($transaction)) {
				$this->Flash->success(__('Die Transaktion wurde gelöscht.'));
			} else {
				$this->Flash->error(__('Die Transaktion konnte nicht gelöscht werden.'));
			}

			return $this->redirect(['action' => 'index']);
		}

        /**
         * Storno method - cancel a scheduled transaction
         *
         * @param string|null $id Transaction id.
         * @return \Cake\Http\Response|null Redirects to account history.
         */
        public function storno($id = null)
        {
            $this->request->allowMethod(['post', 'delete']);
            $transaction = $this->Transactions->get($id, [
                'contain' => ['Accounts.Users']
            ]);

            # Sicherheitsprüfung: Nur eigene Transaktionen stornieren (Übungsfirma)
            $user = $this->Auth->user();
            if ($user['role'] !== 'admin' && $transaction->account->user_id != $user['id']) {
                $this->Flash->error(__('Sie können nur eigene Transaktionen stornieren.'));
                return $this->redirect(['controller' => 'Accounts', 'action' => 'index']);
            }

            # Schuladmin darf nur Transaktionen seiner Schule stornieren
            if ($this->school && $transaction->account->user->school_id != $this->school['id']) {
                $this->Flash->error(__('Sie können nur Transaktionen Ihrer eigenen Schule stornieren.'));
                return $this->redirect(['action' => 'index']);
            }

            if ($this->Transactions->delete($transaction)) {
                $this->Flash->success(__('Der geplante Auftrag wurde storniert.'));
            } else {
                $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
            }
            return $this->redirect(['controller' => 'Accounts', 'action' => 'history', $transaction->account_id]);
        }

        /**
         * Search Recipients - AJAX endpoint for Select2
         * Returns all practice companies with their IBAN/BIC for autocomplete
         *
         * @return \Cake\Http\Response JSON response
         */
        public function searchRecipients() {
            $this->autoRender = false;
            $this->response = $this->response->withType('application/json');

            $query = $this->request->getQuery('q', '');

            # Alle Übungsfirmen aus freigegebenen Schulen laden
            $this->loadModel('Users');
            $this->loadModel('Accounts');

            $accountsQuery = $this->Accounts->find('all')
                ->contain(['Users.Schools'])
                ->matching('Users', function ($q) {
                    return $q->where(['Users.role' => 'user', 'Users.active' => 1]);
                })
                ->matching('Users.Schools', function ($q) {
                    return $q->where(['Schools.status' => 'approved']);
                });

            # Suchfilter anwenden
            if (!empty($query)) {
                $accountsQuery->where([
                    'OR' => [
                        'Users.name LIKE' => '%' . $query . '%',
                        'Accounts.iban LIKE' => '%' . $query . '%',
                        'Accounts.name LIKE' => '%' . $query . '%',
                    ]
                ]);
            }

            # Eigenes Konto ausschließen
            $userId = $this->Auth->user('id');
            $accountsQuery->where(['Users.id !=' => $userId]);

            $accounts = $accountsQuery->order(['Users.name' => 'ASC'])->limit(50)->toArray();

            # Ergebnis formatieren für Select2
            $results = [];
            foreach ($accounts as $account) {
                $schoolName = $account->user->school->name ?? '';
                $results[] = [
                    'id' => $account->id,
                    'text' => $schoolName . ' | ' . $account->user->name . ' – ' . $account->iban,
                    'name' => $account->user->name,
                    'iban' => $account->iban,
                    'bic' => $account->bic,
                    'school' => $schoolName,
                ];
            }

            $this->response = $this->response->withStringBody(json_encode(['results' => $results]));
            return $this->response;
        }

        /**
         * Check IBAN method - AJAX endpoint to validate IBAN
         * Returns JSON true/false if IBAN exists in system
         *
         * @return \Cake\Http\Response JSON response
         */
        public function checkiban() {
            $this->autoRender = false;
            $this->response = $this->response->withType('application/json');

            $iban = $this->request->getQuery('iban', '');
            $enteredName = $this->request->getQuery('name', '');

            # Fallback für alte Aufrufmethode
            if (empty($iban) && !empty($this->request->params['?'])) {
                $iban = key($this->request->params['?']);
            }

            $result = [
                'valid' => false,
                'nameMatch' => null,
                'actualName' => null,
                'message' => null
            ];

            # IBAN-Format prüfen
            if (strlen($iban) != 20) {
                $result['message'] = 'Ungültiges IBAN-Format';
                $this->response = $this->response->withStringBody(json_encode($result));
                return $this->response;
            }

            # Konto suchen
            $account = $this->Transactions->Accounts->find('all')
                ->contain(['Users'])
                ->where(['iban' => $iban])
                ->first();

            if (empty($account)) {
                $result['message'] = 'IBAN nicht im EduBank-System gefunden';
                $this->response = $this->response->withStringBody(json_encode($result));
                return $this->response;
            }

            # IBAN ist gültig
            $result['valid'] = true;
            $result['actualName'] = $account->user->name;

            # Namensvergleich wenn Name angegeben
            if (!empty($enteredName)) {
                $enteredNormalized = mb_strtolower(trim($enteredName));
                $actualNormalized = mb_strtolower(trim($account->user->name));

                if ($enteredNormalized === $actualNormalized) {
                    $result['nameMatch'] = 'exact';
                } else {
                    $result['nameMatch'] = 'none';
                    $result['message'] = 'Der angegebene Name stimmt mit dem Inhaber des Empfängerkontos nicht überein.';
                }
            }

            $this->response = $this->response->withStringBody(json_encode($result));
            return $this->response;
        }
	}
