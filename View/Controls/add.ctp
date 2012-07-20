<div class="Accounts form">
<?php echo $this->Form->create('Account'); ?>
	<fieldset>
		<legend><?php echo __d('z', 'Add Account'); ?></legend>
	<?php
		echo $this->Form->input('email',
			array(
				'label' => __d('z', 'label_email'),
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
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Tokens'), array('action' => 'tokens')); ?> </li>
	</ul>
</div>
