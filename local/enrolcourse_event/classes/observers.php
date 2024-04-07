<?php
namespace local_enrolcourse_event;
defined('MOODLE_INTERNAL') || die();

class observers {
  
public static function userEnrol(\core\event\user_enrolment_created $event) {
    

    global $DB,$CFG;
    $userForcepasswordChange = $DB->get_record('user_preferences', array('userid' => $event->relateduserid));
   if(empty($userForcepasswordChange)){
    if(is_siteadmin()){
        require_once($CFG->libdir.'/moodlelib.php');
        $pwdval = bin2hex(random_bytes(8));
        $user = $DB->get_record('user', array('id' => $event->relateduserid));
        $course = $DB->get_record('course', array('id' => $event->courseid));
        $usernew = $DB->get_record('user', array('id' => $event->relateduserid));
        $subject="course enrol Student";
       // $messagetext="course enrolled successfully please login this url  $CFG->wwwroot <br> temporary password= $pwdval <br> username= $user->username";
       $course = get_course($event->courseid);
       $courseName="";
       if ($course) {
        // Course found, get the course name
        $courseName = $course->fullname;
       
    } 
       $messagetext='<!DOCTYPE html>
       <html lang="en">
       <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Email Template</title>
       </head>
       <body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; padding: 20px;">
         <div style="max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 5px;">
           <h2>Welcome to Our Learning Platform!</h2>
           <p style="margin-bottom: 20px;">You have been enrolled in the following course:</p>
           <ul>
             <li><strong>Course Name:</strong> '.$courseName.'</li>
           </ul>
           <p>Your login credentials are:</p>
           <ul>
             <li><strong>Username:</strong> '.$user->username.'</li>
             <li><strong>Temporary Password:</strong> '.$pwdval.'</li>
           </ul>
           
           <p>Click the button below to start learning:</p>
           <a href="'.$CFG->wwwroot.'" style="display: inline-block; background: #007bff; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px;">Start Learning</a>
         </div>
       </body>
       </html>
       ';
       
       $headers = array('Content-Type: text/html; charset=UTF-8','From:');
        $new_password_hash=password_hash($pwdval, PASSWORD_DEFAULT);
        $DB->execute('UPDATE {user} set password=:passowrd_hash where id=:userid_input', ['passowrd_hash' => $new_password_hash, 'userid_input' =>$event->relateduserid]);
        set_user_preference('auth_forcepasswordchange', 1, $usernew);
        return email_to_user($user, $user, $subject, $messagetext, $messagehtml = '', $attachment = '', $attachname = '',
        $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79);

    }
    }
}

}