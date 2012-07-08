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

}
