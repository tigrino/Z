<?php
App::uses('ZAppModel', 'Z.Model');
/**
 * AccountFlag Model
 *
 * @property Account $Account
 */
class AccountFlag extends ZAppModel {
	public $useTable = 'z_account_flags';
	public $validate = array(
		'agreement' => array(
			'set' => array(
				'rule'    => array('equalTo', '1'),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'The agreement to Terms of Service is required.',
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
}
