<?php

/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php'); // Moodle configuration file
require "$CFG->libdir/tablelib.php";
require "class.php";
require_login();

$coursefilter = optional_param('coursefilter', '', PARAM_RAW);


$title = "Course Completion Report";
$pagetitle = $title;
$PAGE->set_title($title);
$PAGE->set_heading($title);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/admin_reports/course_completion_report');
$PAGE->set_pagelayout('standard');
$download = optional_param('download', '', PARAM_ALPHA);
$table = new test_table('uniqueid');
$table->is_downloading($download, "Course Completion Report", 'testing123');
if (!$table->is_downloading()) {
    // Only print headers if not asked to download data.
    // Print the page header.
    $PAGE->set_title("Course Completion Report");
    $PAGE->set_heading("Course Completion Report");
    $PAGE->navbar->add("Course Completion Report", new moodle_url('/local/admin_reports/course_completion_report/index.php'));
   
    echo $OUTPUT->header();
    echo '<form method="get" class="d-flex align-items-end">';
    echo '<div class="mr-3"><b><label for="userfilter">Course filter:</label></b>';
   
    echo '<input type="text" name="coursefilter" id="coursefilter" value="' . $coursefilter . '"></div>';
    
    echo '<div><input type="submit" value="Filter" Class="submit"><a href="' . $CFG->wwwroot . '/local/admin_reports/course_completion_report/index.php"><input type="button" value="Reset" Class="reset"></a></div>';
    echo '</form>';
}
$params = [
    'coursefilter' => "%{$coursefilter}%"
];
global $DB;


$DB->execute('SET @row_number = 0', array());
$fields = 'c.id,c.fullname as coursename,cc.name,(@row_number:=@row_number + 1) as num';
$from = '{course} as c JOIN {course_categories} as cc ON c.category=cc.id';
$where = "c.id>0 and c.fullname LIKE :coursefilter";
$table->set_sql( $fields, $from,$where,$params); 
$table->define_baseurl("$CFG->wwwroot/local/admin_reports/course_completion_report/index.php?coursefilter={$coursefilter}");
$table->out(10, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}
echo'
<style>
#page-local-admin_reports-course_completion_report table.flexible.table.table-striped.table-hover.generaltable.generalbox th {
    pointer-events: none !important;
}
</style>
';
