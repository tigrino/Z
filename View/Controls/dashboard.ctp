<?php
App::import('Vendor', 'Z.PasswordHash');
?>
<div class="accounts index">
<?php //debug($this); ?>
<?php //debug($z_wordlists); ?>
	<h2><?php echo __d('z', 'Settings and Dashboard'); ?></h2>
	<li>version=<?php echo $z_version; ?>
	<li>mt_getrandmax=<?php echo mt_getrandmax(); ?>
	<li>token_length=<?php echo $z_token_length; ?>
	<li>password hash cost=<?php echo $z_hash_cost; ?>
	<li>word lists (used: <?php echo $z_use_password_blacklist?"yes":"no" ?>) : <?php echo implode(', ', $z_wordlists); ?>
	<li>accounts total=<?php echo $accounts; ?>
	<li>accounts active=<?php echo $accounts_active; ?>
	<li>tokens active=<?php echo $tokens; ?>
</pre>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'accounts')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Tokens'), array('action' => 'tokens')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Cryptography tests'), array('action' => 'cryptotest')); ?></li>
	</ul>
</div>
