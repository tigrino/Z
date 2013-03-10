<?php
App::uses('ZAppModel', 'Z.Model');

class AccountLogin extends ZAppModel {
	public $validationDomain = 'z';
	public $useTable = 'z_password_logins';
	public $displayField = 'account_id';
        public $belongsTo = array(
                'AccountPassword' => array(
                        'className' => 'Z.AccountPassword',
                        'foreignKey' => false,
                        'conditions' => array('`AccountPassword`.`email` = `AccountLogin`.`email`'),
                        'fields' => '',
                        'order' => ''
                )
        );
	public $validate = array(
	);
}
