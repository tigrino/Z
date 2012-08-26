<?php
App::uses('ZAppModel', 'Z.Model');
App::import('Vendor', 'Z.zpasswordblacklist');
App::import('Vendor', 'Z.PasswordHash');

class AccountPassword extends ZAppModel {
	public $validationDomain = 'z';
	public $useTable = 'z_account_passwords';
	var $name = 'AccountPassword';
	public $displayField = 'password';
	var $actsAs = array(
		'Z.RandomId' => array(
			'id_field' => 'id',
			'id_length' => 19
		),
	);

	public function beforeSave($options = array()) {
		// Hash the password
		$hasher = new PasswordHash(PLUGIN_Z_PASSWORD_HASH_COST, FALSE);
		$this->data['AccountPassword']['password'] = 
			$hasher->HashPassword($this->data['AccountPassword']['password']);
		unset( $hasher );
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
				// an arbitrary value, really
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
