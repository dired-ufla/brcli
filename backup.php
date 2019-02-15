<?php
/**
 * admin tool brcli
 * Backup & restore command line interface
 * @package admin
 * @subpackage tool
 * @author Paulo Júnior <pauloa.junior@ufla.br> based on /admin/cli/backup.php
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('CLI_SCRIPT', 1);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(array(
    'categoryid' => false,
    'destination' => '',
    'help' => false,
    ), array('h' => 'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('unknowoption', 'tool_brcli', $unrecognized));
}

if ($options['help'] || !($options['categoryid'])) {
    echo get_string('helpoptionbck', 'tool_brcli');
    die;
}

$admin = get_admin();
if (!$admin) {
    mtrace(get_string('noadminaccount', 'tool_brcli'));
    die;
}

// Do we need to store backup somewhere else?
$dir = rtrim($options['destination'], '/');
if (!empty($dir)) {
    if (!file_exists($dir) || !is_dir($dir) || !is_writable($dir)) {
        mtrace(get_string('directoryerror', 'tool_brcli'));
        die;
    }
} else {
    $dir = __DIR__.'/bcks';
}

// Check that the category exists.
if ($options['categoryid']) {
    if ($DB->count_records('course_categories', array('id'=>$options['categoryid'])) == 0) {
        mtrace(get_string('nocategory', 'tool_brcli'));
        die;
    }
} 

$courses = $DB->get_records('course', array('category'=>$options['categoryid']));
$amount_of_courses = count($courses);

$index = 1;

foreach ($courses as $cs) {
    $bc = new backup_controller(backup::TYPE_1COURSE, $cs->id, backup::FORMAT_MOODLE,
                                backup::INTERACTIVE_YES, backup::MODE_GENERAL, $admin->id);
    
    mtrace(get_string('performingbck', 'tool_brcli', $index . '/' . $amount_of_courses));

    // Set the default filename.
    $format = $bc->get_format();
    $type = $bc->get_type();
    $id = $bc->get_id();
    $users = $bc->get_plan()->get_setting('users')->get_value();
    $anonymised = $bc->get_plan()->get_setting('anonymize')->get_value();
    $filename = backup_plan_dbops::get_default_backup_filename($format, $type, $id, $users, $anonymised);
    $bc->get_plan()->get_setting('filename')->set_value($filename);

    // Execution.
    $bc->finish_ui();
    $bc->execute_plan();
    $results = $bc->get_results();
    $file = $results['backup_destination']; // May be empty if file already moved to target location.

    // Do we need to store backup somewhere else?
    if ($file) {
        if ($file->copy_content_to($dir.'/'.$filename)) {
            $file->delete();
        } else {
            mtrace(get_string('directoryerror', 'tool_brcli'));
        }
    }
    $bc->destroy();
    $index = $index + 1;
}
mtrace(get_string('operationdone', 'tool_brcli'));

exit(0);