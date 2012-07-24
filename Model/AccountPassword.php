<?php
App::uses('ZAppModel', 'Z.Model');
App::import('Vendor', 'Z.zrandom');

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
		$commonwords = array(
			'12',
			'123',
			'1234',
			'12345',
			'123456',
			'1234567',
			'12345678',
			'123456789',
			'1234567890',
			'11',
			'111',
			'1111',
			'11111',
			'111111',
			'1111111',
			'11111111',
			'123123',
			'21',
			'321',
			'4321',
			'54321',
			'654321',
			'7654321',
			'87654321',
			'987654321',
			'0987654321',
			'7777777',
			'112233',
			'abc123',
			'password', 'qwerty', 'monkey', 'letmein',
			'trustno1', 'dragon', 'baseball', 'iloveyou',
			'master', 'sunshine', 'ashley', 'bailey',
			'passwOrd', 'shadow', 'superman', 'qazwsx',
			'michael', 'football', 'jesus', 'love',
			'christ', 'jesus1', 'princess', 'blessed',
			'sunshine', 'faith', 'angel', 'single',
			'lovely', 'freedom', 'blessing', 'grace',
			'heaven', 'angels', 'shadow', 'tigger',
			'summer', 'hope', 'looking', 'peace',
			'mother', 'shalom', 'rotimi', 'victory',
			'happy', 'purple', 'john316', 'joshua',
			'london', 'church', 'loving', 'computer',
			'mylove', 'praise', 'saved', 'richard',
			'pastor', 'test', 'letmein', 'trustno1',
			'dragon', 'hello', 'monkey', 'master',
			'killer', 'computer', 'asdf', 'internet',
			'whatever', 'starwars', 'cheese', 'cocacola',
			'none', 'god', 'emmanuel', 'fuckoff',
			'john', '1q2w3e4r', 'red123', 'blabla',
			'qwert', 'angel1', 'hallo', 'hotdog',
		);
		return ! in_array($check['password'], $commonwords);
	}
}
