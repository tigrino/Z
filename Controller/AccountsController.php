<?php
App::uses('ZAppController', 'Z.Controller');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'Z.zcaptcha');
App::import('Vendor', 'Z.PasswordHash');

/**
 * Accounts Controller
 *
 */
class AccountsController extends ZAppController {
	//
	// executed before each request
	public function beforeFilter() {
		$this->Auth->allow(array('captcha', 'login', 'logout', 'register', 'verify', 'reset', 'confirm'));
		//$this->Auth->allow();
		return parent::beforeFilter();
	}

	//
	// Redirect index requests
	public function index() {
		return $this->redirect(Router::url( array('controller' => 'users', 'action' => 'index'), true ));
	}

	//
	// User login function
	// basically, use Auth, all the rest is just housekeeping
	public function login() {
		//
		// Mind you, the view uses 'User' because
		// that's what we need for Auth module
		//
		if ($this->request->is('post')) {
			//
			// Check the dummy field is empty
			if ( $this->_block_robots() ) return;
			$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
			$this->request->data['User']['email'] = 
				strtolower( trim( $this->request->data['User']['email'] ) );
			if ($this->Auth->login()) {
				/// Logged in successfully
				$this->Session->setFlash(__d('z', 'logged_in'), 'default', array(), 'auth');
				$this->Account->id = $this->Auth->user('id');
				$this->Account->recursive = 0;
				$saveData = $this->Account->read();
				//$saveData['Account']['id'] = $this->Auth->user('id');
				$saveData['AccountLogin']['account_id'] = $this->Auth->user('id');
				$saveData['AccountLogin']['good_from_ip'] = $this->RequestHandler->getClientIp();
				$saveData['AccountLogin']['good_login'] = DboSource::expression('NOW()');
				if (! $this->Account->AccountLogin->save($saveData,
					array(  'fieldList' => array(
					    'AccountLogin' => array('account_id', 'good_from_ip', 'good_login'),
						),
						'verify' => true
					))) {
					debug($this->Account->AccountLogin->validationErrors);
				}
				//
				// if the user is admin forward him to the control panel
				if ( $this->Auth->user('user_admin') == 1 ) {
					return $this->redirect(Router::url( array('controller' => 'controls', 'action' => 'accounts'), false ));
				} else {
					return $this->redirect($this->Auth->redirect());
				}
			} else {
				if ( !empty($this->request->data['User']['email']) ) {
					$options = array( 'conditions' => array( 
						'Account.email' => $this->request->data['User']['email'] ) );
					if ( $this->Account->find('count', $options) ) {
						$saveData = $this->Account->find('first', $options);
						$saveData['AccountLogin']['account_id'] = $saveData['Account']['id'];
						$saveData['AccountLogin']['bad_from_ip'] = $this->RequestHandler->getClientIp();
						$saveData['AccountLogin']['bad_login'] = DboSource::expression('NOW()');
						if (! $this->Account->AccountLogin->save($saveData,
							array(  'fieldList' => array(
							    'AccountLogin' => array('account_id', 'bad_from_ip', 'bad_login'),
								),
								'verify' => true
							))) {
							debug($this->Account->AccountLogin->validationErrors);
						}
					}
				}
				/// Username or password is incorrect
				$this->Session->setFlash(__d('z', 'incorrect_credentials'), 'default', array(), 'auth');
			}
		}
	}

	//
	// User logout function, simply use Auth
	public function logout() {
		$this->redirect($this->Auth->logout());
	}

	//
	// Delete the user - set the inactive bit 
	// and marked as deleted in flags
	public function delete($id = null) {
		if ( ! $this->Auth->user('id') ) {
			// User is not logged in
			/// The requested action requires you to be logged in.
			$this->Session->setFlash(__d('z', 'action_requires_login'));
			$this->redirect(array('controller' => 'accounts', 'action' => 'login'));
		} else {
			$this->Account->id = $this->Auth->user('id');
			$this->Account->recursive = 0;
			$this->Account->read();
			$data = array( 
				'Account' => array( 
					'id' => $this->Account->id,
					'active' => false
					),
				'AccountFlag' => array(
					'id' => $this->Account->data['AccountFlag']['id'],
					'account_id' => $this->Account->data['AccountFlag']['account_id'],
					'deleted' => true,
					'deleted_date' => DboSource::expression('NOW()')
					));
			$this->Account->create($data);
			$options = array(
				'fieldList' => array(
					'Account' => array(
						'active'
					),
					'AccountFlag' => array(
						'deleted', 
						'deleted_date'
					)
				)
			);
			$this->Account->saveAssociated(null, $options);
			/// The user account has been deleted. Bye.
			$this->Session->setFlash(__d('z', 'your_account_deleted_success'));
			$this->redirect($this->Auth->logout());
		}
	}

	//
	// Request to change a password
	public function password($id=null) {
		if ( ! $this->Auth->user('id') ) {
			// User is not logged in, forward to reset password
			/// You are not logged in, use this form to reset your password.
			$this->Session->setFlash(__d('z', 'not_logged_in_reset_password'));
			$this->redirect(array('controller' => 'accounts', 'action' => 'reset', $id));
		} else {
			// User is logged in
			$id = $this->Auth->user('id'); // not set if called without userid
			//debug($id);
			if ($this->request->is('post') || $this->request->is('put')) {
				// user is logged in and submits new password
				$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
				// Data verification for correctness and expectations
				if ( $this->request->data['Account']['id'] != $id ) {
					/// Login data mismatch, you have been logged out for security reasons.
					$this->Session->setFlash(__d('z', 'credentials_mismatch_logout'));
					$this->redirect($this->Auth->logout());
					return; // just in case :)
				}
				if ($this->request->data['AccountPassword']['password'] != 
					$this->request->data['AccountPassword']['confirm_password']) {
					/// Passwords must match
					$this->Account->AccountPassword->invalidate('password', __d('z', 'passwords_must_match'), true);
					$this->Account->AccountPassword->invalidate('confirm_password', __d('z', 'passwords_must_match'), true);
					/// The passwords did not match up.
					$this->Session->setFlash(__d('z', 'passwords_do_not_match'));
					return;
				}
				// Verify that user password is correct
				$password_hash = $this->Account->AccountPassword->field('password', array('AccountPassword.account_id' => $id));
				$hasher = new PasswordHash(PLUGIN_Z_PASSWORD_HASH_COST, FALSE);
				if ( ! $hasher->CheckPassword($this->request->data['AccountPassword']['old_password'], $password_hash) ) {
					unset($password_hash);
					unset($hasher);
					$this->Session->setFlash(__d('z', 'credentials_mismatch_logout'));
					$this->redirect($this->Auth->logout());
					return; // just in case :)
				}
				unset($password_hash);
				unset($hasher);
				$this->request->data['AccountPassword']['account_id'] = $id;
				unset($this->request->data['AccountPassword']['old_password']);
				unset($this->request->data['AccountPassword']['confirm_password']);
				// See if validation rules are ok 
				// before we hash the password during save
				$this->Account->AccountPassword->set($this->request->data);
				if (! $this->Account->AccountPassword->validates(
					array('fieldList' => array('password')))) {
					/// Password validation failure. Please, choose a different password.
					$this->Session->setFlash(__d('z', 'bad_password_choose_another'));
					return;
				}
				// Ok, update the user's password
				if ($this->Account->saveAssociated($this->request->data,
					array(  'fieldList' => array(
					    'AccountPassword' => array('account_id', 'password'),
						),
						'verify' => true
					))) {
					/// Your password has been updated.
					$this->Session->setFlash(__d('z', 'password_update_success'));
					$this->redirect(array('controller' => 'users', 'action' => 'index'));
				} else {
					/// The new password could not be saved. Please, try again.
					$this->Session->setFlash(__d('z', 'password_not_saved_problem'));
				}
			} else {
				// 'get' request
				// user is logged in and wants to change password
				$this->Account->recursive = 0;
				$this->request->data = $this->Account->read(null, $id);
				unset($this->request->data['AccountPassword']['password']);
			}
		}
	}

	//
	// User registration with an e-mail confirmation
	public function register() {
		//debug($this->request);
		$this->Auth->logout();
		if ($this->request->is('post')) {
			$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
			//
			// Check the dummy field is empty
			if ( $this->_block_robots() ) return;
			//
			// Check the CAPTCHA is correct
			if ( (! $this->Session->check('captcha_code')) ||
				($this->Session->check('captcha_code') && 
				($this->request->data['Account']['captcha'] != $this->Session->read('captcha_code'))) ) {
				/// The CAPTCHA code is incorrect. Please, try again.
				$this->Session->setFlash(__d('z', 'incorrect_captcha_try_again'));
				$this->request->data['Account']['captcha'] = '';
				$this->Account->invalidate('captcha', __d('z', 'incorrect_captcha_try_again', true));
				return;
			}
			//
			// TOS was agreed
			// a checkbox, if unset, does not appear in data
			// so a Validate does not catch it
			if ( empty($this->request->data['AccountFlag']['agreement']) ) {
				/// Please, confirm your agreement to Terms of Service.
				$this->Session->setFlash(__d('z', 'tos_agreement_confirm'));
				$this->Account->AccountFlag->invalidate('agreement');
				$this->request->data['Account']['captcha'] = '';
				return;
			}
			//
			// Check the passwords are same
			// since the model does not have a rule for this
			if ($this->request->data['AccountPassword']['password'] != 
				$this->request->data['AccountPassword']['confirm_password']) {
				$this->Account->AccountPassword->invalidate('password', __d('z', 'Passwords must match', true));
				$this->Account->AccountPassword->invalidate('confirm_password', __d('z', 'Passwords must match', true));
				$this->Session->setFlash(__d('z', 'The passwords did not match up.'));
				$this->request->data['Account']['captcha'] = '';
				return;
			}
			//
			// And now validate the rest and create the account
			$this->request->data['Account']['email'] = 
				strtolower( trim( $this->request->data['Account']['email'] ) );
			unset($this->request->data['Account']['id']);
			unset($this->request->data['AccountPassword']['id']);
			$this->request->data['Account']['active'] = 0;
			$this->request->data['AccountFlag']['agreement_date'] = DboSource::expression('NOW()');
			$this->Account->create($this->request->data);
			if (! $this->Account->saveAll($this->request->data, array('validate' => 'only'))) {
				$this->Session->setFlash(__d('z', 'Registration data validation failure. Please, check your input.'));
				$this->request->data['Account']['captcha'] = '';
				return;
			}
			$dataSource = $this->Account->getDataSource();
			$dataSource->begin();
			if ($this->Account->save($this->request->data)) {
				$this->request->data['AccountFlag']['account_id'] = $this->Account->id;
				$this->request->data['AccountPassword']['account_id'] = $this->Account->id;
				$this->id = $this->Account->id;
				if ( $this->Account->AccountFlag->save($this->request->data) &&
					$this->Account->AccountPassword->save($this->request->data) ) {
					// All saved OK
					$this->request->data['AccountToken']['account_id'] = $this->Account->id;
					// User needs sometimes to copy/paste this
					// do not make the token too long
					$this->request->data['AccountToken']['purpose'] = PLUGIN_Z_TOKEN_MAIL_VERIFY;
					$this->request->data['AccountToken']['token'] = z_random_hex(PLUGIN_Z_TOKEN_LENGTH);
					$this->request->data['AccountToken']['expires'] = date('Y-m-d H:i:s', strtotime('+4 hours'));
					$this->_clean_old_tokens();
					if ( $this->Account->AccountToken->save($this->request->data) ) {
						$dataSource->commit();
						$fromurl = Router::url( array('action' => 'register'), true );
						$url = Router::url( array('action' => 'verify'), true );
						$urltoken = $url.'/t:'.$this->data['AccountToken']['token'].'/n:'.$this->data['Account']['email'].'';
						$frommail = Configure::read('Z.email_from');
						$sitename = Configure::read('Z.site_title');
						// and send an e-mail to the user
						$email = new CakeEmail();
						$email->viewVars(array(
							'sitename' => $sitename,
							'email' => $this->data['Account']['email'],
							'fromurl' => $fromurl,
							'token' => $this->data['AccountToken']['token'],
							'url' => $url,
							'urltoken' => $urltoken,
							));
						$email->emailFormat('text');
						$email->template('Z.verify', 'Z.comeclick');
						$email->from(array($frommail => $sitename));
						$email->to($this->data['Account']['email']);
						$email->subject('Confirm Registration for ' . $sitename);
						$email->send();
						$this->Session->setFlash('User created successfully. Please check your email for a validation link.');
						$this->redirect(array('action' => 'verify'));
					} else {
						$dataSource->rollback();
						$this->Session->setFlash(__d('z', 'The user could not be saved. Please, try again.'));
					}
				} else {
					$dataSource->rollback();
					$this->Session->setFlash(__d('z', 'The user could not be saved. Please, try again.'));
				}
			} else {
				$dataSource->rollback();
				$this->Session->setFlash(__d('z', 'The user could not be saved. Please, try again.'));
			}
		}
		$this->request->data['AccountPassword']['password'] = '';
		$this->request->data['AccountPassword']['confirm_password'] = '';
		$this->request->data['Account']['captcha'] = '';
		$this->request->data['Account']['ruhuman'] = '';
	}

	//
	// Registration second step
	// Verify the token send to e-mail and enable account
	public function verify() {
		if (isset($this->passedArgs['t']) && isset($this->passedArgs['n'])){
			$this->passedArgs = Sanitize::clean($this->passedArgs, array('encode' => false));
			$token = $this->passedArgs['t'];
			$email = $this->passedArgs['n'];
		} else if ($this->request->is('post')) {
			$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
			//
			// Check the dummy field is empty
			if ( $this->_block_robots() ) return;
			$this->request->data['Account']['email'] = 
				strtolower( trim( $this->request->data['Account']['email'] ) );
			$email = $this->request->data['Account']['email'];
			$token = $this->request->data['AccountToken']['token'];
		} else {
			$token = null;
		}
		$this->_clean_old_tokens(); // first remove expired tokens
		$this->_clean_old_registrations(); // remove expired registration requests
		if ( !empty($token) ) {
			$result = $this->Account->AccountToken->find('first', array(
				'conditions' => array(
					'AccountToken.token' => $token,
					'AccountToken.purpose' => PLUGIN_Z_TOKEN_MAIL_VERIFY,
					'Account.email' => $email,
					//'Token.expires >=' => date('Y-m-d H:i:s')
					)
				));
			if ( !empty($result) ) {
				$this->Account->id = $result['AccountToken']['account_id'];
				$this->Account->recursive = 0;
				$this->Account->read();
				$data = array(
					'Account' => array(
						'id' => $this->Account->data['Account']['id'],
						'active' => 1
					),
					'AccountFlag' => array(
						'id' => $this->Account->data['AccountFlag']['id'],
						'account_id' => $this->Account->data['Account']['id'],
						'email_verified' => 1,
						'email_verified_date' => DboSource::expression('NOW()')
					)
				);
				$this->Account->create($data);
				$options = array(
					'fieldList' => array(
						'Account' => array(
							'active'
						),
						'AccountFlag' => array(
							'email_verified',
							'email_verified_date'
						)
					)
				);
				if (! $this->Account->saveAssociated(null, $options) ) {
					$this->Session->setFlash(__d('z', 'The user could not be saved. Please, try again.'));
					return;
				}
				$this->Account->AccountToken->delete($result['AccountToken']['id']);
				$this->Session->setFlash(__d('z', 'Your e-mail was successfully verified.'));
				$this->redirect(array('action' => 'login'));
			} else {
				$this->Session->setFlash(__d('z', 'User e-mail verification failed.'));
			}
		}
	}

	//
	// User lost his password and requests a reset link
	public function reset() {
		if ( $this->Auth->user('id') ) {
			// User logged in, forward to change password
			$id = $this->Auth->user('id');
			$this->Session->setFlash(__d('z', 'You are already logged in, use this form to change your password.'));
			$this->redirect(array('action' => 'password'));
		}
		if ($this->request->is('post')) {
			//
			// Check the dummy field is empty
			if ( $this->_block_robots() ) return;
			// Sanitize data
			$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
			//
			// Check the CAPTCHA is correct
			if ( (! $this->Session->check('captcha_code')) ||
				($this->Session->check('captcha_code') && 
				($this->request->data['Account']['captcha'] != $this->Session->read('captcha_code'))) ) {
				$this->Session->setFlash(__d('z', 'The CAPTCHA code is incorrect. Please, try again.'));
				$this->request->data['Account']['captcha'] = '';
				$this->Account->invalidate('captcha', __d('z', 'CAPTCHA code is incorrect', true));
				return;
			}
			$this->request->data['Account']['email'] = 
				strtolower( trim( $this->request->data['Account']['email'] ) );
			// Find the user record by email
			$this->Account->recursive = 0; // prevent loading any hasMany rows
			$userData = $this->Account->find( 'first', array(
				'conditions' => array('Account.email' => $this->request->data['Account']['email'])
				));
			if ( ! is_array( $userData ) ) {
				// user was not found
				/// The e-mail address is not registered.
				$this->Session->setFlash(__d('z', 'email_not_registered'));
				$this->request->data['Account']['captcha'] = '';
				$this->Account->invalidate('email', __d('z', 'email_not_registered', true));
				return;
			} else {
				// user is found, proceed with token generation
				$data = array( 
					'AccountToken' => array(
						'account_id' => $userData['Account']['id'],
						'purpose' => PLUGIN_Z_TOKEN_RESET_CONFIRM,
						// User needs sometimes to copy/paste this
						// do not make the token too long
						'token' => z_random_hex(PLUGIN_Z_TOKEN_LENGTH),
						'expires' => date('Y-m-d H:i:s', strtotime('+4 hours'))
						));
				$this->Account->AccountToken->create($data);
				$options = array(
					'fieldList' => array(
						'AccountToken' => array(
							'account_id', 
							'purpose', 
							'token',
							'expires'
						)
					)
				);
				if ( $this->Account->AccountToken->save(null, $options) ) {
					$fromurl = Router::url( array('action' => 'reset'), true );
					$url = Router::url( array('action' => 'confirm'), true );
					$urltoken = $url.'/t:'.$data['AccountToken']['token'].'/n:'.$this->request->data['Account']['email'].'';
					$frommail = Configure::read('Z.email_from');
					$sitename = Configure::read('Z.site_title');
					// and send an e-mail to the user
					$email = new CakeEmail();
					$email->viewVars(array(
						'sitename' => $sitename,
						'email' => $this->request->data['Account']['email'],
						'fromurl' => $fromurl,
						'token' => $data['AccountToken']['token'],
						'url' => $url,
						'urltoken' => $urltoken,
						));
					$email->emailFormat('text');
					$email->template('Z.reset', 'Z.comeclick');
					$email->from(array($frommail => $sitename));
					$email->to($this->request->data['Account']['email']);
					$email->subject('Confirm Password Reset for Your Account at ' . $sitename);
					$email->send();
					$this->Session->setFlash(__d('z', 'Password reset request created successfully. Please check your email.'));
					$this->redirect(array('action' => 'confirm'));
				} else {
					$this->Session->setFlash(__d('z', 'The request could not be approved. Please, try again.'));
				}
			}
		}
		$this->request->data['User']['captcha'] = '';
	}

	public function confirm() {
		$this->_clean_old_tokens(); // first remove expired tokens
		if ($this->request->is('post') || $this->request->is('put')) {
			//
			// Check the dummy field is empty
			if ( $this->_block_robots() ) return;
			$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
			$this->request->data['Account']['email'] = 
				strtolower( trim( $this->request->data['Account']['email'] ) );
			$email = $this->request->data['Account']['email'];
			$token = $this->request->data['AccountToken']['token'];
			$password1 = $this->request->data['AccountPassword']['password'];
			$password2 = $this->request->data['AccountPassword']['password_confirm'];
			if ( empty($token) ) {
				$this->Session->setFlash(__d('z', 'Please, enter values to all required fields.'));
				$this->Account->AccountToken->invalidate('token', __d('z', 'Missing a required value.'), true);
				return;
			}
			if ( empty($email) ) {
				$this->Session->setFlash(__d('z', 'Please, enter values to all required fields.'));
				$this->Account->invalidate('email', __d('z', 'Missing a required value.'), true);
				return;
			}
			if ( empty($password1) ) {
				$this->Session->setFlash(__d('z', 'Please, enter values to all required fields.'));
				$this->Account->AccountPassword->invalidate('password', __d('z', 'Missing a required value.'), true);
				return;
			}
			if ( empty($password2) ) {
				$this->Session->setFlash(__d('z', 'Please, enter values to all required fields.'));
				$this->Account->AccountPassword->invalidate('password_confirm', __d('z', 'Missing a required value.'), true);
				return;
			}
			if ( !($password1 === $password2) ) {
				$this->Session->setFlash(__d('z', 'The passwords are different.'));
				$this->Account->AccountPassword->invalidate('password', __d('z', 'Passwords must match.'), true);
				$this->Account->AccountPassword->invalidate('password_confirm', __d('z', 'Passwords must match.'), true);
				return;
			}
			$result = $this->Account->AccountToken->find('first', array(
				'conditions' => array(
					'AccountToken.token' => $token,
					'Account.email' => $email,
					'AccountToken.purpose' => PLUGIN_Z_TOKEN_RESET_CONFIRM
					)
				));
			if ( empty($result) ) {
				$this->Session->setFlash(__d('z', 'Token verification failed.'));
				$this->Account->AccountToken->invalidate('token', __d('z', 'Missing a correct token.'), true);
				return;
			}
			$token_id = $result['AccountToken']['id'];
			$result = $this->Account->find('first', array(
				'conditions' => array(
					'Account.id' => $result['Account']['id'],
				)
			));
			if ( empty($result) ) {
				// something is terminally wrong
				$this->Session->setFlash(__d('z', 'Retrieval of user record during password update failed.'));
				return;
			}
			$result['AccountPassword']['password'] = $password1;
			//unset($result['AccountPassword']['salt']);
			if ( $this->Account->AccountPassword->save($result) ) {
				$this->Account->AccountToken->delete($token_id);
				$this->Session->setFlash(__d('z', 'User password was successfully changed.'));
				$this->redirect(array('action' => 'login'));
			} else {
				$this->Session->setFlash(__d('z', 'Password update failed.'));
			}
		} else {
			if (isset($this->passedArgs['t']) && isset($this->passedArgs['n'])){
				$this->passedArgs = Sanitize::clean($this->passedArgs, array('encode' => false));
				$this->request->data['Account']['email'] = $this->passedArgs['n'];
				$this->request->data['AccountToken']['token'] = $this->passedArgs['t'];
			}
		}
	}


	//
	// Displays the CAPTCHA picture
	// and saves its value to the session
	// called as an img from view
	public function captcha() {
		$this->autoRender = false;
		$this->layout='ajax';
		$code = zcaptcha_create();
		$this->Session->write('captcha_code', $code);
	}

	protected function _clean_old_tokens( ) {
		// check and remove the expired tokens
		$this->Account->recursive = 1;
		$this->Account->AccountToken->deleteAll( array(
			'AccountToken.expires <' => date('Y-m-d H:i:s')
			), true );
	}
	protected function _clean_old_registrations( ) {
		// XXX TODO
		// Here we need to find all user records that are
		// - not active
		// - with e-mail not verified
		// - with no outstanding e-mail tokens
		// and delete them.
	}
	protected function _block_robots() {
		// The forms have a hidden field that is supposed
		// to be left empty. If it is filled in, we are
		// dealing with an overzealous robot.
		if ( !empty($this->request->data['Account']['ruhuman']) ) {
			/// We do not accept registrations from bots.
			$this->Session->setFlash(__d('z', 'bots_are_not_welcome'));
			$this->redirect(array('plugin' => null, 'controller' => 'pages', 'action' => 'index'));
			return true; // just in case
		} else {
			return false;
		}
	}

}
