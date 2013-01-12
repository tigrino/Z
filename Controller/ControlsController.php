<?php
App::uses('ZAppController', 'Z.Controller');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::uses('Account', 'Z.Model');
App::import('Vendor', 'Z.PasswordHash');
App::import('Vendor', 'Z.zpasswordblacklist');

class ControlsController extends ZAppController {
	public $uses = array('Z.Account');
	public $helpers = array('Form', 'Html', 'Js');
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
		// Verify that we are coming from either this
		// controller or the login page.
		// As a precaution, if we come from anywhere
		// else (direct link, page in application)
		// require a re-authentication.
		$location = parse_url($this->referer());
		$normal_url = Router::normalize($location['path']);
		$from_route = Router::parse($normal_url);
		//debug($this->referer());
		//debug($location);
		//debug($normal_url);
		//debug($from_route);
		//return;
		if ( 
			($from_route['plugin'] != 'z') ||
			! ( ($from_route['controller'] == 'controls') ||
			    ( ($from_route['controller'] == 'accounts') && ($from_route['action'] == 'login') ) )
			) {
				$this->Session->setFlash(__d('z', 'action_requires_reauthorization'));
				$this->redirect($this->Auth->logout());
		}
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
		return $this->redirect(Router::url( array('action' => 'dashboard'), true ));
	}
	public function dashboard() {
		$this->set('z_version', Configure::read('z.version'));
		$this->set('z_token_length', PLUGIN_Z_TOKEN_LENGTH);
		$this->set('z_hash_cost', PLUGIN_Z_PASSWORD_HASH_COST);
		$this->set('z_wordlists', z_wordlist_names() );
		$this->set('z_use_password_blacklist', Configure::read('z.use_password_blacklist'));

		$accounts_total = $this->Account->find('count');
		$this->set('accounts_total', $accounts_total);
		$accounts_active = $this->Account->find('count', 
			array(
		        	'conditions' => array('Account.active' => true)
			));
		$this->set('accounts_active', $accounts_active);
		$tokens = $this->Account->AccountToken->find('count');
		//debug($tokens);
		$this->set('tokens', $tokens);

		$qresults = $this->Account->query("select count(created), date(created) from z_account_logins where success=1 group by day(created);");
		$logins = array();
		$logins['good'] = array();
		$logins['bad'] = array();
		foreach ($qresults as $i => $row) {
			foreach ($row as $j => $qres) {
				array_push($logins['good'], [$qres['date(created)'], $qres['count(created)']]);
			}
		}
		//$logins['good'] = [[1, 2],[3,5.12],[5,13.1],[7,33.6],[9,85.9],[11,219.9]];
		$qresults = $this->Account->query("select count(created), date(created) from z_account_logins where success=0 group by day(created);");
		foreach ($qresults as $i => $row) {
			foreach ($row as $j => $qres) {
				array_push($logins['bad'], [$qres['date(created)'], $qres['count(created)']]);
			}
		}
		//debug($logins);
		$this->set('logins', $logins);

		$accounts = array();
		$accounts['good'] = array();
		$accounts['bad'] = array();
		$qresults = $this->Account->query("select count(created), date(created) from z_accounts where active=1 group by day(created);");
		foreach ($qresults as $i => $row) {
			foreach ($row as $j => $qres) {
				array_push($accounts['good'], [$qres['date(created)'], $qres['count(created)']]);
			}
		}
		$qresults = $this->Account->query("select count(created), date(created) from z_accounts where active=0 group by day(created);");
		foreach ($qresults as $i => $row) {
			foreach ($row as $j => $qres) {
				array_push($accounts['bad'], [$qres['date(created)'], $qres['count(created)']]);
			}
		}
		//debug($accounts);
		$this->set('accounts', $accounts);
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
		$this->Account->recursive = 1;
		$this->Account->AccountLogin->recursive = -1;
		$this->Account->setAccountLoginLimit(20); // TODO: should be configurable
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
			unset($this->request->data['AccountPassword']['password']);
			unset($this->request->data['AccountPassword']['salt']);
		}
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
