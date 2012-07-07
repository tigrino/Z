<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');

class ZUserAuthenticate extends BaseAuthenticate {
	public $components = array('Session');
	public $uses = array();
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		if ( (! empty($request->data['User']['email'])) &&
			(! empty($request->data['User']['password'])) ) {
			Controller::loadModel('User');
			$credentials = $this->User->find('first', array(
				'conditions' => array('User.email' => $request->data['User']['email'])
				));
			if ( ! empty($credentials) ) {
				if ( $credentials['User']['password'] == AuthComponent::password( $credentials['User']['salt'] . $request->data['User']['password']) ) {
					unset($credentials['User']['password']);
					unset($credentials['User']['salt']);
					return($credentials['User']);
				}
			}
		}
		return false;
	}
}
