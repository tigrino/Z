<?php
App::uses('ZAppController', 'Z.Controller');

class UsersController extends ZAppController {

	public function index() {
		$this->redirect(array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'index'));
	}

	public function view() {
		$this->redirect(array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'view'));
	}

}
