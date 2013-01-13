<?php

class ZAppController extends AppController {

	public $components = array(
		'Security' => array(
			'csrfExpires' => '+1 hour'
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
	);

	function beforeFilter() {
		parent::beforeFilter();
		// Now the basic security setup
		Security::setHash('sha512');
		$this->Cookie->secure = false;
		// Enforce SSL on required controllers
		$location = Router::parse(Router::normalize($this->request->here()));
		$SecureControllerList = array( 'users', 'accounts', 'controls' );
		if ( in_array( $location['controller'], $SecureControllerList ) ) { 
			$this->Security->requireSecure();
		}
		$this->Security->blackHoleCallback = 'blackhole';
		$this->set('authUser', $this->Auth->user());
	}

	//public function afterFilter() {
	//	parent::afterFilter();
	//}

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
