<?php
App::uses('AccountFlag', 'Z.Model');

/**
 * AccountFlag Test Case
 *
 */
class AccountFlagTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.z.account_flag',
		'plugin.z.account'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AccountFlag = ClassRegistry::init('Z.AccountFlag');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AccountFlag);

		parent::tearDown();
	}

}
