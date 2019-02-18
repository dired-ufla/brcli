# BrCLI
BrCLI (Backup & Restore Command-Line Interface) is a plug-in integrated in Moodle that allows administrators to backup and restore courses via a command line interface.

## Features
* backup all courses of a specific category using a single command
* restore a set of courses backup files in a specific category

## How to use
Please type the commands below to know how to use this plugin:

`sudo -u www-data /usr/bin/php admin/tool/brcli/backup.php --help`

`sudo -u www-data /usr/bin/php admin/tool/brcli/restore.php --help`

## Release notes
* v1.1 - it is mandatory to inform the destination folder.
* v1.0 - the restore feature is available.
