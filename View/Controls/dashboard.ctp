<?php
App::import('Vendor', 'Z.PasswordHash');
?>
<div class="accounts index">
	<h2><?php echo __d('z', 'Settings and Dashboard'); ?></h2>
	<li>version=<?php echo $z_version; ?>
	<li>mt_getrandmax=<?php echo mt_getrandmax(); ?>
</pre>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Cryptography tests'), array('action' => 'cryptotest')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'accounts')); ?></li>
	</ul>
</div>
