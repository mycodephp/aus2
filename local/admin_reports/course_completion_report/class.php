<?php

/**
 * Test table class to be put in test_table.php of root of Moodle installation.
 *  for defining some custom column names and proccessing
 * Username and Password feilds using custom and other column methods.
 */
class test_table extends table_sql
{
    /**
     * Constructor
     * @param int $uniqueid all tables have to have a unique id, this is used
     *      as a key when storing table properties like sort order in the session.
     */
    function __construct($uniqueid)
    {

        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('num', 'name', 'coursename', 'student', 'pencentage');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('S no.', 'Category name', 'Course name', 'No. of students', 'Percentage of course completion by students');
        $this->define_headers($headers);
    }

    /**
     * This function is called for each data row to allow processing of the
     * username value.
     *
     * @param object $values Contains object with all the values of record.
     * @return $string Return username with link to profile or username only
     *     when downloading.
     */

    function col_num($values)
    {
        $num = (($_GET['page']) * 10) + $values->num;

        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return $num;
        } else {
            return $num;
        }
    }
    function col_name($values)
    {
        global $DB;


        if ($this->is_downloading()) {
            return $values->name;
        } else {
            return $values->name;
        }
    }

    function col_coursename($values)
    {
        global $DB;
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return $values->coursename;
        } else {
            return $values->coursename;
        }
    }
    function col_student($values)
    {
        global $DB;
        $context = context_course::instance($values->id);
        $users = $DB->get_records_sql("SELECT ra.id FROM {role_assignments} as ra join {role} as r on ra.roleid=r.id where ra.contextid=$context->id and r.shortname='student'");
        $user = COUNT($users);


        if ($this->is_downloading()) {

            return $user;
        } else {
            return $user;
        }
    }
    function col_pencentage($values)
    {
        global $DB;
        $count1 = 0;
        $context = context_course::instance($values->id);
        $users = $DB->get_records_sql("SELECT ra.id,ra.userid FROM {role_assignments} as ra join {role} as r on ra.roleid=r.id where ra.contextid=$context->id and r.shortname='student'");
        $count = COUNT($users);
        foreach ($users as $u) {
          
            $course = $DB->get_record_sql("SELECT * from {course} where id=$values->id");

            $progress = \core_completion\progress::get_course_progress_percentage($course, $u->userid);
            if ($progress == 100) {
                $count1++;
            }
        }
        $percentage=($count1/$count)*100;


        // // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return (int)$percentage;
        } else {
            return (int)$percentage;
        }
    }
}
