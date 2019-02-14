<?php
/**
 * admin tool brcli
 * Backup & restore command line interface
 * @package admin
 * @subpackage tool
 * @author Paulo Júnior <pauloa.junior@ufla.br>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Backup and Restore Command-Line Interface';
$string['unknowoption'] = 'Unknow option: {$a}';
$string['helpoption'] = 
'Perform backup of the given course.

Options:
--categoryid=INTEGER        Category ID for backup.
--destination=STRING        Path where to store backup file. If not set the backup
                            will be stored within the admin/tool/brcli/bcks folder.
-h, --help                  Print out this help.

Example:
    sudo -u www-data /usr/bin/php admin/tool/brcli/backup.php --categoryid=1 --destination=/moodle/backup/
';
$string['noadminaccount'] = 'Error: No admin account was found!';
$string['directoryerror'] = 'Error: Destination directory does not exists or not writable!';
$string['nocategory'] = 'Error: No category was found!';
$string['performingbck'] = 'Performing backup of the {$a} course...';
$string['backupdone'] = 'Done!';
