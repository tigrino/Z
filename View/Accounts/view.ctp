<?php 
$this->Html->addCrumb('Users', '/z/accounts');
$this->Html->addCrumb('View', '/z/accounts/view');
?>
<div class="users view">
<h2><?php  echo __d('z', 'User'); ?></h2>
		<?php 
			$gravatar = md5($user['User']['email']); 
		?>
	<div id="gravatar">
		<?php echo '<a href="http://www.gravatar.com/" title="Gravatar"><img src="https://secure.gravatar.com/avatar/' . $gravatar . '?s=120&d=mm&r=pg" alt="☺" /></a>'; ?>
	</div>
	<dl>
		<dt><?php echo __d('z', 'Id'); ?></dt>
		<dd>
			<?php echo h($user['User']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Created'); ?></dt>
		<dd>
			<?php echo h($user['User']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Modified'); ?></dt>
		<dd>
			<?php echo h($user['User']['modified']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Last Login'); ?></dt>
		<dd>
			<?php echo h($user['User']['good_login']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'From'); ?></dt>
		<dd>
			<?php echo h($user['User']['good_from_ip']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'Last Failed Login'); ?></dt>
		<dd>
			<?php echo h($user['User']['bad_login']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __d('z', 'From'); ?></dt>
		<dd>
			<?php echo h($user['User']['bad_from_ip']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Change Password'), array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'password', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__d('z', 'Delete'), array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'delete', $this->Form->value('User.id')), null, __d('z', 'Are you sure you want to delete your user account %s?', $user['User']['email'])); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Logout'), array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'logout')); ?> </li>
		<?php if ( $user['User']['user_admin'] == 1 ) { ?>
		<li><?php echo $this->Html->link(__d('z', 'Admin Panel'), array('plugin' => 'z', 'controller' => 'controls', 'action' => 'index')); ?> </li>
		<?php } ?>
	</ul>
</div>
