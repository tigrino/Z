<?php 
$this->Html->addCrumb('Accounts', '/z/accounts');
$this->Html->addCrumb('Reset', '/z/accounts/reset');
?>
<div class="users form">
<?php echo $this->Form->create('Account');?>
	<fieldset>
		<legend><?php echo __('Reset password'); ?></legend>
	<?php
		echo $this->Form->input('email');
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
				'label'=> 'Enter the given code: ' . $this->Html->image($this->Html->url(array('controller'=>'accounts', 'action'=>'captcha'), true),array('style'=>'','vspace'=>2)) . '<br>' . 'into the following field:',
				'class'=>'',
				'error'=>__('The validation of the CAPTCHA code did not succeed.',true)
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
		<li><?php echo $this->Html->link(__('Confirm password'), array('action' => 'confirm'));?></li>
	</ul>
</div>
