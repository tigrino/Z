<?php
App::uses('ZAppController', 'Z.Controller');

class UsersController extends ZAppController {

	public function index() {
		$this->redirect(array('action' => 'view'));
	}

	public function view() {
		//debug($this->request);
		if ( ! $this->Auth->user('id') ) {
			// User is not logged in, forward to login
			$this->Session->setFlash(__('The requested action requires you to be logged in.'));
			$this->redirect(array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'login'));
		} else {
			// User is logged in
			$id = $this->Auth->user('id');
			$this->User->id = $id;
			if (!$this->User->exists()) {
				// A strange situation when the logged in
				// user actually does not exist
				// but maybe he was blocked? Log him out.
				$this->redirect($this->Auth->logout());
				//throw new NotFoundException(__('Invalid user'));
			}
			$this->set('user', $this->User->read(null, $id));
		}
	}

}
