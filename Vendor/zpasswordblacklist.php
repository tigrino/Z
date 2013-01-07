<?php

//
// Check that the given password is not listed in the file
function z_password_listed($pass) {
	$wordlists = z_wordlist_files();
	foreach ($wordlists as $wl) {
		if ( ! is_dir($wl) ) {
			// Perform a case-insensitive match
			if ( preg_grep("/^{$pass}$/i", file($wl)) ) {
				return true;
			}
		}
	}
	return false;
}

//
// Return the list of wordlist files
function z_wordlist_names() {
	$wordlists = z_wordlist_files();
	$names = array_map('basename', $wordlists);
	return $names;
}

//
// Return the list of wordlist files with full pathnames
function z_wordlist_files() {
	$wldir = dirname(__FILE__) . "/wordlists";
	$wordlists = glob($wldir . "/*");
	return $wordlists;
}


?>
