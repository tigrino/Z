<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Verify', '/z/accounts/verify');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __('Verify e-mail'); ?></legend>
	<?php
		echo $this->Form->input('Account.email');
		echo $this->Form->input('AccountToken.token');
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
		<li><?php echo $this->Html->link(__('Register'), array('action' => 'register'));?></li>
	</ul>
</div>
