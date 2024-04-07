<?php

defined('MOODLE_INTERNAL') || die();

$observers = array(

    array(
        'eventname' => '\core\event\user_enrolment_created',
        'callback' => '\local_enrolcourse_event\observers::userEnrol',
    )
);