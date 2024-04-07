<?php

/**
 * Simple file test_custom.php to drop into root of Moodle installation.
 * This is an example of using a sql_table class to format data.
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php'); // Moodle configuration file
require "$CFG->libdir/tablelib.php";
require "class.php";

require_login();
if(!is_siteadmin()){
    redirect("$CFG->wwwroot/local/dashboard/student/","Invalid link!!!");
    
}

$userfilter = optional_param('userfilter', '', PARAM_RAW);
$coursefilter = optional_param('coursefilter', '', PARAM_RAW);
$statusfilter = optional_param('statusfilter', '', PARAM_RAW);




$title = "Course Progress Report";
$pagetitle = $title;
$PAGE->set_title($title);
$PAGE->set_heading($title);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/admin_reports/course_progress_report');
$PAGE->set_pagelayout('standard');
$download = optional_param('download', '', PARAM_ALPHA);
$table = new test_table('uniqueid');
$table->is_downloading($download, "Course Progress Report", 'testing123');
if (!$table->is_downloading()) {
    // Only print headers if not asked to download data.
    // Print the page header.
    $PAGE->set_title("Course Progress Report");
    $PAGE->set_heading("Course Progress Report");
    $PAGE->navbar->add("Course Progress Report", new moodle_url('/local/admin_reports/course_progress_report/index.php'));
    // $previewnode = $PAGE->navigation->add('Percentage of students who never log in from any class', new moodle_url('/local/admin_reports/course_progress_report/index.php'), navigation_node::TYPE_CONTAINER);
    echo $OUTPUT->header();
    echo '<form method="get" class="d-flex align-items-end">';
    echo '<div class="mr-3"><b><label for="userfilter">User filter:</label></b>';
    echo '<input type="text" name="userfilter" id="userfilter" value="' . $userfilter . '"></div>';
    echo '<div><b><label for="coursefilter">Course filter:</label></b>';
    echo '<input type="text" name="coursefilter" id="coursefilter" value="' . $coursefilter . '"></div>';
    echo '<div>';
    echo '<b><label for="statusfilter">Status:</label></b>';
    echo '<select name="statusfilter" id="statusfilter">';
    echo '<option value="">All</option>';
    echo '<option value="inactive" ' . ($statusfilter == "inactive" ? "selected" : "") . '>Inactive</option>';
    echo '<option value="completed" ' . ($statusfilter == "completed" ? "selected" : "") . '>Completed</option>';
    echo '<option value="inprogress" ' . ($statusfilter == "inprogress" ? "selected" : "") . '>In Progress</option>';
    echo '</select>';
    echo '</div>';
    echo '<div><input type="submit" value="Filter" Class="submit"><a href="' . $CFG->wwwroot . '/local/admin_reports/course_progress_report/index.php"><input type="button" value="Reset" Class="reset"></a></div>';
    echo '</form>';
}
$params = [
    'userfilter' => "%{$userfilter}%",
    'coursefilter' => "%{$coursefilter}%",
    'status' => "%{$statusfilter}"

];
global $DB;
$a_array = array();
$a_array1 = array();


$e_u_status = $DB->get_records_sql("SELECT ue.id,ue.userid,e.courseid from {user_enrolments} as ue JOIN {enrol} as e ON ue.enrolid=e.id join {user} as u on u.id=ue.userid join {course} as c on c.id=e.courseid join {role_assignments} as ra on ra.userid=u.id join {role} as r on ra.roleid=r.id where r.shortname='student'");
foreach ($e_u_status as $e) {
    $course = $DB->get_record_sql("SELECT * from {course} where id=$e->courseid");

    $progress = \core_completion\progress::get_course_progress_percentage($course, $e->userid);
    $status = NULL;

    if ($progress == NULL) {
        $progress = 0;
    }
    if ($progress == 0) {
        $status = 'inactive';
    } else if ($progress > 0 && $progress < 100) {
        $status = 'inprogress';
    } else if ($progress == 100) {
        $status = 'completed';
    }
    if($status=='inactive'){
        $last = $DB->get_record_sql("SELECT * from {user_lastaccess} where courseid = $course->id and userid=$e->userid");


    if ($last) {
        $status = 'inprogress';
    }

    }

    if ($status == $statusfilter) {
        $a_array[] = $e->id;
    } else {
        $a_array1[] = $e->id;
    }
}

if ($statusfilter) {
    $a_array2 = implode(',', $a_array);
} else {
    $a_array2 = implode(',', $a_array1);
}


if (empty($a_array2)) {
    echo 'No record found!!';
    echo $OUTPUT->footer();
} else {

    $fields = 'ue.id,ue.userid,e.courseid,(@row_number:=@row_number + 1) as num';
    $from = '{user_enrolments} as ue JOIN {enrol} as e ON ue.enrolid=e.id join {user} as u on u.id=ue.userid join {course} as c on c.id=e.courseid';
    $where = "CONCAT(u.firstname,' ',u.lastname) LIKE :userfilter AND c.fullname LIKE :coursefilter and ue.id in ($a_array2)";

    $DB->execute('SET @row_number = 0', array());

    $table->set_sql($fields, $from, $where, $params);
    $table->define_baseurl("$CFG->wwwroot/local/admin_reports/course_progress_report/index.php?userfilter={$userfilter}&coursefilter={$coursefilter}&statusfilter={$statusfilter}");
    $table->out(10, true);
    // echo '
    // <style>
    // .icon
    // {
    //     display:none !important;
    // } 
    // .header a{
    //     pointer-events: none !important;
    // }
    // </style>
    // ';
    if (!$table->is_downloading()) {
        echo $OUTPUT->footer();
    }
}
echo'
<style>
#page-local-admin_reports-course_progress_report table.flexible.table.table-striped.table-hover.generaltable.generalbox th {
    pointer-events: none !important;
}
</style>
';
