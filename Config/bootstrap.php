<?php
	// Operational settings - overwrite in application bootstrap
	// where we redirect on successful login
	define('PLUGIN_Z_ON_LOGIN', '/z/users/');

	// Plugin constant definitions
	define('PLUGIN_Z_TOKEN_MAIL_VERIFY',	1);
	define('PLUGIN_Z_TOKEN_RESET_CONFIRM',	2);

	//
	// Cryptography control
	define('PLUGIN_Z_TOKEN_LENGTH',		32); // in hex characters, 32 = 128 bit
	define('PLUGIN_Z_PASSWORD_HASH_COST',	10); // logarithmic scale, 4-31

	//
	// Language configuration
	if ( !defined('DEFAULT_LANGUAGE') ) {
		define('DEFAULT_LANGUAGE', 'en');
	}
	$language_set = Configure::read('Config.language');
	if ( empty($language_set) ) {
		Configure::write('Config.language', 'eng');
		Configure::write('Config.supported_languages', array(
		    'en-GB' => 'eng',
		    'en-US' => 'eng',
		    'en' => 'eng',
		    'ru-RU' => 'rus',
		    'ru' => 'rus',
		));

		if ( isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
			$supported_languages = Configure::read('Config.supported_languages');
			$accepted_languages = split(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			foreach ($accepted_languages as $language) {
			    $language_data = split(';', $language);
			    if (in_array($language_data[0], array_keys($supported_languages))) {
				Configure::write('Config.language', $supported_languages[$language_data[0]]);
				break;
			    }
			}
		}
	}
?>
