<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Login', '/z/accounts/login');
?>
<div class="users form">
<?php echo $this->Form->create('User'); // Special case - for Auth module ?>
	<fieldset>
		<legend><?php echo __('Login'); ?></legend>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('password');
		echo $this->Form->hidden('Account.ruhuman',
			array(
				'type' => 'text',
				'autocomplete'=>'off',
				'label'=> 'Please enter something into the following field if you are not human:',
				)
			);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Register'), array('action' => 'register'));?></li>
		<li><?php echo $this->Html->link(__('Reset account'), array('action' => 'reset'));?></li>
	</ul>
</div>
