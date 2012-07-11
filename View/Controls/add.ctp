<div class="Accounts form">
<?php echo $this->Form->create('Account'); ?>
	<fieldset>
		<legend><?php echo __('Add Account'); ?></legend>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('AccountPassword.password');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Accounts'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('Tokens'), array('action' => 'tokens')); ?> </li>
	</ul>
</div>
