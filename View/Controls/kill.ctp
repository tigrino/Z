<div class="controls view">
<h2><?php  echo __d('z', 'Destroy all user records? Action is not reversible!'); ?></h2>
	<dl>
<?php echo $this->Form->create('User'); // Special case - for Auth module ?>
	<fieldset>
	<p>
	<?php echo __d('z', 'Attention! You are about to delete all users from the system! '); ?>
	<?php echo __d('z', 'This action is not reversible! '); ?>
	<?php echo __d('z', 'This action will permanently delete every user account here, including administrators. '); ?>
	<?php echo __d('z', 'If you are totally sure that that is what you want, press the button and say good-bye to your users.'); ?>
	</p>
	<?php
		echo $this->Form->hidden('Account.ruhuman',
			array(
				'type' => 'text',
				'autocomplete'=>'off',
				'label'=> __d('z', 'Please enter something into the following field if you are not human:'),
				)
			);
	?>
	</fieldset>
<?php echo $this->Form->end(__d('z', 'Destroy all user accounts!', true));?>
</div>
<div class="controls actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Dashboard'), array('action' => 'dashboard')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'accounts')); ?></li>
	</ul>
</div>
