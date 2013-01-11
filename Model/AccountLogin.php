<?php
App::uses('ZAppModel', 'Z.Model');

class AccountLogin extends ZAppModel {
	public $validationDomain = 'z';
	public $useTable = 'z_account_logins';
	public $displayField = 'account_id';
        public $belongsTo = array(
                'Account' => array(
                        'className' => 'Z.Account',
                        'foreignKey' => false,
                        'conditions' => array('`Account`.`email` = `AccountLogin`.`email`'),
                        'fields' => '',
                        'order' => ''
                )
        );
	public $validate = array(
	);
}
