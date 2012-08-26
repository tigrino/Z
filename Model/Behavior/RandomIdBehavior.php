<?php

/*
 * CakePHP RandomId Behavior
 * Automatically generates random id fields.
 * @version 1.0
 * @author Albert Tigr <albert@tigr.net>
 * @copyright Copyright (c) 2012, Albert Tigr
 * @license MIT
 */

class RandomIdBehavior extends ModelBehavior
{
	public $settings = array();
	
	public function setup(&$model, $settings)
	{
		if (!isset($this->settings[$model->alias])) {
			$this->settings[$model->alias] = array(
				'id_field' => 'id',
				'id_length' => 20
			);
		}
		$this->settings[$model->alias] = array_merge(
			$this->settings[$model->alias],
			(array)$settings
		);
	}
	
	/*
	 * Check if the id field is empty and if so - generates a random id.
	 */
	public function beforeValidate(&$model) {
		if ($this->_record_needs_id($model)){
			$this->_generate_id($model);
		}
		return true;
	}
	
	/*
	 * Check if the record has an empty id field
	 */
	private function _record_needs_id(&$model)
	{
		if ((!isset($model->id)) || empty($model->id)) {
			return true;
			
		} else {
			return false;
		}
	}
	
	/*
	 * Generate the new id
	 */
	private function _generate_id(&$model)
	{
		do {
			// generate ID
			$ready_id = $this->_generate_random($this->settings[$model->alias]['id_length']);
			// MAKE SURE IT DOES NOT EXIST YET
			$existing_id = $model->find(
				'count', 
				array(
					'recursive' => -1, 
					'conditions' => array($model->alias . '.' . $this->settings[$model->alias]['id_field'] => $ready_id)
				)
			);
		} while ((!empty($existing_id)) || ($ready_id == '00000000000000000000'));
		$model->data[$model->alias][$this->settings[$model->alias]['id_field']] = $ready_id;
	}
	/*
	 * Generate a random number
	 * This is not cryptographically strong just fairly unpredictable
	 */
	private function _generate_random($length) {
		$ready = '';
		do {
			$rand = mt_rand(0, 99999);
			$rand = str_pad($rand, 5, '0', STR_PAD_LEFT);
			$togo = $length - strlen($ready);
			$ready .= substr($rand, -(($togo > 5)? 5 : $togo));
		} while (($length - strlen($ready)) > 0);
		return($ready);
	}
}
