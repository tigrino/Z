<div class="accounts index">
	<h2><?php echo __d('z', 'Accounts List'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('email'); ?></th>
			<th><?php echo $this->Paginator->sort('active', 'Active'); ?></th>
			<th><?php echo $this->Paginator->sort('AccountFlag.user_admin', 'Admin'); ?></th>
			<th><?php echo $this->Paginator->sort('AccountFlag.deleted', 'Deleted'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __d('z', 'Actions'); ?></th>
	</tr>
	<?php
	foreach ($accounts as $account): ?>
	<tr>
		<td><?php echo $this->Html->link(h($account['Account']['id']),
			array('action' => 'view', $account['Account']['id'])); ?>&nbsp;</td>
		<td><?php echo $this->Html->link(h($account['Account']['email']),
			array('action' => 'view', $account['Account']['id'])); ?>&nbsp;</td>
		<td><?php echo h($account['Account']['active']); ?>&nbsp;</td>
		<td><?php echo h($account['AccountFlag']['user_admin']); ?>&nbsp;</td>
		<td><?php echo h($account['AccountFlag']['deleted']); ?>&nbsp;</td>
		<td><?php echo h(date('Y-m-d', strtotime($account['Account']['created']))); ?>&nbsp;</td>
		<td><?php echo h(date('Y-m-d', strtotime($account['Account']['modified']))); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__d('z', 'View'), array('action' => 'view', $account['Account']['id'])); ?>
			<?php echo $this->Html->link(__d('z', 'Edit'), array('action' => 'edit', $account['Account']['id'])); ?>
			<?php echo $this->Form->postLink(__d('z', 'Delete'), array('action' => 'delete', $account['Account']['id']), null, __d('z', 'Are you sure you want to delete # %s?', $account['Account']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __d('z', 'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __d('z', 'previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__d('z', 'next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'New Account'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Tokens'), array('action' => 'tokens')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Dashboard'), array('action' => 'dashboard')); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Logout'), array('controller' => 'accounts', 'action' => 'logout')); ?> </li>
	</ul>
</div>
