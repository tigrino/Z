<?php
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Password', '/z/accounts/password');
?>
<div class="users form">
<?php //debug($this); ?>
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __d('z', 'Change Password'); ?></legend>
	<?php
		echo $this->Form->input('Account.id');
		echo $this->Form->hidden('Account.email');
		echo $this->Form->input('AccountPassword.id');
		echo $this->Form->input('AccountPassword.old_password',
                        array(
                                'type' => 'password',
				'div' => 'required'
                        ));
		echo $this->Form->input('AccountPassword.password',
			array(
                                'type' => 'password',
				'div' => 'required'
			));
		echo $this->Form->input('AccountPassword.confirm_password',
                        array(
                                'type' => 'password',
				'div' => 'required'
                        ));
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__d('z', 'View Account'), array('controller' => 'users', 'action' => 'view'));?></li>
		<li><?php echo $this->Form->postLink(__d('z', 'Delete'), array('controller' => 'accounts', 'action' => 'delete', $this->Form->value('Account.id')), null, __d('z', 'Are you sure you want to delete your user account %s?', $this->Form->value('Account.email'))); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Logout'), array('controller' => 'accounts', 'action' => 'logout')); ?> </li>
		<li><?php echo $this->Html->link(__d('z', 'User Agreement'), array('action' => 'tos'));?></li>
	</ul>
</div>
<?php echo $this->Html->script('/z/js/jquery.min'); ?>
<?php echo $this->Html->script('/z/js/passwordstrength'); ?>
