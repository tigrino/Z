<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Register', '/z/accounts/register');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __('Register'); ?></legend>
	<?php
		echo $this->Form->input('email');
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
		echo $this->Form->hidden('ruhuman',
			array(
				'type' => 'text',
				'autocomplete'=>'off',
				'label'=> 'Please enter something into the following field if you are not human:',
				)
			);
		echo $this->Form->input('captcha',
			array(
				'type' => 'text',
				'type' => 'required',
				'autocomplete'=>'off',
				'label'=> 'Enter the given code: ' . $this->Html->image($this->Html->url(array('controller'=>'accounts', 'action'=>'captcha'), true),array('style'=>'','vspace'=>2)) . ' <br />into the following field:',
				'class'=>'',
				'error'=>__('The validation of the CAPTCHA code did not succeed.',true)
				)
			);
		echo $this->Form->input('AccountFlag.agreement', 
			array(
				'hiddenField' => false,
				'div' => 'required',
				'label' => 'I confirm that I agree to '.$this->Html->link('the Terms of Service and User Agreement', array('plugin' => null, 'controller' => 'pages', 'action' => 'tos')),
				'error'=>__('The acceptance is mandatory.',true)
				)
			);
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Login'), array('action' => 'login'));?></li>
		<li><?php echo $this->Html->link(__('Verify e-mail'), array('action' => 'verify'));?></li>
		<li><?php echo $this->Html->link(__('Reset password'), array('action' => 'reset'));?></li>
	</ul>
</div>
<?php echo $this->Html->script('/z/js/jquery.min'); ?>
<?php echo $this->Html->script('/z/js/passwordstrength'); ?>
