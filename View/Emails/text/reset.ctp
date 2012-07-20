Hello and thank you for using <?php echo $sitename; ?>!

This email has been sent from <?php echo $fromurl; ?> 

You have received this email because this email address
was used during registration for our website and recently
someone (possibly you) requested to reset the password
to your account.
If you did not request a password reset, please disregard this
email. You do not need to unsubscribe or take any further action.
The request will automatically expire and be expunged.

------------------------------------------------
Reset Instructions
------------------------------------------------

To reset your account's password, simply click on the following link:

<?php echo $urltoken; ?> 

(Some email client users may need to copy and paste the link into your web
browser).

------------------------------------------------
Not working?
------------------------------------------------

If you could not change your password by clicking on the link, please
visit this page:

<?php echo $url; ?> 

It will ask you for your e-mail address and your validation token. These are shown
below:

E-mail: <?php echo $email; ?> 

Validation token: <?php echo $token; ?> 

Please copy and paste, or type those numbers into the corresponding fields in the form.

If you still cannot access your account, it's possible that the account has been removed.
If this is the case, please contact an administrator to rectify the problem.

Thank you for registering and enjoy your stay!

Best regards,
Your <?php echo $sitename; ?> team.

