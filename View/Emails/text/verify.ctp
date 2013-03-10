Hello and welcome to <?php echo $sitename; ?>!

This email has been sent from <?php echo $fromurl; ?> 

You have received this email because this email address
was used during registration at our website.
If you did not register at our website, please disregard this
email. You do not need to unsubscribe or take any further action.
The subscription will automatically expire and be expunged.

------------------------------------------------
Activation Instructions
------------------------------------------------

Thank you for registering.
We require that you "validate" your registration to ensure that
the email address you entered was correct. This protects against
unwanted spam and malicious abuse.

To activate your account, simply click on the following link:

<?php echo $urltoken; ?> 

(Some email client users may need to copy and paste the link into your web
browser).

------------------------------------------------
Not working?
------------------------------------------------

If you could not validate your registration by clicking on the link, please
visit this page:

<?php echo $url; ?> 

It will ask you for your e-mail address and your validation token. These are shown
below:

E-mail: <?php echo $email; ?> 

Validation token: <?php echo $token; ?> 

Please copy and paste, or type those numbers into the corresponding fields in the form.

If you still cannot validate your account, it's possible that the account has been removed.
If this is the case, please contact an administrator to rectify the problem.

Thank you for registering and enjoy your stay!

Best regards,
Your <?php echo $sitename; ?> team.

