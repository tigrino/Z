<?php
App::uses('ZAppController', 'Z.Controller');
App::uses('Sanitize', 'Utility');
App::uses('CakeEmail', 'Network/Email');
App::import('Vendor', 'Z.zcaptcha');
App::import('Vendor', 'Z.PasswordHash');
App::import('Vendor', 'Z.zrandom');

/**
 * Accounts Controller
 *
 */
class AccountsController extends ZAppController {
	//
	// executed before each request
	public function beforeFilter() {
		$this->Auth->allow(array('captcha', 'login', 'logout', 'register', 'verify', 'reset', 'confirm', 'tos'));
		//$this->Auth->allow();
		$this->_clean_old_tokens(); // first remove expired tokens
		$this->_clean_old_registrations(); // remove expired registration requests
		return parent::beforeFilter();
	}

	//
	// Redirect index requests
	public function index() {
		return $this->redirect(Router::url( array('action' => 'view'), true ));
	}

	//
	// Redirect index requests
	public function view() {
		if ( ! $this->Auth->user('id') ) {
			// User is not logged in, forward to login
			/// The requested action requires you to be logged in.
			$this->Session->setFlash(__d('z', 'action_requires_login'), 'flash_bad', array(), 'auth');
			$this->redirect(array('action' => 'login'));
		} else {
			// User is logged in
			Controller::loadModel('User');
			$id = $this->Auth->user('id');
			$this->User->id = $id;
			if (!$this->User->exists()) {
				// A strange situation when the logged in
				// user actually does not exist
				// but maybe he was blocked? Log him out.
				$this->redirect($this->Auth->logout());
				//throw new NotFoundException(__('error_invalid_user'));
			}
			$this->set('user', $this->User->read(null, $id));
		}
	}

	//
	// User login function
	// basically, use Auth, all the rest is just housekeeping
	public function login() {
		if ( $this->Auth->user('id') ) {
			// If user is logged in already
			// we redirect him to the "home page"
			// that is hopefully set in the
			// Auth configuration
			return $this->redirect($this->Auth->redirect());
		}
		//
		// Mind you, the view uses 'User' because
		// that's what we need for Auth module
		//
		if ($this->request->is('post')) {
			//
			// Check the dummy field is empty
			if ( $this->_block_robots() ) return;
			$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
			//debug($this->request->data);
			//$login_name = strtolower( trim( $this->request->data['User']['alias'] ) );
			//
			// Now verify the history, if a user
			// is running a brute force - slow down
			$result = $this->Account->AccountLogin->find('count', 
				array(
					'conditions' => array(
						'AccountLogin.from_ip' => $this->RequestHandler->getClientIp(),
						'AccountLogin.success' => FALSE,
						'AccountLogin.created >' => date('Y-m-d H:i:s', strtotime("-300 seconds"))
						)
				));
			//debug($result);
			if ( $result > 2 ) {
				sleep($result*3); // the more you try the slower it gets
				// 180 sec @ *2 = circa 13 tries first 3 minutes
				// 300 sec @ *2 = circa 18 tries first 5 minutes, top delay at 34 seconds
				// 180 sec @ *3 = circa 11 tries first 3 minutes
				// 300 sec @ *3 = circa 14 tries first 5 minutes, top delay at 42 seconds
				// 180 sec @ square = circa 7 tries first 3 minutes
				// 300 sec @ square = circa 9 tries first 5 minutes, top delay at 81 seconds
			}
			//
			// Now login
			if ($this->Auth->login()) {
				/// Logged in successfully
				//debug($this->Auth->user);
				$this->Session->setFlash(__d('z', 'logged_in'), 'flash_good', array(), 'auth');
				$this->Account->id = $this->Auth->user('id');
				$saveData = array();
				$saveData['AccountLogin']['email'] = $this->Auth->user('email');
				$saveData['AccountLogin']['from_ip'] = $this->RequestHandler->getClientIp();
				$saveData['AccountLogin']['success'] = TRUE;
				if (! $this->Account->AccountLogin->save($saveData,
					array(  'fieldList' => array(
					    'AccountLogin' => array('email', 'from_ip', 'success'),
						),
						'verify' => true
					))) {
					//debug($this->Account->AccountLogin->validationErrors);
				}
				//
				// if the user is admin forward him to the control panel
				if ( $this->Auth->user('user_admin') == 1 ) {
					return $this->redirect(Router::url( array('controller' => 'controls', 'action' => 'index'), true ));
				} else {
					return $this->redirect($this->Auth->redirect());
				}
			} else {
				$saveData = array();
				$saveData['AccountLogin']['email'] = $this->request->data['User']['alias'];
				$saveData['AccountLogin']['from_ip'] = $this->RequestHandler->getClientIp();
				$saveData['AccountLogin']['success'] = FALSE;
				if (! $this->Account->AccountLogin->save($saveData,
					array(  'fieldList' => array(
					    'AccountLogin' => array('email', 'from_ip', 'success'),
						),
						'verify' => true
					))) {
					//debug($this->Account->AccountLogin->validationErrors);
				}
				/// Username or password is incorrect
				$this->Session->setFlash(__d('z', 'incorrect_credentials'), 'flash_bad', array(), 'auth');
			}
		}
	}

	//
	// User logout function, simply use Auth
	public function logout() {
		$this->Session->setFlash(__d('z','logged_out'), 'flash_good', array(), 'auth');
		$this->redirect($this->Auth->logout());
	}

	//
	// Delete the user - set the inactive bit 
	// and marked as deleted in flags
	public function delete($id = null) {
		if ( ! $this->Auth->user('id') ) {
			// User is not logged in
			/// The requested action requires you to be logged in.
			$this->Session->setFlash(__d('z','action_requires_login'), 'flash_bad', array(), 'auth');
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
					'deleted_date' => $this->Account->getDataSource()->expression('NOW()')
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
			$this->Session->setFlash(__d('z', 'your_account_deleted_success'), 'flash_good');
			$this->redirect($this->Auth->logout());
		}
	}

	//
	// Request to change a password
	public function password($id=null) {
		if ( ! $this->Auth->user('id') ) {
			// User is not logged in, forward to reset password
			$this->Session->setFlash(__d('z', 'not_logged_in_reset_password'), 'flash_bad');
			$this->redirect(array('controller' => 'accounts', 'action' => 'reset', $id));
		} else {
			// User is logged in
			$id = $this->Auth->user('id'); // not set if called without userid
			if ($this->request->is('post') || $this->request->is('put')) {
				// user is logged in and submits new password
				//$this->request->data = Sanitize::clean($this->request->data, array('encode' => false));
				// Data verification for correctness and expectations
				if ( $this->request->data['Account']['id'] != $id ) {
					/// Login data mismatch, you have been logged out for security reasons.
					$this->Session->setFlash(__d('z', 'credentials_mismatch_logout'), 'flash_bad');
					$this->redirect($this->Auth->logout());
					return; // just in case :)
				}
				if ($this->request->data['AccountPassword']['password'] != 
					$this->request->data['AccountPassword']['confirm_password']) {
					/// Passwords must match
					$this->Account->AccountPassword->invalidate('password', __d('z', 'passwords_must_match'), true);
					$this->Account->AccountPassword->invalidate('confirm_password', __d('z', 'passwords_must_match'), true);
					/// The passwords did not match up.
					$this->Session->setFlash(__d('z', 'passwords_do_not_match'), 'flash_bad');
					return;
				}
				// Verify that user password is correct
				$password_hash = $this->Account->AccountPassword->field('password', array('AccountPassword.account_id' => $id));
				$hash_cost = Configure::read('z.hash_cost');
				$hash_cost = $hash_cost ? $hash_cost : PLUGIN_Z_DEFAULT_PASSWORD_HASH_COST;
				$hasher = new PasswordHash($hash_cost, FALSE);
				if ( ! $hasher->CheckPassword($this->request->data['AccountPassword']['old_password'], $password_hash) ) {
					unset($password_hash);
					unset($hasher);
					$this->Session->setFlash(__d('z', 'credentials_mismatch_logout'), 'flash_bad');
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
					$this->Session->setFlash(__d('z', 'bad_password_choose_another'), 'flash_bad');
					return;
				}
				// Ok, update the user's password
				//debug($this->Account->AccountPassword);
				if ($this->Account->AccountPassword->save($this->request->data,
					array(  'fieldList' => array(
					    //'AccountPassword' => array('account_id', 'password'),
					    'AccountPassword' => array('password'),
						),
						'verify' => true
					))) {
					/// Your password has been updated.
					$this->Session->setFlash(__d('z', 'password_update_success'), 'flash_good');
					$this->redirect(array('controller' => 'users', 'action' => 'index'));
				} else {
					/// The new password could not be saved. Please, try again.
					$this->Session->setFlash(__d('z', 'password_not_saved_problem'), 'flash_bad');
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
				$this->Session->setFlash(__d('z', 'incorrect_captcha_try_again'), 'flash_bad');
				$this->request->data['Account']['captcha'] = '';
				$this->Account->invalidate('captcha', __d('z', 'captcha_incorrect', true));
				return;
			}
			//
			// TOS was agreed
			// a checkbox, if unset, does not appear in data
			// so a Validate does not catch it
			if ( empty($this->request->data['AccountFlag']['agreement']) ) {
				/// Please, confirm your agreement to Terms of Service.
				$this->Session->setFlash(__d('z', 'tos_agreement_confirm'), 'flash_bad');
				$this->Account->AccountFlag->invalidate('agreement');
				$this->request->data['Account']['captcha'] = '';
				return;
			}
			//
			// Check the passwords are same
			// since the model does not have a rule for this
			if ($this->request->data['AccountPassword']['password'] != 
				$this->request->data['AccountPassword']['confirm_password']) {
				$this->Account->AccountPassword->invalidate('password', __d('z', 'passwords_must_match', true));
				$this->Account->AccountPassword->invalidate('confirm_password', __d('z', 'passwords_must_match', true));
				$this->Session->setFlash(__d('z', 'passwords_did_not_match'), 'flash_bad');
				$this->request->data['Account']['captcha'] = '';
				return;
			}
			//
			// And now validate the rest and create the account
			$this->request->data['AccountPassword']['email'] = 
				strtolower( trim( $this->request->data['AccountPassword']['email'] ) );
			unset($this->request->data['Account']['id']);
			unset($this->request->data['AccountPassword']['id']);
			$this->request->data['Account']['active'] = 0;
			$this->request->data['AccountFlag']['agreement_date'] = $this->Account->getDataSource()->expression('NOW()');
			$this->Account->create($this->request->data);
			if (! $this->Account->saveAll($this->request->data, array('validate' => 'only'))) {
				$this->Session->setFlash(__d('z', 'registration_data_validation_failure'), 'flash_bad');
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
					$token_length = Configure::read('z.token_length');
					$token_length = $token_length ? $token_length : PLUGIN_Z_DEFAULT_TOKEN_LENGTH;
					$this->request->data['AccountToken']['token'] = z_random_hex($token_length);
					$this->request->data['AccountToken']['expires'] = date('Y-m-d H:i:s', strtotime('+4 hours'));
					$this->_clean_old_tokens();
					if ( $this->Account->AccountToken->save($this->request->data) ) {
						$dataSource->commit();
						$fromurl = Router::url( array('action' => 'register'), true );
						$url = Router::url( array('action' => 'verify'), true );
						$urltoken = $url.'/t:'.$this->data['AccountToken']['token'].'/n:'.$this->data['AccountPassword']['email'].'';
						$frommail = Configure::read('Z.email_from');
						$sitename = Configure::read('Z.site_title');
						// and send an e-mail to the user
						$email = new CakeEmail();
						$email->viewVars(array(
							'sitename' => $sitename,
							'email' => $this->data['AccountPassword']['email'],
							'fromurl' => $fromurl,
							'token' => $this->data['AccountToken']['token'],
							'url' => $url,
							'urltoken' => $urltoken,
							));
						$email->emailFormat('text');
						$email->template('Z.verify', 'Z.comeclick');
						$email->from(array($frommail => $sitename));
						$email->to($this->data['AccountPassword']['email']);
						$email->subject('Confirm Registration for ' . $sitename);
						$email->send();
						$this->Session->setFlash(__d('z', 'user_created_successfully_check_email'), 'flash_good');
						$this->redirect(array('action' => 'verify'));
					} else {
						$dataSource->rollback();
						$this->Session->setFlash(__d('z', 'user_not_saved'), 'flash_bad');
					}
				} else {
					$dataSource->rollback();
					$this->Session->setFlash(__d('z', 'user_not_saved'), 'flash_bad');
				}
			} else {
				$dataSource->rollback();
				$this->Session->setFlash(__d('z', 'user_not_saved'), 'flash_bad');
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
			$this->request->data['AccountPassword']['email'] = 
				strtolower( trim( $this->request->data['AccountPassword']['email'] ) );
			$email = $this->request->data['AccountPassword']['email'];
			$token = $this->request->data['AccountToken']['token'];
		} else {
			$token = null;
		}
		$this->_clean_old_tokens(); // first remove expired tokens
		$this->_clean_old_registrations(); // remove expired registration requests
		if ( !empty($token) ) {
			$this->Account->recursive = 0;
			$result = $this->Account->AccountToken->find('first', array(
				'recursive' => 0,
				'conditions' => array(
					'AccountToken.token' => $token,
					'AccountToken.purpose' => PLUGIN_Z_TOKEN_MAIL_VERIFY,
					//'AccountPassword.email' => $email,
					//'Token.expires >=' => date('Y-m-d H:i:s')
					)
				));
			//$result = $this->Account->AccountToken->find('first',array(
			//	'joins' => array(
			//		array(
			//			'table' => 'z_passwords',
			//			'alias' => 'AccountPassword',
			//			'type' => 'LEFT',
			//			'conditions' => array(
			//				'AccountPassword.account_id = Account.id'
			//			)
			//		)
			//	),
			//	'conditions' => array(
			//		'AccountToken.token' => $token,
			//		'AccountToken.purpose' => PLUGIN_Z_TOKEN_MAIL_VERIFY,
			//		'AccountPassword.email' => $email,
			//	),
			//	//'fields' => array('AccountPassword.*', 'Account.*', 'AccountToken.*'),
			//));
			if ( !empty($result) ) {
				$this->Account->id = $result['AccountToken']['account_id'];
				$this->Account->recursive = 0;
				$this->Account->read();
				if ( $this->Account->data['AccountPassword']['email'] == $email ) {
					$data = array(
						'Account' => array(
							'id' => $this->Account->data['Account']['id'],
							'active' => 1
						),
						'AccountFlag' => array(
							'id' => $this->Account->data['AccountFlag']['id'],
							'account_id' => $this->Account->data['Account']['id'],
							'email_verified' => 1,
							'email_verified_date' => $this->Account->getDataSource()->expression('NOW()')
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
						$this->Session->setFlash(__d('z', 'user_not_saved'), 'flash_bad');
						return;
					}
					$this->Account->AccountToken->delete($result['AccountToken']['id']);
					$this->Session->setFlash(__d('z', 'email_verify_success'), 'flash_good');
					$this->redirect(array('action' => 'login'));
				} else {
					$this->Session->setFlash(__d('z', 'email_verify_fail'), 'flash_bad');
				}
			} else {
				$this->Session->setFlash(__d('z', 'email_verify_fail'), 'flash_bad');
			}
		}
	}

	//
	// User lost his password and requests a reset link
	public function reset() {
		if ( $this->Auth->user('id') ) {
			// User logged in, forward to change password
			$id = $this->Auth->user('id');
			$this->Session->setFlash(__d('z', 'already_logged_in_change_password'), 'flash_bad');
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
				$this->Session->setFlash(__d('z', 'incorrect_captcha_try_again'), 'flash_bad');
				$this->request->data['Account']['captcha'] = '';
				$this->Account->invalidate('captcha', __d('z', 'captcha_incorrect', true));
				return;
			}
			$this->request->data['AccountPassword']['email'] = 
				strtolower( trim( $this->request->data['AccountPassword']['email'] ) );
			// Find the user record by email
			$this->Account->recursive = 0; // prevent loading any hasMany rows
			$userData = $this->Account->find( 'first', array(
				'conditions' => array('AccountPassword.email' => $this->request->data['AccountPassword']['email'])
				));
			if ( ! is_array( $userData ) ) {
				// user was not found
				/// The e-mail address is not registered.
				$this->Session->setFlash(__d('z', 'email_not_registered'), 'flash_bad');
				$this->request->data['Account']['captcha'] = '';
				$this->Account->invalidate('email', __d('z', 'email_not_registered', true));
				return;
			} else {
				// user is found, proceed with token generation
				$token_length = Configure::read('z.token_length');
				$token_length = $token_length ? $token_length : PLUGIN_Z_DEFAULT_TOKEN_LENGTH;
				$data = array( 
					'AccountToken' => array(
						'account_id' => $userData['Account']['id'],
						'purpose' => PLUGIN_Z_TOKEN_RESET_CONFIRM,
						// User needs sometimes to copy/paste this
						// do not make the token too long
						'token' => z_random_hex($token_length),
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
					$urltoken = $url.'/t:'.$data['AccountToken']['token'].'/n:'.$this->request->data['AccountPassword']['email'].'';
					$frommail = Configure::read('Z.email_from');
					$sitename = Configure::read('Z.site_title');
					// and send an e-mail to the user
					$email = new CakeEmail();
					$email->viewVars(array(
						'sitename' => $sitename,
						'email' => $this->request->data['AccountPassword']['email'],
						'fromurl' => $fromurl,
						'token' => $data['AccountToken']['token'],
						'url' => $url,
						'urltoken' => $urltoken,
						));
					$email->emailFormat('text');
					$email->template('Z.reset', 'Z.comeclick');
					$email->from(array($frommail => $sitename));
					$email->to($this->request->data['AccountPassword']['email']);
					$email->subject('Confirm Password Reset for Your Account at ' . $sitename);
					$email->send();
					$this->Session->setFlash(__d('z', 'reset_success_check_email'), 'flash_good');
					$this->redirect(array('action' => 'confirm'));
				} else {
					$this->Session->setFlash(__d('z', 'reset_request_fail'), 'flash_bad');
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
			$this->request->data['AccountPassword']['email'] = 
				strtolower( trim( $this->request->data['AccountPassword']['email'] ) );
			$email = $this->request->data['AccountPassword']['email'];
			$token = $this->request->data['AccountToken']['token'];
			$password1 = $this->request->data['AccountPassword']['password'];
			$password2 = $this->request->data['AccountPassword']['password_confirm'];
			if ( empty($token) ) {
				$this->Session->setFlash(__d('z', 'missing_required_field'), 'flash_bad');
				$this->Account->AccountToken->invalidate('token', __d('z', 'required_field'), true);
				return;
			}
			if ( empty($email) ) {
				$this->Session->setFlash(__d('z', 'missing_required_field'), 'flash_bad');
				$this->Account->invalidate('email', __d('z', 'required_field'), true);
				return;
			}
			if ( empty($password1) ) {
				$this->Session->setFlash(__d('z', 'missing_required_field'), 'flash_bad');
				$this->Account->AccountPassword->invalidate('password', __d('z', 'required_field'), true);
				return;
			}
			if ( empty($password2) ) {
				$this->Session->setFlash(__d('z', 'missing_required_field'), 'flash_bad');
				$this->Account->AccountPassword->invalidate('password_confirm', __d('z', 'required_field'), true);
				return;
			}
			if ( !($password1 === $password2) ) {
				$this->Session->setFlash(__d('z', 'passwords_differ'), 'flash_bad');
				$this->Account->AccountPassword->invalidate('password', __d('z', 'passwords_must_match'), true);
				$this->Account->AccountPassword->invalidate('password_confirm', __d('z', 'passwords_must_match'), true);
				return;
			}
			$result = $this->Account->AccountToken->find('first', array(
				'conditions' => array(
					'AccountToken.token' => $token,
					//'AccountPassword.email' => $email,
					'AccountToken.purpose' => PLUGIN_Z_TOKEN_RESET_CONFIRM
					)
				));
			if ( empty($result) ) {
				$this->Session->setFlash(__d('z', 'token_verify_fail'), 'flash_bad');
				$this->Account->AccountToken->invalidate('token', __d('z', 'token_incorrect'), true);
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
				$this->Session->setFlash(__d('z', 'internal_error_password_retrieval_failed'), 'flash_bad');
				return;
			}
			if ( $result['AccountPassword']['email'] != $email ) {
				$this->Session->setFlash(__d('z', 'token_verify_fail'), 'flash_bad');
				$this->Account->AccountPassword->invalidate('email', __d('z', 'email_incorrect'), true);
				return;
			}
			$result['AccountPassword']['password'] = $password1;
			//unset($result['AccountPassword']['salt']);
			if ( $this->Account->AccountPassword->save($result) ) {
				$this->Account->AccountToken->delete($token_id);
				$this->Session->setFlash(__d('z', 'password_change_success'), 'flash_good');
				$this->redirect(array('action' => 'login'));
			} else {
				$this->Session->setFlash(__d('z', 'password_change_fail'), 'flash_bad');
			}
		} else {
			if (isset($this->passedArgs['t']) && isset($this->passedArgs['n'])){
				$this->passedArgs = Sanitize::clean($this->passedArgs, array('encode' => false));
				$this->request->data['AccountPassword']['email'] = $this->passedArgs['n'];
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
		// Here we need to find all user records that are
		// - not active
		// - with e-mail not verified
		// - with no outstanding e-mail tokens
		// and delete them.
		$outstanding = $this->Account->find('all', array(
				'recursive' => 1,
				'conditions' => array(
					'Account.active' => false,
					'AccountFlag.email_verified' => false,
					'AccountFlag.deleted' => false
				)
			)
		);
		foreach ( $outstanding as $i => $outrec ) {
			if ( empty($outrec['AccountToken']) ) {
				//
				// Cascading is done without using transactions
				// so we do a transaction manually
				$dataSource = $this->Account->getDataSource();
				$dataSource->begin();
				$this->Account->delete($outrec['Account']['id'], true);
				$dataSource->commit();
			}
		};
	}
	protected function _block_robots() {
		// The forms have a hidden field that is supposed
		// to be left empty. If it is filled in, we are
		// dealing with an overzealous robot.
		if ( !empty($this->request->data['Account']['ruhuman']) ) {
			/// We do not accept registrations from bots.
			$this->Session->setFlash(__d('z', 'bots_are_not_welcome'), 'flash_bad');
			$this->redirect(array('plugin' => null, 'controller' => 'pages', 'action' => 'index'));
			return true; // just in case
		} else {
			return false;
		}
	}

	public function tos() {
		// Display Terms of Service for this plugin and the use of cookies
		// Only the view is used, so nothing to see here
	}
}
