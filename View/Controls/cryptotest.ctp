<?php
App::import('Vendor', 'Z.PasswordHash');
App::import('Vendor', 'Z.zrandom');
?>
<div class="accounts index">
	<h2><?php echo __d('z', 'Accounts Control Dashboard - Cryptography Tests'); ?></h2>
<h3>Hash and random speed</h3>
<table cellpadding="0" cellspacing="0">
<tr><th>Random length</th><th>Execution time (sec.)</th><th>Random length</th><th>Random value</th></tr>
<?php
function print_hash($ref, $timing, $hashvalue) {
	echo "<tr><td> " . $ref . "</td><td>" . number_format($timing, 16) . '</td><td>' . strlen($hashvalue) . '</td><td>' . $hashvalue . "</td></tr>\n";
}

for ($i=24; $i<=64; $i+=4) {
	$timing = microtime(true);
	$hashvalue = z_random_hex($i);
	$timing = microtime(true) - $timing;
	print_hash($i*4 . ' bit', $timing, $hashvalue);
}
?>

</table>
<table cellpadding="0" cellspacing="0">
<tr><th>Algorithm</th><th>Execution time (sec.)</th><th>Hash length</th><th>Hash value</th></tr>

<?php
$correct = 'test12345';
if (CRYPT_STD_DES == 1) {
        $timing = microtime(true);
	$hashvalue = crypt($correct, 'rl');
        $timing = microtime(true) - $timing;
	print_hash('Standard DES', $timing, $hashvalue);
} else {
	echo "<tr><td>Standard DES</td><td>-</td><td>-</td><td>-</td></tr>\n";
}

if (CRYPT_EXT_DES == 1) {
        $timing = microtime(true);
        $hashvalue = crypt($correct, '_J9..rasm');
        $timing = microtime(true) - $timing;
	print_hash('Extended DES', $timing, $hashvalue);
} else {
	echo "<tr><td>Extended DES</td><td>-</td><td>-</td><td>-</td></tr>\n";
}

if (CRYPT_MD5 == 1) {
        $timing = microtime(true);
        $hashvalue = crypt($correct, '$1$rasmusle$');
        $timing = microtime(true) - $timing;
	print_hash('MD5', $timing, $hashvalue);
} else {
	echo "<tr><td>MD5</td><td>-</td><td>-</td><td>-</td></tr>\n";
}

if (CRYPT_BLOWFISH == 1) {
        $timing = microtime(true);
        $hashvalue = crypt($correct, '$2a$07$usesomesillystringforsalt$');
        $timing = microtime(true) - $timing;
	print_hash('Blowfish', $timing, $hashvalue);
} else {
	echo "<tr><td>Blowfish</td><td>-</td><td>-</td><td>-</td></tr>\n";
}

if (CRYPT_SHA256 == 1) {
        $timing = microtime(true);
        $hashvalue = crypt($correct, '$5$rounds=5000$usesomesillystringforsalt$');
        $timing = microtime(true) - $timing;
	print_hash('SHA-256', $timing, $hashvalue);
} else {
	echo "<tr><td>SHA-256</td><td>-</td><td>-</td><td>-</td></tr>\n";
}

if (CRYPT_SHA512 == 1) {
        $timing = microtime(true);
        $hashvalue = crypt($correct, '$6$rounds=5000$usesomesillystringforsalt$');
        $timing = microtime(true) - $timing;
	print_hash('SHA-512', $timing, $hashvalue);
} else {
	echo "<tr><td>SHA-512</td><td>-</td><td>-</td><td>-</td></tr>\n";
}


$correct = 'test12345';
# Force the use of weaker portable hashes.
$t_hasher = new PasswordHash(8, TRUE);
$timing = microtime(true);
$hashvalue = $t_hasher->HashPassword($correct);
$timing = microtime(true) - $timing;
print_hash('phphash-md5', $timing, $hashvalue);
unset($t_hasher);
# Standard with various iteration count
for ($i = 4; $i <= 16; $i+=2) {
        $t_hasher = new PasswordHash($i, FALSE);
        $timing = microtime(true);
        $hashvalue = $t_hasher->HashPassword($correct);
        $timing = microtime(true) - $timing;
	print_hash('phphash-' . $i, $timing, $hashvalue);
        unset($t_hasher);
}

?>
</table>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Dashboard'), array('action' => 'dashboard')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'accounts')); ?></li>
	</ul>
</div>
