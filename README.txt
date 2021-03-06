
	Z authentication and account management plugin for CakePHP
	by Albert 'Tigr' Zenkoff <albert@tigr.net>

A Bit Of Philosophy
===================

The state of security overall in most projects is miserable. This is
rather annoying to see knowing well that the tools are available, the
knowledge is accessible and there is no excuse for not making things
more secure. So I decided to do a little bit on this front and here
is the user authentication and management plugin that is already fairly
secure but I intend to make it even more so.

What's so different?
 - it disposes with the MD5 hashing of passwords with a fixed salt 
   in favor of using a professionally done bcrypt library that uses
   key stretching and random salts.
   The former is ... funny, the latter is fairly secure even if your
   password database is stolen (and that happens more often than you
   think).
 - All identifiers are 20 digit long numbers. They are randomly
   generated where it matters (user record id) and they are simply
   auto-incremented where I think it is of no importance.
   The practice of using UUID for a user id is just wrong, the UUID
   is not unpredictable.
 - Originally the user was identified only by an email address. 
   Internally, a user always has also an ID but the external identifier 
   is basically indpendent. Unfortunately, it turns out users are
   not comfortable without a user name. Although that's kind of
   useless but they do insist on having an alias, a nickname, a
   user name to identify themselves to the system. Also, some
   external systems only supply user name. So we have to stick
   with user names and also keep the e-mail for verification.
 - The user registration is confirmed by e-mail. There is a lot that
   can be done here but let's face it - that's the accepted practice.
 - If any strange behavior is detected, the user is logged out 
   immediately just in case.
 - The user authentication is kept completely separate from all
   other things, there is no link to any authorization scheme or
   anything else. This plugin tries to make just one thing well.
   Once you have the user ID, you use it in your own authorization
   scheme, of course.

I am not saying that what you get is the ultimate in site security.
However, looking at what people throw together for their site user
management, I think they better use this. At least as a starting 
point.

Potential problems
==================

Since I write the plugin for a particular site and function it may
have things that you are not interested in. You have two ways.
You can wait until I get around to making things configurable and
more flexible. Or you can help making it better. Guess which 
I will appreciate more?

Installation
============

The plugin is written for the 2.2 release originally. What happens
on the other releases I do not know.

I install like this:
1. Put it in your Plugin/Z folder.
2. Add the following to Config/bootstrap.php

	CakePlugin::load(
		array(
		'Z' => array('bootstrap' => true, 'routes' => true)
		)
	);
	Configure::write ('Z.email_from', 'info@yoursite.com');
	Configure::write ('Z.site_title', 'Your Site');

   Additional configuration is in Plugin/Z/Config/bootstrap.php

3. Use the schemas in the plugin's Config/Schema directory
   to create the tables.

4. Add the first administrator user
   by going to
   	http://www.yoursite.com/z/controls/init

5. Login as the newly created admin.

The authentication is done through the standard CakePHP Auth module,
so you just call on the things like $this->Auth->user() and
$this->Session->read('Auth.User') to get to the user data.

For authentication forward people to /z/accounts/login
and for logging them out - to /z/accounts/logout
For registration forward them to /z/accounts/register
User info page is available at /z/users/view
Account management is at /z/controls/accounts

For clarity again, this is not authorization, this is only
authentication. You have to set up authorization separately
as usual.

Good luck!

P.S. For extra info on various subjects in software security
     head over to http://holyhash.com/
