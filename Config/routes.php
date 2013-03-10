<?php
	Router::connect('/users', array('plugin' => 'Z', 'controller' => 'users', 'action' => 'view'));
	Router::connect('/users/', array('plugin' => 'Z', 'controller' => 'users', 'action' => 'view'));
	Router::connect('/users/:action/*', array('plugin' => 'z', 'controller' => 'accounts'));
?>
