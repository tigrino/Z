<?php
App::uses('ZAppController', 'Z.Controller');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('Account', 'Z.Model');

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
			throw new NotFoundException(__('Invalid account'));
		}
		$this->set('Account', $this->Account->read(null, $id));
	}

	//
	// Add a new user to the system
	public function add() {
		if ($this->request->is('post')) {
			$this->Account->create();
			if ($this->Account->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The account has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The account could not be saved. Please, try again.'));
			}
		}
	}

	public function edit($id = null) {
		$this->Account->id = $id;
		if (!$this->Account->exists()) {
			throw new NotFoundException(__('Invalid account'));
		}
		//debug($this->request->data);
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Account->saveAssociated($this->request->data)) {
				$this->Session->setFlash(__('The account has been saved'));
				//$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The account could not be saved. Please, try again.'));
			}
		} else {
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
			throw new NotFoundException(__('Invalid account'));
		}
		if ($this->Account->delete()) {
			$this->Session->setFlash(__('Account deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Account was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

}
