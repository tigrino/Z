<?php
App::uses('ZAppModel', 'Z.Model');

class AccountLogin extends ZAppModel {
	public $useTable = 'z_account_logins';
	public $displayField = 'account_id';
	public $belongsTo = array( 'Z.Account' );
	public $validate = array(
		'account_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'required' => true,
			),
		),
	);
}
