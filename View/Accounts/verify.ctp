<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Verify', '/z/accounts/verify');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __d('z', 'Verify e-mail'); ?></legend>
	<?php
		echo $this->Form->input('Account.email',
			array(
				'label' => __d('z', 'label_email'),
				'type' => 'email',
			)
		);
		echo $this->Form->input('AccountToken.token',
			array(
				'label' => __d('z', 'label_token'),
			)
		);
		echo $this->Form->hidden('ruhuman',
			array(
				'type' => 'text',
				'autocomplete'=>'off',
				'label'=> __d('z', 'Please enter something into the following field if you are not human:'),
				)
			);
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Login'), array('action' => 'login'));?></li>
		<li><?php echo $this->Html->link(__d('z', 'Register'), array('action' => 'register'));?></li>
	</ul>
</div>
