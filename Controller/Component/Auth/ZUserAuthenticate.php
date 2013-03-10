<?php
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
App::import('Vendor', 'Z.PasswordHash');

class ZUserAuthenticate extends BaseAuthenticate {
	public $components = array('Session');
	public $uses = array();
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		//
		// Check we have the data and convert
		$userdata = array();
		if ( (! empty($request->data['User']['email'])) &&
			(! empty($request->data['User']['password'])) ) {
			// The request contains an email and password
			$userdata['email'] = strtolower( trim( $request->data['User']['email'] ) );
			$userdata['password'] = $request->data['User']['password'];
		} else if ( (! empty($request->data['User']['alias'])) &&
		                        (! empty($request->data['User']['password'])) ) {
			if ( filter_var( $request->data['User']['alias'], FILTER_VALIDATE_EMAIL ) ) {
				// The email in the alias field
				$userdata['email'] = strtolower( trim( $request->data['User']['alias'] ) );
				$userdata['password'] = $request->data['User']['password'];
			} else {
				// The request contains an alias and password
				$userdata['alias'] = trim( $request->data['User']['alias'] );
				$userdata['password'] = $request->data['User']['password'];
			}
		}
		// 
		// Do we have authentication data?
		if ( empty( $userdata ) ) {
			return false;
		}
		//
		// Load the 'User' model
		$this->User = ClassRegistry::init('User');
		//
		// Find the user record
		if ( !empty($userdata['email']) ) {
			$credentials = $this->User->find('first', array(
				'conditions' => array('User.email' => $userdata['email'])
				));
		} else if ( !empty($userdata['alias']) ) {
			$credentials = $this->User->find('first', array(
				'conditions' => array('User.alias' => $userdata['alias'])
				));
		}
		// 
		// Do we have a user record?
		if ( empty( $credentials ) ) {
			return false;
		}
		//
		// Check the user password
		// Create an instance of bcrypt password hasher
		$hasher = new PasswordHash(Configure::read("z.hash_cost"), FALSE);
		// Request the hasher to check the password
		// The salt is kept together with the password hash
		// in the same field
		if ( $hasher->CheckPassword($userdata['password'], $credentials['User']['password']) ) {
			// On success
			// return the user record from the DB
			// less the password info
			unset($credentials['User']['password']);
			unset($hasher);
			return($credentials['User']);
		}
		unset( $hasher );
		return false;
	}
}
