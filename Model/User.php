<?php
App::uses('ZAppModel', 'Z.Model');
App::import('Vendor', 'Z.zrandom');
/**
 * User Model
 */
class User extends ZAppModel {
	public $name = 'User';
	public $validationDomain = 'z';
	public $useTable = 'users';
	public $primaryKey = 'id';
	public $displayField = 'email';

	public function beforeSave($options = array()) {
		// This is a read-only model
		return false;
	}
}
