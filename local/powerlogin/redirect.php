<?php
require_once("../../config.php");
require_once($CFG->dirroot.'/login/lib.php');
require_once($CFG->dirroot.'/lib/moodlelib.php');
require_once('../../user/lib.php');
global $CFG, $DB, $USER;

$username=$_POST['secondField'];
$userdata= $DB->get_record('user', array('username'=>$username));
if($userdata){
complete_user_login($userdata); redirect($CFG->wwwroot);
}
else{
echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Error Message</title>
</head>
<body>

<h1 style="color: red; text-align: center;">Invalid Username</h1>

</body>
</html>
';
}
?>