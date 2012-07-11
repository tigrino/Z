<div class="Accounts view">
<h2><?php  echo __('Account'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Active'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['active']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($Account['Account']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
		<h3><?php echo __('Password'); ?></h3>
	<?php if (!empty($Account['AccountPassword'])): ?>
		<dl>
			<dt><?php echo __('Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['id']; ?>
&nbsp;</dd>
		<dt><?php echo __('Account Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['account_id']; ?>
&nbsp;</dd>
		<dt><?php echo __('Salt'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['salt']; ?>
&nbsp;</dd>
		<dt><?php echo __('Password'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['password']; ?>
&nbsp;</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['created']; ?>
&nbsp;</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
	<?php echo $Account['AccountPassword']['modified']; ?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<h3><?php echo __('Flags'); ?></h3>
	<?php if (!empty($Account['AccountFlag'])): ?>
		<dl>
			<dt><?php echo __('Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['id']; ?>
&nbsp;</dd>
		<dt><?php echo __('Account Id'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['account_id']; ?>
&nbsp;</dd>
		<dt><?php echo __('User Admin'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['user_admin']; ?>
&nbsp;</dd>
		<dt><?php echo __('Agreement'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['agreement']; ?>
&nbsp;</dd>
		<dt><?php echo __('Agreement Date'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['agreement_date']; ?>
&nbsp;</dd>
		<dt><?php echo __('Email Verified'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['email_verified']; ?>
&nbsp;</dd>
		<dt><?php echo __('Email Verified Date'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['email_verified_date']; ?>
&nbsp;</dd>
		<dt><?php echo __('Deleted'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['deleted']; ?>
&nbsp;</dd>
		<dt><?php echo __('Deleted Date'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['deleted_date']; ?>
&nbsp;</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['created']; ?>
&nbsp;</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
	<?php echo $Account['AccountFlag']['modified']; ?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Account'), array('action' => 'edit', $Account['Account']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Account'), array('action' => 'delete', $Account['Account']['id']), null, __('Are you sure you want to delete # %s?', $Account['Account']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Accounts'), array('action' => 'accounts')); ?> </li>
		<li><?php echo $this->Html->link(__('New Account'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Tokens'), array('action' => 'tokens')); ?> </li>
	</ul>
</div>
	<div class="related">
	<h3><?php echo __('Account Tokens'); ?></h3>
	<?php if (!empty($Account['AccountToken'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Account Id'); ?></th>
		<th><?php echo __('Token'); ?></th>
		<th><?php echo __('Purpose'); ?></th>
		<th><?php echo __('Expires'); ?></th>
		<th><?php echo __('Created'); ?></th>
		<th><?php echo __('Modified'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
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
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'account_tokens', 'action' => 'delete', $AccountToken['id']), null, __('Are you sure you want to delete # %s?', $AccountToken['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Account Token'), array('action' => 'token')); ?> </li>
		</ul>
	</div>
</div>
