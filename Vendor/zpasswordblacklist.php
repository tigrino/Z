<?php

//
// Check that the given password is not listed in the file
function z_password_listed($pass) {
	$file = file_get_contents(dirname(__FILE__) . "/zpasswordblacklist.txt");
	return !!strpos($file, $pass);
}

?>
