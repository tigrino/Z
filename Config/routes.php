<?php
	Router::connect('/users/*', array('plugin' => 'Z', 'controller' => 'users', 'action' => 'view'));
?>
