<?php
App::uses('ZAppModel', 'Z.Model');
App::import('Vendor', 'Z.zrandom');
App::import('Vendor', 'Z.zpasswords');

class AccountPassword extends ZAppModel {
	public $validationDomain = 'z';
	public $useTable = 'z_account_passwords';
	var $name = 'AccountPassword';
	public $displayField = 'password';

	public function beforeSave($options = array()) {
		//debug($this->data['AccountPassword']['password']);
		//debug($this->data);
		//
		// We should really check here for isset() perhaps
		// The password may be empty but the data field 
		// should be set. Otherwise we may be updating salt
		// without being passed a password...
		// Although this should not happen on a table that's
		// dedicated to saving passwords but we all know
		// about things that "should not happen" :)
		//if ( !empty( $this->data['AccountPassword']['password'] ) ) {
			// saving a password, need salt
			if ( empty( $this->data['AccountPassword']['salt'] ) ) {
				$this->data['AccountPassword']['salt'] = z_random_base64_64();
				$this->data['AccountPassword']['password'] =
					$this->data['AccountPassword']['salt'] .
					$this->data['AccountPassword']['password'];
			}
		//}
		// Hash the password
		$this->data['AccountPassword']['password'] = AuthComponent::password($this->data['AccountPassword']['password']);
		if ( empty( $this->data['AccountPassword']['id'] ) ) {
			// A new record, generate ID
			do {
				$ready_id = z_random_64();
				// MAKE SURE IT DOES NOT EXIST YET
				// Otherwise we risk overwriting an existing record
				$existing_id = $this->find('first', array('recursive' => -1, 'conditions' => array('AccountPassword.id' => $ready_id)));
			} while (!empty($existing_id));
			$this->id = $ready_id;
			$this->data['AccountPassword']['id'] = $ready_id;
		}
		//debug($this->data);
		return parent::beforeSave();
	}
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'password_empty_not_allowed',
				'allowEmpty' => false,
				'required' => true,
			),
			'maxLength' => array(
				'rule'    => array('maxLength', 255),
				'message' => 'password_max_length %d'
			),
			'minLength' => array(
				'rule'    => array('minLength', 6),
				'message' => 'password_min_length %d'
			),
			'notInList' => array(
				'rule'    => array('notInCommonWordList'),
				'message' => 'password_in_common_list'
			),
		),
		/*'salt' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'required' => true,
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),*/
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Account' => array(
			'className' => 'Z.Account',
			'foreignKey' => 'account_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function notInCommonWordList($check) {
		return ! z_password_listed($check['password']);
	}
}
