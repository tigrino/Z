<?php
App::uses('AccountToken', 'Z.Model');

/**
 * AccountToken Test Case
 *
 */
class AccountTokenTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.z.account_token',
		'plugin.z.account'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->AccountToken = ClassRegistry::init('Z.AccountToken');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->AccountToken);

		parent::tearDown();
	}

}
