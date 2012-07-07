<?php 
$this->Html->addCrumb('Users', '/z/users');
$this->Html->addCrumb('View', '/z/users/view');
?>
<div class="users view">
<h2><?php  echo __('User'); ?></h2>
		<?php 
			$gravatar = md5($user['User']['email']); 
		?>
	<div id="gravatar">
		<?php echo '<a href="http://www.gravatar.com/" title="Gravatar"><img src="https://secure.gravatar.com/avatar/' . $gravatar . '?s=120&d=mm&r=pg" alt="gravatar" /></a>'; ?>
	</div>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($user['User']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($user['User']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($user['User']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Last Login'); ?></dt>
		<dd>
			<?php echo h($user['User']['good_login']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('From'); ?></dt>
		<dd>
			<?php echo h($user['User']['good_from_ip']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Last Failed Login'); ?></dt>
		<dd>
			<?php echo h($user['User']['bad_login']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('From'); ?></dt>
		<dd>
			<?php echo h($user['User']['bad_from_ip']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Change Password'), array('controller' => 'accounts', 'action' => 'password', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete'), array('controller' => 'accounts', 'action' => 'delete', $this->Form->value('User.id')), null, __('Are you sure you want to delete your user account %s?', $user['User']['email'])); ?></li>
		<li><?php echo $this->Html->link(__('Logout'), array('controller' => 'accounts', 'action' => 'logout')); ?> </li>
	</ul>
</div>
