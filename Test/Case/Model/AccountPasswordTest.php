<?php
App::uses('AccountPassword', 'Z.Model');

/**
 * AccountPassword Test Case
 *
 */
class AccountPasswordTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.z.account_password',
		'plugin.z.account'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AccountPassword = ClassRegistry::init('Z.AccountPassword');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AccountPassword);

		parent::tearDown();
	}

}
