<?php
/**
 * AccountTokenFixture
 *
 */
class AccountTokenFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20, 'key' => 'primary'),
		'account_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 20),
		'token' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'purpose' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 4),
		'expires' => array('type' => 'datetime', 'null' => true, 'default' => null),
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
			'token' => 'Lorem ipsum dolor sit amet',
			'purpose' => 1,
			'expires' => '2012-07-01 15:31:30',
			'created' => '2012-07-01 15:31:30',
			'modified' => '2012-07-01 15:31:30'
		),
	);

}
