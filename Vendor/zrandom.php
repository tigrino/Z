<?php
	/*
	 * Generate random numbers to the best of our ability
	 * albert@tigr.net
	 * 
	 * With thanks to George Argyros and Aggelos Kiayias 
	 */
	//
	// Return the specified number of hex digits
	function z_random_hex($len = 32) {
		return substr(bin2hex(z_random($len/2)), 0, ceil($len));
	}
	//
	// Return the specified number of bytes
	function z_random($len = 16) {
		// Do we need to round it up or not?
		// Do we need it right away for everything?
		$len = ceil($len);
		// if a secure randomness generator exists and we don't have a buggy PHP version use it.
		if (function_exists('openssl_random_pseudo_bytes') &&
			(version_compare(PHP_VERSION, '5.3.4') >= 0 || substr(PHP_OS, 0, 3) !== 'WIN')) {
			//$str = bin2hex(openssl_random_pseudo_bytes(($len/2)+1, $strong));
			$str = openssl_random_pseudo_bytes(($len)+1, $strong);
			if ($strong == true)
				return substr($str, 0, $len);
		}
		//collect any entropy available in the system along with a number
		//of time measurements or operating system randomness.
		$str = '';
		$bits_per_round = 2;
		$msec_per_round = 400;
		$hash_len = 20; // SHA-1 Hash length
		$total = ceil($len); // total bytes of entropy to collect
		do {
			$bytes = ($total > $hash_len)? $hash_len : $total;
			$total -= $bytes;
			//collect any entropy available from the PHP system and filesystem
			$entropy = rand() . uniqid(mt_rand(), true);
			$entropy .= implode('', @fstat(fopen( __FILE__, 'r')));
			$entropy .= memory_get_usage();
			if(@is_readable('/dev/urandom') && ($handle = @fopen('/dev/urandom', 'rb'))) {
				$entropy .= @fread($handle, $bytes);
				@fclose($handle);
			} else {
				// Measure the time that the operations will take on average
				for ($i = 0; $i < 3; $i ++) {
					$c1 = microtime() * 1000000;
					$var = sha1(mt_rand());
					for ($j = 0; $j < 50; $j++) {
						$var = sha1($var);
					}
					$c2 = microtime() * 1000000;
					$entropy .= $c1 . $c2;
				}
				if ($c1 > $c2) $c2 += 1000000;
				// Based on the above measurement determine the total rounds
				// in order to bound the total running time.
				$rounds = (int)(($msec_per_round / ($c2-$c1))*50);
				// Take the additional measurements. On average we can expect
				// at least $bits_per_round bits of entropy from each measurement.
				$iter = $bytes*(int)(ceil(8 / $bits_per_round));
				for ($i = 0; $i < $iter; $i ++) {
					$c1 = microtime();
					$var = sha1(mt_rand());
					for ($j = 0; $j < $rounds; $j++) {
						$var = sha1($var);
					}
					$c2 = microtime();
					$entropy .= $c1 . $c2;
				}
			}
			// We assume sha1 is a deterministic extractor for the $entropy variable.
			$str .= sha1($entropy, TRUE);
		} while ($len > strlen($str));
		return substr($str, 0, $len);
	}

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

// Counts how many bits are needed to represent $value
function count_bits($value) {
    for($count = 0; $value != 0; $value >>= 1) {
        ++$count;
    }
    return $count;
}

// Returns a base16 random string of at least $bits bits
// Actual bits returned will be a multiple of 4 (1 hex digit)
function random_bits($bits) {
    $result = '';
    $accumulated_bits = 0;
    $total_bits = count_bits(mt_getrandmax());
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
function base_convert_arbitrary($number, $fromBase, $toBase) {
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

// Generate a 64 bit id as a decimal string (suitable for MySQL BIGINT)
function z_generate_id() {
	return str_pad(base_convert_arbitrary(random_bits(64), 16, 10), 20, '0', STR_PAD_LEFT);
}

//printf("[%s]", base_convert_arbitrary('ffffffffffffffff', 16, 10) == '18446744073709551615');
//printf("[%s]\n", base_convert_arbitrary('10000000000000000', 16, 10) == '18446744073709551616');
//$randnum = random_bits(32);
//printf("[%s=%s]\n", str_pad($randnum, 16, '0', STR_PAD_LEFT), str_pad(base_convert_arbitrary($randnum, 16, 10), 20, '0', STR_PAD_LEFT));
//$randnum = random_bits(64);
//printf("[%s=%s]\n", str_pad($randnum, 16, '0', STR_PAD_LEFT), str_pad(base_convert_arbitrary($randnum, 16, 10), 20, '0', STR_PAD_LEFT));
//for ($i = 0; $i < 10; ++$i) {
//	$rid = z_generate_id();
//	printf("[id=%s]\n", $rid);
//}
//printf("\n");
?>
