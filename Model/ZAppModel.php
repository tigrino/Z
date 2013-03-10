<?php
App::uses('AppModel', 'Model');

class ZAppModel extends AppModel {
	public $validationDomain = 'z';
	public $actsAs = array(
		'Containable'
	);
}

