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
	email VARCHAR(255) DEFAULT NULL,
	from_ip VARCHAR(255),
	success TINYINT(1),
	created DATETIME,
	KEY `z_logins_email` (`email`),
	KEY `z_logins_from_ip` (`from_ip`)
) CHARACTER SET 'utf8' COLLATE 'utf8_general_ci' ENGINE=InnoDB;

# MySQL view pollution starts here :)
CREATE VIEW z_last_good_logins AS select * from z_account_logins where success=1 group by email desc;
CREATE VIEW z_last_bad_logins AS select * from z_account_logins where success=0 group by email desc;
CREATE VIEW z_last_logins AS select lg.email,lg.from_ip as good_from_ip,lg.created as good_login,lb.from_ip as bad_from_ip,lb.created as bad_login from z_last_good_logins as lg left join z_last_bad_logins AS lb ON lg.email=lb.email;
CREATE VIEW users AS 
SELECT 
        `ActiveUser`.`id`, 
        `ActiveUser`.`email`, 
        `AccountFlags`.`user_admin`, 
        `ActiveUser`.`created`, 
        `Password`.`salt`, 
        `Password`.`password`,
        `RecentVisit`.`good_login`, 
        `RecentVisit`.`good_from_ip`, 
        `RecentVisit`.`bad_login`, 
        `RecentVisit`.`bad_from_ip`, 
        (GREATEST(IFNULL(`ActiveUser`.`modified`, 0), IFNULL(`Password`.`modified`, 0))) AS `modified` 
FROM 
        `z_accounts` AS `ActiveUser` 
        LEFT JOIN `z_account_passwords` AS `Password` 
                ON (`Password`.`account_id` = `ActiveUser`.`id`) 
        LEFT JOIN `z_account_flags` AS `AccountFlags` 
                ON (`AccountFlags`.`account_id` = `ActiveUser`.`id`) 
        LEFT JOIN z_last_logins AS `RecentVisit` 
                ON (`RecentVisit`.`email` = `ActiveUser`.`email` ) 
WHERE (`ActiveUser`.`active` = '1');

