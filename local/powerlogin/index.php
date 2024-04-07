<?php

require_once('../../config.php');// Moodle configuration file
// require_login();

// if(is_siteadmin()){


$title = "Power Login";
$pagetitle = $title;
$PAGE->set_title($title);
$PAGE->set_heading($title);
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/powerlogin/index.php');
$PAGE->set_pagelayout('standard');


echo $OUTPUT->header();
$templatecontext = [
    'cfg' => $CFG->wwwroot,
    'table'=>"as"
 ];
 


echo $OUTPUT->render_from_template('local_powerlogin/powerlogin', $templatecontext);
echo $OUTPUT->footer();

//}