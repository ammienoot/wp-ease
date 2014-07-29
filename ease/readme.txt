=== Shared Secret Authentication with EASE option ===
Contributors: Stephen P Vickers (based on HTTP Authentication by dwc)
Tags: authentication
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 1.0

Use an external authentication source in WordPress.

== Description ==

The Shared Secret Authentication plugin allows you to use existing means of authenticating people to WordPress and pass their credentials using a shared secret.

== Installation ==

1. Login as an existing user, such as admin.
2. Upload `sso-authentication.php` to your plugins folder, usually `wp-content/plugins`.
3. Activate the plugin on the Plugins screen.
5. Logout.
6. Create a script to pass the username, timestamp and MAC to /wp-login.php

Note: This version works with WordPress 3.0 and above.

This plugin also supports WordPress MU as of version 2.4. You can install it globally or on a per-site basis. Refer to the WordPress MU documentation for more information on installing plugins.

== Frequently Asked Questions ==

= What authentication mechanisms can I use? =

...

= How does this plugin authenticate users? =

This plugin doesn't actually authenticate users. It simply feeds WordPress the name of a user who has successfully authenticated through other means.

This plugin generates a random password each time you create a user or edit an existing user's profile. However, since this plugin requires an external authentication mechanism, this password is not requested by WordPress. Generating a random password helps protect accounts, preventing one authorized user from pretending to be another.

= If I disable this plugin, how will I login? =

Because this plugin generates a random password when you create a new user or edit an existing user's profile, you will most likely have to reset each user's password if you disable this plugin. WordPress provides a link for requesting a new password on the login screen.

Also, you should leave the `admin` user as a fallback, i.e. create a new account to use with this plugin. As long as you don't edit the `admin` profile, WordPress will store the password set when you installed WordPress.

In the worst case scenario, you may have to use phpMyAdmin or the MySQL command line to [reset a user's password](http://codex.wordpress.org/Resetting_Your_Password).

== Changelog ==

= 1.0 =
* First release
