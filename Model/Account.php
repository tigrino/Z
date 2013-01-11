<?php
App::uses('ZAppModel', 'Z.Model');
//App::import('Vendor', 'Z.zrandom');

class Account extends ZAppModel {
	public $validationDomain = 'z';
	public $useTable='z_accounts';
	public $displayField = 'email';
	public $actsAs = array(
		'Z.RandomId' => array(
			'id_field' => 'id',
			'id_length' => 19
		),
	);

	public $validate = array(
		'maxLength' => array(
			'rule'    => array('maxLength', 255),
			'message' => 'email_max_length %d'
		),
		'minLength' => array(
			'rule'    => array('minLength', 5),
			'message' => 'email_min_length %d'
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'email_need_valid_email',
				'allowEmpty' => false,
				'required' => true,
			),
		'unique' => array(
				'rule' => 'isUnique',
				'required' => true,
				'message' => 'email_already_registered'
			),
		),
	);

	public $hasMany = array(
		'AccountLogin' => array(
			'className' => 'Z.AccountLogin',
			'foreignKey' => false,
			'conditions' => '1=0',
			//'conditions' => array('AccountLogin.email' => $data['Account']['email']),
			'fields' => array('email', 'from_ip', 'created', 'success'),
			//'order' => '',
			//'limit' => '1',
			'recursive' => null,
			'dependent'    => false
		),
		'AccountToken' => array(
			'className' => 'Z.AccountToken',
			'foreignKey' => 'account_id',
			//'conditions' => '',
			//'fields' => '',
			//'order' => '',
			//'order' => array('Visit.created' => 'desc'),
			//'limit' => '1',
			'dependent'    => true
		)
	);
	public $hasOne = array(
		'AccountPassword' => array(
			'className' => 'Z.AccountPassword',
			'foreignKey' => 'account_id',
			//'conditions' => '',
			//'fields' => '',
			//'order' => '',
			'dependent'    => true
		),
		'AccountFlag' => array(
			'className' => 'Z.AccountFlag',
			'foreignKey' => 'account_id',
			//'conditions' => '',
			//'fields' => '',
			//'order' => '',
			//'order' => array('Visit.created' => 'desc'),
			//'limit' => '1',
			'dependent'    => true
		),
	);
	public function setAccountLoginLimit( $new_limit ) {
		$this->hasMany['AccountLogin']['limit'] = $new_limit;
	}
	public function beforeSave($options = array()) {
		//debug($this);
		parent::beforeSave($options);
	}

	public function beforeFind( $queryData ) {
		//$this->_bindRecentVisit();
		return parent::beforeFind($queryData);
	}
	public function afterFind( $results, $primary = false ) {
		if (!isset($results[0]['Account'])) {
			// no results (e.g. count only) - abort
			return $results;
		}
		if (!isset($results[0]['AccountLogin'])) {
			// no results or no recursion - abort
			return $results;
		}
		$query = array(
			'conditions' => array(),
			'recursive' => isset($this->hasMany['AccountLogin']['recursive']) ?
				($this->recursive - 1) : null,
			'fields' => $this->hasMany['AccountLogin']['fields'],
			'limit' => $this->hasMany['AccountLogin']['limit'],
			'order' => $this->hasMany['AccountLogin']['order'],
			);
		//debug($results);
		foreach ($results as $i => $row) {
			$query['conditions'] = array('AccountLogin.email' => $row['Account']['email']);
			$accesses = $this->AccountLogin->find('all', $query);
			foreach ($accesses as $rowIndex => $subRow) {
				$access = $subRow['AccountLogin'];
				unset($subRow['AccountLogin']);
				// What on Earth would this loop do?
				//foreach ($subRow as $key => $value) {
				//	$access[$key] = $value;
				//}
				$results[$i]['AccountLogin'][$rowIndex] = $access;
			}
		}
		//debug($results);
		return $results;
	}

/*
	function _bindRecentVisit() {
		if ( isset($this->hasOne['RecentVisit'])) { return; }
		$dbo = $this->Visit->getDatasource();
		$subQuery = String::insert("`RecentVisit`.`id` = (:q)", array(
			'q'=>$dbo->buildStatement(array(
				'fields' => array( String::insert(':sqVisit:eq.:sqid:eq', array('sq'=>$dbo->startQuote, 'eq'=>$dbo->endQuote))),
				'table'  => $dbo->fullTableName($this->Visit),
				'alias'  => 'Visit',
				'limit'  => 1,
				'order'  => array('Visit.created'=>'DESC'),
				'group'  => null,
				'conditions' => array(
					'Visit.account_id = Account.id'
				)
				), $this->Visit)
		));
		$this->bindModel(array('hasOne'=>array(
				'RecentVisit'=>array(
				'className' => 'Visit',
				'conditions' => array( $subQuery )
				)
			)),false);
	}
*/
}
