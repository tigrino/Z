<?php
/**
 * AccountFlagFixture
 *
 */
class AccountFlagFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'account_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20),
		'user_admin' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'agreement' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'agreement_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'email_verified' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'email_verified_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'deleted' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'deleted_date' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'account_id' => 1,
			'user_admin' => 1,
			'agreement' => 1,
			'agreement_date' => '2012-07-01 15:30:30',
			'email_verified' => 1,
			'email_verified_date' => '2012-07-01 15:30:30',
			'deleted' => 1,
			'deleted_date' => '2012-07-01 15:30:30',
			'created' => '2012-07-01 15:30:30',
			'modified' => '2012-07-01 15:30:30'
		),
	);

}
