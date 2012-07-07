<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Confirm', '/z/accounts/confirm');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __('Confirm Password Reset'); ?></legend>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('AccountToken.token');
		echo $this->Form->input('AccountPassword.password');
		echo $this->Form->input('AccountPassword.password_confirm',
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
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Login'), array('action' => 'login'));?></li>
		<li><?php echo $this->Html->link(__('Reset account'), array('action' => 'reset'));?></li>
	</ul>
</div>
<?php echo $this->Html->script('/z/js/jquery.min'); ?>
<?php echo $this->Html->script('/z/js/passwordstrength'); ?>
