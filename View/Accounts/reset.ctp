<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Reset', '/z/accounts/reset');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __d('z', 'Reset password'); ?></legend>
		<p>
		<?php echo __d('z', 'You can only reset a password when you registered with your e-mail here. If you registered with some other means, this form will not work for you.'); ?>
		</p>
	<?php
		echo $this->Form->input('AccountPassword.email',
			array(
				'label' => __d('z', 'label_email'),
				'type' => 'email',
				'div' => 'required'
			)
		);
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
				'error'=>__d('z', 'captcha_incorrect',true)
				)
			);
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Request a reset'));?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Login'), array('action' => 'login'));?></li>
		<li><?php echo $this->Html->link(__d('z', 'Confirm password'), array('action' => 'confirm'));?></li>
	</ul>
</div>
