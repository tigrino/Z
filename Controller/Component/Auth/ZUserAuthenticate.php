<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::import('Vendor', 'Z.PasswordHash');

class ZUserAuthenticate extends BaseAuthenticate {
	public $components = array('Session');
	public $uses = array();
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		if ( (! empty($request->data['User']['email'])) &&
			(! empty($request->data['User']['password'])) ) {
			// The request contains an email and password
			// Load the 'User' model
			$this->User = ClassRegistry::init('User');
			// Find a record with the given e-mail address
			$credentials = $this->User->find('first', array(
				'conditions' => array('User.email' => $request->data['User']['email'])
				));
			if ( ! empty($credentials) ) {
				// Create an instance of bcrypt password hasher
				$hasher = new PasswordHash(PLUGIN_Z_PASSWORD_HASH_COST, FALSE);
				// Request the hasher to check the password
				// The salt is kept together with the password hash
				// in the same field
				if ( $hasher->CheckPassword($request->data['User']['password'], $credentials['User']['password']) ) {
					// On success
					// return the user record from the DB
					// less the password info
					unset($credentials['User']['password']);
					unset($hasher);
					return($credentials['User']);
				}
				unset( $hasher );
			}
		}
		return false;
	}
}
