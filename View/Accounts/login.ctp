<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Login', '/z/accounts/login');
?>
<?php
/*	debug( RequestHandlerComponent::getClientIP() );
	debug( microtime() );
	debug( microtime() * 1000000 );*/
?>
<div class="users form">
<?php echo $this->Form->create('User'); // Special case - for Auth module ?>
	<fieldset>
		<legend><?php echo __d('z', 'Login'); ?></legend>
	<p><?php 
		echo __d('z', 'Forgot you password?'); 
		echo " ";
		echo $this->Html->link(__d('z', 'Request a password reset'), array('action' => 'reset'));
	?>!</p>
	<p><?php
		echo __d('z', 'Not registered yet?'); 
		echo " ";
		echo $this->Html->link(__d('z', 'Register now'), array('action' => 'register'));
	?>!</p>
	<?php
		echo $this->Form->input('alias',
			array(
				'label' => __d('z', 'label_user_alias_or_email'),
				'div' => 'required'
			)
		);
		echo $this->Form->input('password',
			array(
				'label' => __d('z', 'label_password'),
				'div' => 'required'
			)
		);
		echo $this->Form->hidden('Account.ruhuman',
			array(
				'type' => 'text',
				'autocomplete'=>'off',
				'label'=> __d('z', 'Please enter something into the following field if you are not human:'),
				)
			);
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Log in', true));?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Register'), array('action' => 'register'));?></li>
		<li><?php echo $this->Html->link(__d('z', 'Reset account'), array('action' => 'reset'));?></li>
		<!-- li><?php echo $this->Html->link(__d('z', 'Verify e-mail'), array('action' => 'verify'));?></li -->
		<!-- li><?php echo $this->Html->link(__d('z', 'Confirm password'), array('action' => 'confirm'));?></li -->
	</ul>
</div>
