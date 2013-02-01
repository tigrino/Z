<div class="Accounts view">
<h2><?php  echo __d('z', 'Destroy all user records? Action is not reversible!'); ?></h2>
	<dl>
<?php echo $this->Form->create('User'); // Special case - for Auth module ?>
	<fieldset>
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
<?php echo $this->Form->end(__d('z', 'Destroy all users!', true));?>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Dashboard'), array('action' => 'dashboard')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'index')); ?></li>
	</ul>
</div>
