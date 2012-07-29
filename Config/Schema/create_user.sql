SET NAMES 'utf8';
SET CHARACTER SET 'utf8'; 
insert into z_accounts (id, email, active, created, modified) values('11111111111111111111', 'YOURNAME@EXAMPLE.COM', 1, NOW(), NOW());
// Password: your_secret_password
insert into z_account_passwords (id, account_id, password, created, modified) SELECT '00000000000000000001', id, '$2a$10$w9djEvGZY/kYtTjBpdTdFu16wxKdvIVP3X.Auk2IeFAv.WuGThrfC', NOW(), NOW() FROM z_accounts WHERE email='YOURNAME@EXAMPLE.COM';
insert into z_account_flags (id, account_id, user_admin, agreement, agreement_date, email_verified, email_verified_date, created, modified) SELECT '00000000000000000001', id, 1, 1, NOW(), 1, NOW(), NOW(), NOW() FROM z_accounts WHERE email='YOURNAME@EXAMPLE.COM';
