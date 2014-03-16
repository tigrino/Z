<div class="controls form">
<?php echo $this->Form->create('Account'); ?>
	<fieldset>
		<legend><?php echo __d('z', 'Add Account'); ?></legend>
	<?php
		echo $this->Form->input('alias',
			array(
				'label' => __d('z', 'label_user_alias'),
			)
		);
		echo $this->Form->input('AccountPassword.email',
			array(
				'label' => __d('z', 'label_email'),
				'type' => 'email',
			)
		);
		echo $this->Form->input('AccountPassword.password',
			array(
				'label' => __d('z', 'label_password'),
			)
		);
		echo $this->Form->input('active',
			array(
				'label' => __d('z', 'label_activate'),
			)
		);
		echo $this->Form->input('AccountFlag.user_admin',
			array(
				'label' => __d('z', 'label_user_admin'),
			)
		);
		echo $this->Form->input('AccountFlag.agreement', 
			array(
				'hiddenField' => false,
				'div' => 'required',
				'label' => __d('z', 'I confirm that the user agrees to ').$this->Html->link(__d('z', 'the Terms of Service and User Agreement'), array('plugin' => null, 'controller' => 'pages', 'action' => 'tos')),
				'error'=>__d('z', 'The acceptance is mandatory.',true)
				)
			);
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Submit')); ?>
</div>
<div class="controls actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Tokens'), array('action' => 'tokens')); ?> </li>
	</ul>
</div>
