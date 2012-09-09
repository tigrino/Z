SET NAMES 'utf8';
SET CHARACTER SET 'utf8'; 
CREATE TABLE z_accounts ( 
	id BIGINT(20) UNSIGNED ZEROFILL PRIMARY KEY, 
	email VARCHAR(255) NOT NULL UNIQUE, 
	active TINYINT(1) DEFAULT 0, 
	created DATETIME, 
	modified DATETIME,
	UNIQUE KEY `z_accounts_email` (`email`)
) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ENGINE=InnoDB;
CREATE TABLE z_account_passwords ( 
	id BIGINT(20) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT, 
	account_id BIGINT(20) UNSIGNED ZEROFILL NOT NULL, 
	salt VARCHAR(255) NOT NULL, 
	password VARCHAR(255) NOT NULL, 
	created DATETIME, 
	modified DATETIME,
	KEY `z_account_passwords_account_id` (`account_id`),
	CONSTRAINT `z_account_passwords_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `z_accounts` (`id`)
) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ENGINE=InnoDB;
CREATE TABLE z_account_flags ( 
	id BIGINT(20) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT, 
	account_id BIGINT(20) UNSIGNED ZEROFILL NOT NULL, 
	user_admin TINYINT(1) DEFAULT 0, 
	agreement TINYINT(1) DEFAULT 0, 
	agreement_date DATETIME, 
	email_verified TINYINT(1) DEFAULT 0, 
	email_verified_date DATETIME, 
	deleted TINYINT(1) DEFAULT 0, 
	deleted_date DATETIME, 
	created DATETIME, 
	modified DATETIME,
	KEY `z_account_flags_account_id` (`account_id`),
	CONSTRAINT `z_account_flags_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `z_accounts` (`id`)
) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ENGINE=InnoDB;
CREATE TABLE z_account_tokens ( 
	id BIGINT(20) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT, 
	account_id BIGINT(20) UNSIGNED ZEROFILL NOT NULL, 
	token VARBINARY(255) NOT NULL, 
	purpose SMALLINT(4) NOT NULL, 
	expires DATETIME, 
	created DATETIME, 
	modified DATETIME,
	KEY `z_account_tokens_account_id` (`account_id`),
	CONSTRAINT `z_account_tokens_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `z_accounts` (`id`)
) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ENGINE=InnoDB;
CREATE TABLE z_account_logins ( 
	id BIGINT(20) UNSIGNED ZEROFILL PRIMARY KEY AUTO_INCREMENT, 
	account_id BIGINT(20) UNSIGNED ZEROFILL NOT NULL, 
	good_login DATETIME, 
	good_from_ip VARCHAR(255), 
	bad_login DATETIME, 
	bad_from_ip VARCHAR(255),
	KEY `z_account_logins_account_id` (`account_id`),
	CONSTRAINT `z_account_logins_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `z_accounts` (`id`)
) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ENGINE=InnoDB;
CREATE VIEW users AS SELECT `ActiveUser`.`id`, `ActiveUser`.`email`, `AccountFlags`.`user_admin`, `ActiveUser`.`created`, `Password`.`salt`, `Password`.`password`, `RecentVisit`.`good_login`, `RecentVisit`.`good_from_ip`, `RecentVisit`.`bad_login`, `RecentVisit`.`bad_from_ip`, (GREATEST(IFNULL(`ActiveUser`.`modified`, 0), IFNULL(`Password`.`modified`, 0))) AS `modified` FROM `z_accounts` AS `ActiveUser` LEFT JOIN `z_account_passwords` AS `Password` ON (`Password`.`account_id` = `ActiveUser`.`id`) LEFT JOIN `z_account_flags` AS `AccountFlags` ON (`AccountFlags`.`account_id` = `ActiveUser`.`id`) LEFT JOIN `z_account_logins` AS `RecentVisit` ON (`RecentVisit`.`account_id` = `ActiveUser`.`id`) WHERE (`ActiveUser`.`active` = '1');
