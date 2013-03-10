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
		//
		// First check if the user account system is initialized
		// and allow init otherwise
		$accounts_total = $this->Account->find('count');
		if ( ! $accounts_total ) {
			$this->Auth->allow(array('init'));
			if ( ($this->request['action'] != 'init') && ($this->request['action'] != 'kill') ) {
				return $this->redirect(Router::url( array('action' => 'init'), true ));
			}
		} else { // The user management initialized
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
		} // if user management initialized
		return parent::beforeFilter();
	}

	//
	// List the users of the system
	public function index() {
		return $this->redirect(Router::url( array('action' => 'dashboard'), true ));
	}
	private function new_pair($one, $two) {
		$newpair = array();
		$newpair[] = $one;
		$newpair[] = $two;
		return $newpair;
	}
	public function dashboard() {
		$this->set('z_version', Configure::read('z.version'));
		$this->set('z_token_length', Configure::read('z.token_length'));
		$this->set('z_hash_cost', Configure::read('z.hash_cost'));
		//$this->set('z_token_length', PLUGIN_Z_TOKEN_LENGTH);
		//$this->set('z_hash_cost', PLUGIN_Z_PASSWORD_HASH_COST);
		$this->set('z_password_min_len', Configure::read('z.password_min_len') );
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
		$logins_total = $this->Account->AccountLogin->find('count');
		$this->set('logins_total', $logins_total);
		$logins_failed = $this->Account->AccountLogin->find('count', array('conditions' => array('success !=' => true)));
		$this->set('logins_failed', $logins_failed);

		$qresults = $this->Account->query("select count(created), date(created) from z_password_logins where success=1 group by day(created);");
		$logins = array();
		$logins['good'] = array();
		$logins['bad'] = array();
		foreach ($qresults as $i => $row) {
			foreach ($row as $j => $qres) {
				//array_push($logins['good'], [$qres['date(created)'], $qres['count(created)']]);
				//$logins['good'][] = [$qres['date(created)'], $qres['count(created)']]; PHP 5.4
				$logins['good'][] = $this->new_pair($qres['date(created)'], $qres['count(created)']);
			}
		}
		//$logins['good'] = [[1, 2],[3,5.12],[5,13.1],[7,33.6],[9,85.9],[11,219.9]];
		$qresults = $this->Account->query("select count(created), date(created) from z_password_logins where success=0 group by day(created);");
		foreach ($qresults as $i => $row) {
			foreach ($row as $j => $qres) {
				//array_push($logins['bad'], [$qres['date(created)'], $qres['count(created)']]);
				//$logins['bad'][] = [$qres['date(created)'], $qres['count(created)']];
				$logins['bad'][] = $this->new_pair($qres['date(created)'], $qres['count(created)']);
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
				//array_push($accounts['good'], [$qres['date(created)'], $qres['count(created)']]);
				//$accounts['good'][] = [$qres['date(created)'], $qres['count(created)']];
				$accounts['good'][] = $this->new_pair($qres['date(created)'], $qres['count(created)']);
			}
		}
		$qresults = $this->Account->query("select count(created), date(created) from z_accounts where active=0 group by day(created);");
		foreach ($qresults as $i => $row) {
			foreach ($row as $j => $qres) {
				//array_push($accounts['bad'], [$qres['date(created)'], $qres['count(created)']]);
				//$accounts['bad'][] = [$qres['date(created)'], $qres['count(created)']];
				$accounts['bad'][] = $this->new_pair($qres['date(created)'], $qres['count(created)']);
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
			// Maybe it's a slug?
			$result = $this->Account->findBySlug($id);
			if ( !$result ) {
				/// Invalid account
				throw new NotFoundException(__d('z', 'account_invalid'));
			}
			$this->Account->id = $result['Account']['id'];
		}
		$this->Account->recursive = 1;
		$this->Account->AccountLogin->recursive = -1;
		$this->Account->setAccountLoginLimit(20); // TODO: should be configurable
		$this->set('Account', $this->Account->read(null, $this->Account->id));
	}

	//
	// Add a new user to the system
	public function add() {
		if ($this->request->is('post')) {
			//debug($this->request->data);
			$this->request->data['AccountPassword']['email'] =
				strtolower( trim( $this->request->data['AccountPassword']['email'] ) );
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
			if ($this->Account->saveAll($this->request->data)) {
				/// The account has been saved
				$this->Session->setFlash(__d('z', 'account_saved_success'));
				$this->redirect(array('action' => 'accounts'));
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
			if ( empty($this->request->data['AccountPassword']['new_password']) ) {
				// unset all password data
				unset($this->request->data['AccountPassword']);
			} else {
				$this->request->data['AccountPassword']['password'] = $this->request->data['AccountPassword']['new_password'];
				unset($this->request->data['AccountPassword']['new_password']);
			}
			//debug($this->request->data);
			$this->Account->create($this->request->data);
			if (! $this->Account->saveAll($this->request->data, array('validate' => 'only'))) {
				$this->Session->setFlash(__d('z', 'Registration data validation failure. Please, check your input.'));
				debug($this->Account->validationErrors);
				return;
			}
			if ($this->Account->saveAll($this->request->data)) {
				/// The account has been saved
				$this->Session->setFlash(__d('z', 'account_saved_success'));
				//$this->redirect(array('action' => 'index'));
			} else {
				/// The account could not be saved. Please, try again.
				$this->Session->setFlash(__d('z', 'account_save_problem'));
			}
		}
		$this->Account->recursive = 0;
		$this->request->data = $this->Account->read(null, $id);
		unset($this->request->data['AccountPassword']['password']);
		unset($this->request->data['AccountPassword']['salt']);
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
			$this->redirect(array('action' => 'accounts'));
		}
		/// Account was not deleted
		$this->Session->setFlash(__d('z', 'account_delete_problem'));
		$this->redirect(array('action' => 'accounts'));
	}


	//
	// Initialization of the user management
	//
	public function init() {
		if ($this->request->is('post')) {
			debug('post - creating admin');
			// Create admin
			$this->request->data['Account']['alias'] =
				trim( $this->request->data['Account']['alias'] );
			$this->request->data['AccountPassword']['email'] =
				strtolower( trim( $this->request->data['AccountPassword']['email'] ) );
			unset($this->request->data['Account']['id']);
			unset($this->request->data['AccountPassword']['id']);
			unset($this->request->data['AccountFlag']['id']);
			unset($this->request->data['AccountPassword']['salt']);
			debug($this->request->data);
			$dataSource = $this->Account->getDataSource();
			$dataSource->begin();
			$this->request->data['Account']['active'] = '1';
			$this->request->data['AccountFlag']['user_admin'] = '1';
			$this->request->data['AccountFlag']['agreement_date'] = $this->Account->getDataSource()->expression('NOW()');
			debug("creating the account...");
			$this->Account->create($this->request->data);
			//if (! $this->Account->saveAll($this->request->data, array('validate' => 'only'))) {
			//	$dataSource->rollback();
			//	$this->Session->setFlash(__d('z', 'Registration data validation failure. Please, check your input.'));
			//	debug($this->Account->validationErrors);
			//	return;
			//}
			//debug("validation success, writing data...");
			$result = $this->Account->saveAll($this->request->data);
			debug($result);
			if ($result) {
				/// The account has been saved
				//$dataSource->rollback();
				$dataSource->commit();
				$this->Session->setFlash(__d('z', 'account_saved_success'));
				return $this->redirect(Router::url( array('controller' => 'accounts', 'action' => 'login'), true ));
			} else {
				$dataSource->rollback();
				debug($this->Account->validationErrors);
				/// The account could not be saved. Please, try again.
				$this->Session->setFlash(__d('z', 'account_save_problem'));
			}
		}
	}

	//
	// Initialization of the user management
	//
	public function kill() {
		if ($this->request->is('post')) {
			$this->Session->setFlash(__d('z', 'This function is disabled in the code.'));
			return;
			$dataSource = $this->Account->getDataSource();
			$dataSource->begin();
			// Drop all users
			$this->Account->deleteAll(array('1 = 1'), true);
			$dataSource->commit();
			$this->Session->setFlash(__d('z', 'All users destroyed.'));
			return $this->redirect(Router::url( array('controller' => 'accounts', 'action' => 'logout'), true ));
		}
	}
}
