<div class="Accounts form">
<?php //debug($this); ?>
<?php echo $this->Form->create('Account'); ?>
	<fieldset>
		<legend><?php echo __d('z', 'Edit Account'); ?></legend>
	<?php
		echo $this->Form->hidden('Account.id');
		echo $this->Form->hidden('AccountPassword.id');
		echo $this->Form->hidden('AccountPassword.account_id');
		echo $this->Form->hidden('AccountFlag.id');
		echo $this->Form->hidden('AccountFlag.account_id');
		echo $this->Form->input('Account.email',
			array(
				'label' => __d('z', 'label_email'),
			)
		);
		echo $this->Form->input('AccountPassword.password',
			array(
				'label' => __d('z', 'label_password_new_or_empty'),
			)
		);
		echo $this->Form->input('Account.active',
			array(
				'label' => __d('z', 'label_activate'),
			)
		);
		echo $this->Form->input('AccountFlag.user_admin',
			array(
				'label' => __d('z', 'label_user_admin'),
			)
		);
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__d('z', 'Delete'), array('action' => 'delete', $this->Form->value('Account.id')), null, __d('z', 'Are you sure you want to permanently delete accout # %s?', $this->Form->value('Account.id'))); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'accounts')); ?></li>
	</ul>
</div>
