<div class="Accounts form">
<?php echo $this->Form->create('Account'); ?>
	<fieldset>
		<legend><?php echo __d('z', 'Initialization: create Administrator user'); ?></legend>
	<?php
		echo $this->Form->input('email',
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
		echo $this->Form->input('AccountPassword.confirm_password',
			array(
				'label' => __d('z', 'label_password_confirm'),
				'type' => 'password',
				'div' => 'required'
			));
		echo $this->Form->input('AccountFlag.agreement', 
			array(
				'hiddenField' => false,
				'div' => 'required',
				'label' => __d('z', 'I confirm that I agree to ').$this->Html->link(__d('z', 'the User Agreement and consent to the use of cookies'), array('action' => 'tos'), array('target' => '_blank')),
				'error'=>__d('z', 'The acceptance is mandatory.',true)
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
	</ul>
</div>
