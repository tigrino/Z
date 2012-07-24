<?php
	/*
	 * Generate a random number to the best of our ability
	 * albert@tigr.net
	 */
	function z_random_64() {
		//$maxrand = mt_getrandmax();
		// maxrand is 2147483647 on my machine
		// that is 2^31-1, i.e. 32 bits signed
		// MySQL limit is 18446744073709551615
		// that is 2^64-1
		// So we need to generate more than 2 randoms anyway...
		// This method is simple but loses a few bits on the way...
		// I will need to rewrite this some day with pack()...
		$rand = array();
		$rand[1] = mt_rand(0, 18445);
		$rand[2] = mt_rand(0, 99999);
		$rand[3] = mt_rand(0, 99999);
		$rand[4] = mt_rand(0, 99999);
		$ready = 
			str_pad($rand[1], 5, '0', STR_PAD_LEFT) . 
			str_pad($rand[2], 5, '0', STR_PAD_LEFT) . 
			str_pad($rand[3], 5, '0', STR_PAD_LEFT) . 
			str_pad($rand[4], 5, '0', STR_PAD_LEFT);
		return($ready);
	};
	function z_random_base64_32() {
		// 32 characters (24 real symbols + ~33% more for encoding) 
		// long base64 encoded random string
		return base64_encode(pack('N6', mt_rand(), mt_rand(), 
			mt_rand(), mt_rand(), mt_rand(), mt_rand()));
	};
	function z_random_base64_64() {
		// 64 characters (48 real symbols + ~33% more for encoding) 
		// long base64 encoded random string
		return base64_encode(pack('N12', mt_rand(), mt_rand(), 
			mt_rand(), mt_rand(), mt_rand(), mt_rand(),
			mt_rand(), mt_rand(), mt_rand(), mt_rand(),
			mt_rand(), mt_rand()
			));
	};
	function z_random_hex_32() {
		// 32 hex characters (16 byte) random string
		return bin2hex(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()));
	};
	function z_random_hex_64() {
		// 64 hex characters (32 byte) random string
		return bin2hex(pack('N8', mt_rand(), mt_rand(), mt_rand(), mt_rand(), 
					mt_rand(), mt_rand(), mt_rand(), mt_rand()));
	};
	function z_random_hex_128() {
		// 128 hex characters (64 byte) random string
		return bin2hex(pack('N16', mt_rand(), mt_rand(), mt_rand(), mt_rand(), 
					mt_rand(), mt_rand(), mt_rand(), mt_rand(),
					mt_rand(), mt_rand(), mt_rand(), mt_rand(), 
					mt_rand(), mt_rand(), mt_rand(), mt_rand()));
	};
	function z_random_hex_256() {
		// 256 hex characters (128 byte) random string
		return bin2hex(pack('N32', mt_rand(), mt_rand(), mt_rand(), mt_rand(), 
					mt_rand(), mt_rand(), mt_rand(), mt_rand(),
					mt_rand(), mt_rand(), mt_rand(), mt_rand(), 
					mt_rand(), mt_rand(), mt_rand(), mt_rand(),
					mt_rand(), mt_rand(), mt_rand(), mt_rand(), 
					mt_rand(), mt_rand(), mt_rand(), mt_rand(),
					mt_rand(), mt_rand(), mt_rand(), mt_rand(), 
					mt_rand(), mt_rand(), mt_rand(), mt_rand()));
	};
	function z_hash_hex_512($data) {
		return hash('sha512', $data, FALSE);
	};
	function z_hash_hex_sha1($data) {
		return hash('sha1', $data, FALSE);
	};
	//$r = z_random_64();
	//$r = z_random_base64_64();
	//$r = z_hash_hex_sha1(z_random_base64_64());
	//$r = z_random_hex_64();
	//$r = z_random_hex_256();
	//printf ("[%u]:[%s]\n", strlen($r), $r);
?>
