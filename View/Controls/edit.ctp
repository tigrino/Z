<div class="Accounts form">
<?php //debug($this); ?>
<?php echo $this->Form->create('Account'); ?>
	<fieldset>
		<legend><?php echo __('Edit Account'); ?></legend>
	<?php
		echo $this->Form->hidden('Account.id');
		echo $this->Form->hidden('AccountPassword.id');
		echo $this->Form->hidden('AccountPassword.account_id');
		echo $this->Form->input('Account.email');
		echo $this->Form->input('AccountPassword.password');
		echo $this->Form->input('Account.active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Account.id')), null, __('Are you sure you want to permanently delete accout # %s?', $this->Form->value('Account.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Accounts'), array('action' => 'accounts')); ?></li>
	</ul>
</div>
