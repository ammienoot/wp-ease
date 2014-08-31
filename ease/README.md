wp-ease
=======

EASE plugin for WordPress

File/Folder permissions
-----------------------

The logout Perl script is picky about directory and file permissions:
- the 'ease' directory and its subdirectories should have permissions set to '755'
- all files within the 'ease' directory and its subdirectories should have permissions set to '644'
- the 'ease/logout.pl' script should have its permissions set to '755'