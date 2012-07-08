<div class="tokens index">
	<h2><?php echo __('Tokens'); ?></h2>
	<?php //debug($tokens); ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('account_id'); ?></th>
			<th><?php echo $this->Paginator->sort('purpose'); ?></th>
			<th><?php echo $this->Paginator->sort('expires'); ?></th>
			<th><?php echo $this->Paginator->sort('created'); ?></th>
			<th><?php echo $this->Paginator->sort('modified'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
	foreach ($tokens as $token): ?>
	<tr>
		<td><?php echo h($token['AccountToken']['id']); ?>&nbsp;</td>
		<td><?php echo h($token['AccountToken']['account_id']); ?>&nbsp;</td>
		<td><?php echo h($token['AccountToken']['purpose']); ?>&nbsp;</td>
		<td><?php echo h($token['AccountToken']['expires']); ?>&nbsp;</td>
		<td><?php echo h($token['AccountToken']['created']); ?>&nbsp;</td>
		<td><?php echo h($token['AccountToken']['modified']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $token['AccountToken']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $token['AccountToken']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $token['AccountToken']['id']), null, __('Are you sure you want to delete # %s?', $token['AccountToken']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Accounts'), array('action' => 'accounts')); ?></li>
		<li><?php echo $this->Html->link(__('New Account'), array('action' => 'add')); ?></li>
	</ul>
</div>
