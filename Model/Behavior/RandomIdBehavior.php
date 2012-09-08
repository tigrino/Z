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
				'id_bits' => 64,
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
			$ready_id = str_pad($this->_base_convert_arbitrary($this->_random_bits(64), 16, 10), 20, '0', STR_PAD_LEFT);
			// MAKE SURE IT DOES NOT EXIST YET
			$existing_id = $model->find(
				'count', 
				array(
					'recursive' => -1, 
					'conditions' => array($model->alias . '.' . $this->settings[$model->alias]['id_field'] => $ready_id)
				)
			);
		} while ((!empty($existing_id)) || ($ready_id == str_repeat('0', strlen($ready_id))));
		$model->data[$model->alias][$this->settings[$model->alias]['id_field']] = $ready_id;
	}

	// Counts how many bits are needed to represent $value
	private function _count_bits($value) {
	    for($count = 0; $value != 0; $value >>= 1) {
		++$count;
	    }
	    return $count;
	}

	// With thanks to Jon of stackoverflow 
	// http://stackoverflow.com/users/50079/jon
	//
	// Returns a base16 random string of at least $bits bits
	// Actual bits returned will be a multiple of 4 (1 hex digit)
	//
	// This is likely sufficiently good for generating IDs
	// although still not cryptographically strong
	private function _random_bits($bits) {
	    $result = '';
	    $accumulated_bits = 0;
	    $total_bits = $this->_count_bits(mt_getrandmax());
	    $usable_bits = intval($total_bits / 8) * 8;

	    while ($accumulated_bits < $bits) {
		$bits_to_add = min($total_bits - $usable_bits, $bits - $accumulated_bits);
		if ($bits_to_add % 4 != 0) {
		    // add bits in whole increments of 4
		    $bits_to_add += 4 - $bits_to_add % 4;
		}

		// isolate leftmost $bits_to_add from mt_rand() result
		$more_bits = mt_rand() & ((1 << $bits_to_add) - 1);

		// format as hex (this will be safe)
		$format_string = '%0'.($bits_to_add / 4).'x';
		$result .= sprintf($format_string, $more_bits);
		$accumulated_bits += $bits_to_add;
	    }

	    return $result;
	}

	// With thanks to Jon of stackoverflow 
	// http://stackoverflow.com/users/50079/jon
	//
	// Convert the bases for large numbers
	private function _base_convert_arbitrary($number, $fromBase, $toBase) {
	    $digits = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $length = strlen($number);
	    $result = '';

	    $nibbles = array();
	    for ($i = 0; $i < $length; ++$i) {
		$nibbles[$i] = strpos($digits, $number[$i]);
	    }

	    do {
		$value = 0;
		$newlen = 0;
		for ($i = 0; $i < $length; ++$i) {
		    $value = $value * $fromBase + $nibbles[$i];
		    if ($value >= $toBase) {
			$nibbles[$newlen++] = (int)($value / $toBase);
			$value %= $toBase;
		    }
		    else if ($newlen > 0) {
			$nibbles[$newlen++] = 0;
		    }
		}
		$length = $newlen;
		$result = $digits[$value].$result;
	    }
	    while ($newlen != 0);
	    return $result;
	}

}
