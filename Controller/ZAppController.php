<?php

class ZAppController extends AppController {

	public $components = array(
		'Security' => array(
			'csrfExpires' => '+1 hour'
			//'csrfExpires' => '+1 minute'
		),
		'Session',
		'Cookie',
		'RequestHandler',
		'Auth' => array(
			'loginAction' => array(
				'controller' => 'accounts',
				'action' => 'login',
				'plugin' => 'z'
			),
			'loginRedirect' => array(
				'controller' => 'users',
				'action' => 'view',
				'plugin' => 'z'
			),
			'authenticate' => array(
				'Z.ZUser'
			)
		),
		//'Z.ZRateLimiter'
	);

	function beforeFilter() {
		parent::beforeFilter();
		// Now the basic security setup
		Security::setHash('sha512');
		$this->Cookie->secure = false;
		// Enforce SSL on required controllers
		$location = Router::parse($this->request->here());
		$SecureControllerList = array( 'users', 'accounts' );
		if ( in_array( $location['controller'], $SecureControllerList ) ) { 
			$this->Security->requireSecure();
		}
		$this->Security->blackHoleCallback = 'blackhole';
		$this->Auth->loginRedirect = $this->referer();
		$this->set('authUser', $this->Auth->user());
	}

	public function afterFilter() {
		parent::afterFilter();

		//debug($this->request->here());
		//debug(Router::parse($this->request->here()));

		// Keep a record of a logged in user whereabouts
		/*$user = $this->Auth->user();
		$IgnoreControllerList = array( 'pages', 'img' );
		$location = Router::parse($this->request->here());
		if ( ! in_array( $location['controller'], $IgnoreControllerList ) ) { 
			$this->loadModel('Visit');
			$this->Visit->recursive = -1;
			$visitData = array();
			if ( ! empty($user) ) {
				$visitData['Visit']['account_id'] = $user['id'];
			}
			$visitData['Visit']['path'] = $this->request->here();
			$visitData['Visit']['from_ip'] = $this->RequestHandler->getClientIp();
			if ( ! $this->Visit->save($visitData) ) {
				debug($this->Visit->validationErrors);
			}
			unset($visitData);
		}*/
	}
	// handle errors
	public function blackhole($type) {
		if ( $type == 'secure' ) {
			$this->redirect('https://' . env('SERVER_NAME') . $this->here);
		} else if ( $type == 'csrf' ) {
			/// The authentication tokens mismatch.
			$this->Session->setFlash(__d('z', 'authentication_tokens_mismatch'));
			$this->redirect($this->referer());
		}
	}
}
