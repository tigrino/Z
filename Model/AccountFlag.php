<?php
App::uses('ZAppModel', 'Z.Model');
/**
 * AccountFlag Model
 *
 * @property Account $Account
 */
class AccountFlag extends ZAppModel {
	public $validationDomain = 'z';
	public $useTable = 'z_flags';
	var $actsAs = array('Z.RandomId');
	public $validate = array(
		'agreement' => array(
			'set' => array(
				'rule'    => array('equalTo', '1'),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'tos_agreement_required',
				'on' => 'create', // Limit validation to 'create' or 'update' operations
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
