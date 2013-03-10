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
		<?php
		$gravatar_img = $this->Html->image('https://secure.gravatar.com/avatar/' . $gravatar . '?s=120&d=mm&r=pg', array(
                                                "alt" => "☺",
                                                'url' => "http://www.gravatar.com/",
                                                "title" => __d("z", "get your gravatar at gravatar.com")
                                                ));
		echo $gravatar_img;
		?>
	</div>
	<dl>
		<dt><?php echo __d('z', 'Alias'); ?></dt>
		<dd>
			<?php echo h($user['User']['alias']); ?>
			&nbsp;
		</dd>
		<?php if ( !empty($user['User']['email']) ) { ?>
		<dt><?php echo __d('z', 'label_email'); ?></dt>
		<dd>
			<?php echo h($user['User']['email']); ?>
			&nbsp;
		</dd>
		<?php } ?>
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
		<dt><?php echo __d('z', 'Last login'); ?></dt>
		<dd>
			<?php echo h($user['User']['good_login']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __d('z', 'Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__d('z', 'Change password'), array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'password', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__d('z', 'Delete your account'), array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'delete', $this->Form->value('User.id')), null, __d('z', 'Are you sure you want to delete your user account %s?', $user['User']['email'])); ?></li>
		<li><?php echo $this->Html->link(__d('z', 'Logout'), array('plugin' => 'z', 'controller' => 'accounts', 'action' => 'logout')); ?> </li>
		<?php if ( $user['User']['user_admin'] == 1 ) { ?>
		<li><?php echo $this->Html->link(__d('z', 'Admin panel'), array('plugin' => 'z', 'controller' => 'controls', 'action' => 'index')); ?> </li>
		<?php } ?>
	</ul>
</div>
