<div class="controls view">
<h2><?php  echo __d('z', 'User'); ?></h2>
	<dl>
		<dt><?php echo __d('z', 'Id'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Alias'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['alias']); echo ' ('; echo h($Account['Account']['slug']); echo ')'; ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Active'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Created'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Modified'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
		<h3><?php echo __d('z', 'Password'); ?></h3>
	<?php if (!empty($Account['AccountPassword'])): ?>
		<dl>
			<dt><?php echo __d('z', 'Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['id']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Account Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['account_id']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Email'); ?></dt>
		<dd>
			<?php echo h($Account['AccountPassword']['email']); ?>
		</dd>
		<dt><?php echo __d('z', 'Password'); ?></dt>
		<dd>
	<?php echo String::truncate($Account['AccountPassword']['password'], 30); ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Created'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['created']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Modified'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['modified']; ?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<h3><?php echo __d('z', 'Flags'); ?></h3>
	<?php if (!empty($Account['AccountFlag'])): ?>
		<dl>
			<dt><?php echo __d('z', 'Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['id']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Account Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['account_id']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'User Admin'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['user_admin']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Agreement'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['agreement']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Agreement Date'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['agreement_date']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Email Verified'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['email_verified']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Email Verified Date'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['email_verified_date']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Deleted'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['deleted']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Deleted Date'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['deleted_date']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Created'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['created']; ?>
&nbsp;</dd>
		<dt><?php echo __d('z', 'Modified'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['modified']; ?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
</div>
<div class="controls actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Edit'), array('action' => 'edit', $Account['Account']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__d('z', 'Delete'), array('action' => 'delete', $this->Form->value('Account.id')), null, __d('z', 'Are you sure you want to permanently delete account "%s" (#%s)?', $Account['Account']['alias'], $Account['Account']['id'])); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Accounts'), array('action' => 'accounts')); ?> </li>
		<li><?php echo $this->Html->link(__d('z', 'New Account'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__d('z', 'Tokens'), array('action' => 'tokens')); ?> </li>
	</ul>
</div>
	<div class="related">
	<h3><?php echo __d('z', 'Tokens'); ?></h3>
	<?php if (!empty($Account['AccountToken'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __d('z', 'Id'); ?></th>
		<th><?php echo __d('z', 'Account Id'); ?></th>
		<th><?php echo __d('z', 'Token'); ?></th>
		<th><?php echo __d('z', 'Purpose'); ?></th>
		<th><?php echo __d('z', 'Expires'); ?></th>
		<th><?php echo __d('z', 'Created'); ?></th>
		<th><?php echo __d('z', 'Modified'); ?></th>
		<th class="actions"><?php echo __d('z', 'Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($Account['AccountToken'] as $AccountToken): ?>
		<tr>
			<td><?php echo $AccountToken['id']; ?></td>
			<td><?php echo $AccountToken['account_id']; ?></td>
			<td><?php echo $AccountToken['token']; ?></td>
			<td><?php echo $AccountToken['purpose']; ?></td>
			<td><?php echo $AccountToken['expires']; ?></td>
			<td><?php echo $AccountToken['created']; ?></td>
			<td><?php echo $AccountToken['modified']; ?></td>
			<td class="actions">
				<?php echo $this->Form->postLink(__d('z', 'Delete'), array('controller' => 'account_tokens', 'action' => 'delete', $AccountToken['id']), null, __d('z', 'Are you sure you want to delete # %s?', $AccountToken['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<h3><?php echo __d('z', 'Login access log'); ?></h3>
	<?php if (!empty($Account['AccountLogin'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __d('z', 'Date'); ?></th>
		<th><?php echo __d('z', 'From IP'); ?></th>
		<th><?php echo __d('z', 'Success'); ?></th>
	</tr>
	<?php foreach ($Account['AccountLogin'] as $AccountLogin): ?>
		<tr>
			<td><?php echo $AccountLogin['created']; ?></td>
			<td><?php echo $AccountLogin['from_ip']; ?></td>
			<td><?php echo $AccountLogin['success']; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
	<?php endif; ?>

	<!-- div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__d('z', 'New Account Token'), array('action' => 'token')); ?> </li>
		</ul>
	</div -->
</div>
