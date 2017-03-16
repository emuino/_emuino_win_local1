WWebserver for command line
===========================
wwebserver_cmd.exe --help

Default settings used from cfg/ directory. 


Virtual directories
===================
You can define virtual directories to handle multiple document paths.
Therefore edit the following configuration file:

C:\WWebserver\cfg\vdir.txt

# Definitions of virtual directories
# <Path>;<Directory>
#
# Note: There must be an '/' on end of <Path>

pma/;C:\phpMyAdmin
typo3/;C:\typo3
drupal/;C:\drupal
wordpress/;C:\wordpress


You can start the applications now verry simple like: http://localhost/wordpress

Note: After changing vdir.txt please STOP and START Webserver again.


CGI programs
============
You can define own CGI Programs for example Perl or Ruby.
Therefore edit the following configuration file:

C:\WWebserver\cfg\cgi.txt

# Definitions of CGI programs
# <Alias 1> <Alias 2> ...;<Full path to CGI program> | [PHPBuildIn]

.rb;C:\ruby\bin\ruby.exe
.pl;C:\perl\bin\perl.exe
.php .php3 .php4;PHPBuildIn

In this example we associate file extensions .rb with Ruby scripts and .pl with Perl scripts.
.php .php3 .php4 files are associated with buildin PHP support.

Note: After changing cgi.txt please STOP and START Webserver again.


PHP configuration
=================
WWebserver comes with buildin PHP support (5.4.45) and all extensions.
You can control the PHP environment with the php.ini file:

C:\WWebserver\cfg\php.ini

You can enable/disable the PHP extension DLL's located in directory:

C:\WWebserver\ext

Note: After changing php.ini you have to EXIT and RESTART the Webserver.



See also:
https://www.mwiede.de/windows-php-webserver/
(C) Matthias Wiede 2008-2016