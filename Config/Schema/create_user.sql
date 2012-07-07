SET NAMES 'utf8';
SET CHARACTER SET 'utf8'; 
insert into z_accounts (id, email, active, created, modified) values('11111111111111111111', 'YOURNAME@EXAMPLE.COM', 1, NOW(), NOW());
insert into z_account_passwords (id, account_id, salt, password, created, modified) SELECT '00000000000000000001', id, 'XshUDVw3qwsVgbNWOWYIBRT45hkfgVpaZPpwdCVxiTsJWJDHQWT6rWPdfJofbSOE', '2cfaa1bf0752b6ddddb46416ee376444e3850c8f27f37a34fbe171087e211ca6600f066d3199cebb63f3f786c8c1bf3542732b03a1c4b82d22834e45028c47a4', NOW(), NOW() FROM z_accounts WHERE email='YOURNAME@EXAMPLE.COM';
insert into z_account_flags (id, account_id, user_admin, agreement, agreement_date, email_verified, email_verified_date, created, modified) SELECT '00000000000000000001', id, 1, 1, NOW(), 1, NOW(), NOW(), NOW() FROM z_accounts WHERE email='YOURNAME@EXAMPLE.COM';
