<?php

require_once('../../config.php'); // Include Moodle configuration file

// Define the user ID you want to check
// Change this to the ID of the user you want to check
global $DB,$CFG,$USER;
// Get user's enrolled courses
$userCourses = enrol_get_users_courses($USER->id);

// Check if the user is enrolled in any courses
if (!empty($userCourses)) {
    // User is enrolled in courses
    echo "User is enrolled in the following courses:<br>";
    foreach ($userCourses as $course) {
        echo "Course ID: " . $course->id . ", Course Name: " . $course->fullname . "<br>";
    }
} else {
    // User is not enrolled in any courses
    echo "User is not enrolled in any courses.";
}



?>