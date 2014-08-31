wp-ease
=======

EASE plugin for WordPress

File/Folder permissions
-----------------------

The logout Perl script is picky about directory and file permissions:
- the 'ease' directory and its subdirectories should have permissions set to '755'
- all files within the 'ease' directory and its subdirectories should have permissions set to '644'
- the 'ease/logout.pl' script should have its permissions set to '755'

Bulk creating new accounts
---------------------

You can bulk create user accounts by visiting:
- {wp install dir}/wp-content/plugins/ease/login/create.php

You can enter a list of UUNs in the 'UUNs' field, either:
- a comma-delimited list of UUNs
- or a space-delimited list of UUNs
- or UUNs on separate lines