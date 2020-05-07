<?php
/**
 * admin tool brcli
 * Backup & restore command line interface
 * @package admin
 * @subpackage tool
 * @author Paulo Júnior <pauloa.junior@ufla.br> based on https://github.com/mudrd8mz/moodle-toolbox/blob/master/mbz/mbz-restorecourses.php
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', 1);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(array(
    'categoryid' => false,
    'source' => '',
    'help' => false,
    ), array('h' => 'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('unknowoption', 'tool_brcli', $unrecognized));
}

if ($options['help'] || !($options['categoryid']) || !($options['source'])) {
    echo get_string('helpoptionres', 'tool_brcli');
    die;
}

$admin = get_admin();
if (!$admin) {
    cli_error(get_string('noadminaccount', 'tool_brcli'));
}

$dir = rtrim($options['source'], '/');
if (empty($dir) || !file_exists($dir) || !is_dir($dir)) {    
    cli_error(get_string('directoryerror', 'tool_brcli'));
}

// Check that the category exists.
if ($DB->count_records('course_categories', array('id'=>$options['categoryid'])) == 0) {
    cli_error(get_string('nocategory', 'tool_brcli'));
} 

$index = 1;
$sourcefiles = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
$amount_of_courses = iterator_count($sourcefiles);

foreach ($sourcefiles as $sourcefile) {
    if ($sourcefile->getExtension() !== 'mbz') {
        continue;
    }

    mtrace(get_string('performingres', 'tool_brcli', $index . '/' . $amount_of_courses));

    // Extract the file.
    $packer = get_file_packer('application/vnd.moodle.backup');
    $backupid = restore_controller::get_tempdir_name(SITEID, $admin->id);
    $path = "$CFG->tempdir/backup/$backupid/";
    if (!$packer->extract_to_pathname($sourcefile->getPathname(), $path)) {
        mtrace(get_string('invalidbackupfile', 'tool_brcli', $sourcefile->getFilename()));
    }

    // Transaction.
    $transaction = $DB->start_delegated_transaction();
 
    // Create new course.
    $folder             = $backupid; // as found in: $CFG->dataroot . '/temp/backup/' 
    $categoryid         = $options['categoryid']; // e.g. 1 == Miscellaneous
    $userdoingrestore   = $admin->id; // e.g. 2 == admin
    $courseid           = restore_dbops::create_new_course('', '', $categoryid);
 
    // Restore backup into course.
    $controller = new restore_controller($folder, $courseid, 
    backup::INTERACTIVE_NO, backup::MODE_GENERAL, $userdoingrestore,
    backup::TARGET_NEW_COURSE);
    if ($controller->execute_precheck()) {
		try {
			$controller->execute_plan();
		} catch (Exception $e) {
			mtrace(get_string('restoringfailed', 'tool_brcli', $index));
			continue;
		}
    } else {
        try {
            $transaction->rollback(new Exception('Prechecked failed'));
        } catch (Exception $e) {
            unset($transaction);
            $controller->destroy();
            unset($controller);
            continue;
        }
    }

    $index = $index + 1;

// Commit and clean up.
    $transaction->allow_commit();
    unset($transaction);
    $controller->destroy();
    unset($controller);
}

mtrace(get_string('operationdone', 'tool_brcli'));

exit(0);
