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
		public function isAuthorized($user)
		{
			if (isset($user['role']) && $user['role'] === 'user') {
				if (in_array($this->request->getParam('action'), ['view', 'add', 'storno', 'checkiban'])) {
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
			$this->paginate = [
				'contain' => ['Accounts']
			];
			$transactions = $this->paginate($this->Transactions);

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
				'contain' => ['Accounts']
			]);
            if ($this->Auth->user()['role'] !== 'admin' && $transaction->account->user_id != $this->Auth->user()['id']) {
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
			# Überweisungslimit checken
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
                    #debug($transaction->betrag);
                    $transaction->betrag = str_replace('.', '', $transaction->betrag);
                    #debug($transaction->betrag);
                    $transaction->betrag = str_replace(',', '.', $transaction->betrag);
                    #debug($transaction->betrag);die();

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
				'contain' => []
			]);
			if ($this->request->is(['patch', 'post', 'put'])) {
				$transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
				if ($this->Transactions->save($transaction)) {
					$this->Flash->success(__('The transaction has been saved.'));

					return $this->redirect(['action' => 'index']);
				}
				$this->Flash->error(__('The transaction could not be saved. Please, try again.'));
			}
			$accounts = $this->Transactions->Accounts->find('list', ['limit' => 200]);
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
			$transaction = $this->Transactions->get($id);
			if ($this->Transactions->delete($transaction)) {
				$this->Flash->success(__('The transaction has been deleted.'));
			} else {
				$this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
			}

			return $this->redirect(['action' => 'index']);
		}
        public function storno($id = null)
        {
            $this->request->allowMethod(['post', 'delete']);
            $transaction = $this->Transactions->get($id);
            if ($this->Transactions->delete($transaction)) {
                $this->Flash->success(__('Der geplante Auftrag wurde storniert.'));
            } else {
                $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
            }
            return $this->redirect(['controller' => 'Accounts', 'action' => 'history', $transaction->account_id]);
        }
        public function checkiban() {
            $this->autoRender = false;
            $iban_ok = false;
            $iban = key($this->request->params['?']);
            $account = $this->Transactions->Accounts->find('all')->where(['iban' => $iban])->first();
            if(strlen($iban) == 20 && !empty($account)) {
                $iban_ok = true;
            }
            $this->response->body(json_encode($iban_ok));
            $this->viewBuilder()->layout(false);
            return  $this->response;
        }
	}
