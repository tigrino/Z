<?php
App::uses('ZAppController', 'Z.Controller');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('Account', 'Z.Model');
App::import('Vendor', 'Z.PasswordHash');

class ControlsController extends ZAppController {
	public $uses = array('Z.Account');
	public $paginate = array(
		'Account' => array(
			'limit' => 10,
			'order' => array( 'Account.email' => 'ASC' )
		),
		'AccountToken' => array(
			'limit' => 10,
			'order' => array( 'AccountToken.user_id' => 'ASC' )
		)
        );
	//
	// executed before each request
	public function beforeFilter() {
		//$this->Auth->allow(array('help'));
		//$this->Auth->allow();
		//
		// Verify that we have an admin here
		$id = $this->Auth->user('id');
		$is_admin = $this->Auth->user('user_admin');
		if ( empty($id) || ( $is_admin != 1 ) ) {
			return $this->redirect(Router::url( array('controller' => 'users', 'action' => 'index'), true ));
		}
		return parent::beforeFilter();
	}

	//
	// List the users of the system
	public function index() {
		return $this->redirect(Router::url( array('action' => 'accounts'), true ));
	}
	public function dashboard() {
	}
	public function cryptotest() {
	}
	public function accounts() {
		$this->Account->recursive = 0;
		$this->set('accounts', $this->paginate());
	}
	public function tokens() {
		$this->Account->recursive = 1;
		$this->set('tokens', $this->paginate('AccountToken'));
	}

	//
	// View a user account
	public function view($id = null) {
		$this->Account->id = $id;
		if (!$this->Account->exists()) {
			/// Invalid account
			throw new NotFoundException(__d('z', 'account_invalid'));
		}
		$this->set('Account', $this->Account->read(null, $id));
	}

	//
	// Add a new user to the system
	public function add() {
		if ($this->request->is('post')) {
			//debug($this->request->data);
			$this->request->data['Account']['email'] =
				strtolower( trim( $this->request->data['Account']['email'] ) );
			unset($this->request->data['Account']['id']);
			unset($this->request->data['AccountPassword']['id']);
			unset($this->request->data['AccountFlag']['id']);
			unset($this->request->data['AccountPassword']['salt']);
			$this->Account->create($this->request->data);
			if (! $this->Account->saveAll($this->request->data, array('validate' => 'only'))) {
				$this->Session->setFlash(__d('z', 'Registration data validation failure. Please, check your input.'));
				//debug($this->Account->validationErrors);
				return;
			}
			if ($this->Account->saveAssociated($this->request->data)) {
				/// The account has been saved
				$this->Session->setFlash(__d('z', 'account_saved_success'));
				$this->redirect(array('action' => 'index'));
			} else {
				/// The account could not be saved. Please, try again.
				$this->Session->setFlash(__d('z', 'account_save_problem'));
			}
		}
	}

	public function edit($id = null) {
		$this->Account->id = $id;
		if (!$this->Account->exists()) {
			/// Invalid account
			throw new NotFoundException(__d('z', 'account_invalid'));
		}
		//debug($this->request->data);
		if ($this->request->is('post') || $this->request->is('put')) {
			if ( empty($this->request->data['AccountPassword']['password']) ) {
				// unset all password data
				unset($this->request->data['AccountPassword']);
			}
			//debug($this->request->data);
			$this->Account->create($this->request->data);
			if (! $this->Account->saveAll($this->request->data, array('validate' => 'only'))) {
				$this->Session->setFlash(__d('z', 'Registration data validation failure. Please, check your input.'));
				//debug($this->Account->validationErrors);
				return;
			}
			if ($this->Account->saveAssociated($this->request->data)) {
				/// The account has been saved
				$this->Session->setFlash(__d('z', 'account_saved_success'));
				//$this->redirect(array('action' => 'index'));
			} else {
				/// The account could not be saved. Please, try again.
				$this->Session->setFlash(__d('z', 'account_save_problem'));
			}
		} else {
			$this->Account->recursive = 0;
			$this->request->data = $this->Account->read(null, $id);
			//$this->set($this->Account->read(null, $id));
			unset($this->request->data['AccountPassword']['password']);
			unset($this->request->data['AccountPassword']['salt']);
		}
		//debug($this->Account->data);
	}

	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Account->id = $id;
		if (!$this->Account->exists()) {
			/// Invalid account
			throw new NotFoundException(__d('z', 'account_invalid'));
		}
		if ($this->Account->delete()) {
			/// Account deleted
			$this->Session->setFlash(__d('z', 'account_deleted'));
			$this->redirect(array('action' => 'index'));
		}
		/// Account was not deleted
		$this->Session->setFlash(__d('z', 'account_delete_problem'));
		$this->redirect(array('action' => 'index'));
	}

}
