<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Register', '/z/accounts/register');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __d('z', 'Register'); ?></legend>
	<?php
		echo $this->Form->input('email', array('label' => __d('z', 'label_email'), 'type' => 'email'));
		echo $this->Form->input('AccountPassword.password',
			array(
                                'type' => 'password',
				'div' => 'required',
				'label' => __d('z', 'label_password')
			));
		echo $this->Form->input('AccountPassword.confirm_password',
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
		$captcha_label = __d('z', 'Enter the given code: ') . 
			$this->Html->image(
				$this->Html->url(
					array('controller'=>'accounts', 'action'=>'captcha'), 
					true),
				array('style'=>'','vspace'=>2)) . 
			' <br />' . __d('z', 'into the following field:');
		echo $this->Form->input('captcha',
			array(
				'type' => 'text',
				'type' => 'required',
				'autocomplete'=>'off',
				'label'=> $captcha_label,
				'class'=>'',
				'error'=>__d('z', 'The validation of the CAPTCHA code did not succeed.',true)
				)
			);
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
<?php echo $this->Form->end(__d('z', 'Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Login'), array('action' => 'login'));?></li>
		<li><?php echo $this->Html->link(__d('z', 'Verify e-mail'), array('action' => 'verify'));?></li>
		<li><?php echo $this->Html->link(__d('z', 'Reset password'), array('action' => 'reset'));?></li>
		<li><?php echo $this->Html->link(__d('z', 'User Agreement'), array('action' => 'tos'));?></li>
	</ul>
</div>
<?php echo $this->Html->script('/z/js/jquery.min'); ?>
<?php echo $this->Html->script('/z/js/passwordstrength'); ?>
