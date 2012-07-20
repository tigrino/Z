<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Confirm', '/z/accounts/confirm');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __d('z', 'Confirm Password Reset'); ?></legend>
	<?php
		echo $this->Form->input('email',
			array(
				'label' => __d('z', 'label_email'),
			)
		);
		echo $this->Form->input('AccountToken.token',
			array(
				'label' => __d('z', 'label_token'),
			)
		);
		echo $this->Form->input('AccountPassword.password',
			array(
				'label' => __d('z', 'label_password'),
			)
		);
		echo $this->Form->input('AccountPassword.password_confirm',
                        array(
				'label' => __d('z', 'label_password_confirm'),
                                'type' => 'password',
				'div' => 'required'
                        ));
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
		<li><?php echo $this->Html->link(__d('z', 'Reset account'), array('action' => 'reset'));?></li>
	</ul>
</div>
<?php echo $this->Html->script('/z/js/jquery.min'); ?>
<?php echo $this->Html->script('/z/js/passwordstrength'); ?>
