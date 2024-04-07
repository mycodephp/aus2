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
            $columns = array('sno','studentname', 'categoryname', 'coursename', 'pencentage','grade', 'status');
            $this->define_columns($columns);

            // Define the titles of columns to show in header.
            $headers = array('S no.','Student name', 'Category name', 'Course name', 'Percentage','Grade', 'Status');
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

    function col_sno($values)
    {
        $sno = (($_GET['page']) * 10) + $values->num;

        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return $sno;
        } else {
            return $sno;
        }
    }
    function col_categoryname($values)
    {
        global $DB;
        $fullname = $DB->get_record_sql("SELECT category from {course} where id=$values->courseid");
        $category = $DB->get_record_sql("SELECT name from {course_categories} where id=$fullname->category");


        if ($this->is_downloading()) {
            return $category->name;
        } else {
            return $category->name;
        }
    }



    function col_studentname($values)
    {
        global $DB;
        $fullname = $DB->get_record_sql("SELECT * from {user} where id=$values->userid");
        $name="$fullname->firstname"." "."$fullname->lastname";
        // $name=$fullname->firstname;
        


        if ($this->is_downloading()) {
            return $name;
        } else {
            return $name;
        }
    }

    /**
     * This function is called for each data row to allow processing of
     * columns which do not have a *_cols function.
     * @return string return processed value. Return NULL if no change has
     *     been made.
     */
    function col_coursename($values)
    {
        global $DB;
        $fullname = $DB->get_record_sql("SELECT fullname from {course} where id=$values->courseid");

        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return $fullname->fullname;
        } else {
            return $fullname->fullname;
        }
    }
    // function col_lastaccess($values)
    // {
    //     global $DB;
    //     $last = $DB->get_record_sql("SELECT * from {user_lastaccess} where courseid = $values->courseid and userid=$values->userid");

    //     $current_time = time(); // The current Unix timestamp
    //     $time_diff = $current_time - $last->timeaccess; // Calculate the time difference in seconds

    //     $hours = floor($time_diff / 3600); // Convert to hours
    //     $minutes = floor(($time_diff % 3600) / 60); // Convert to minutes
    //     $seconds = $time_diff % 60; // Get the remaining seconds
    //     $time = $hours . " h, " . $minutes . " min, " . $seconds . " seconds";
    //     if (!$last) {
    //         $time = 'Never access';
    //     }

    //     if ($this->is_downloading()) {

    //         return $time;
    //     } else {
    //         return $time;
    //     }
    // }
    function col_pencentage($values)
    {
        global $DB;
        $course = $DB->get_record_sql("SELECT * from {course} where id=$values->courseid");

        $progress = \core_completion\progress::get_course_progress_percentage($course, $values->userid);

        if ($progress == NULL) {
            $progress = 0;
        }

        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return (int)$progress;
        } else {
            return (int)$progress;
        }
    }
    function col_grade($values)
    {
        global $DB;
        $garde = $DB->get_record_sql("SELECT * from {grade_items} as gi join {grade_grades} as gg on gg.itemid=gi.id where gg.userid=$values->userid and gi.courseid=$values->courseid and gi.itemtype='course' ");
        
        $max=$DB->get_record_sql("SELECT * FROM {grade_items} where courseid=$values->courseid and itemtype='course'");
       

        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return (int)$garde->finalgrade.'/'.(int)$max->grademax;
        } else {
            return (int)$garde->finalgrade.'/'.(int)$max->grademax;
        }
    }
    
    function col_status($values)
    {
        global $DB;
        $course = $DB->get_record_sql("SELECT * from {course} where id=$values->courseid");

        $progress = \core_completion\progress::get_course_progress_percentage($course, $values->userid);

        if ($progress == NULL) {
            $progress = 0;
        }
        if ($progress == 0) {
            $status = 'Inactive';
        } else if ($progress > 0 && $progress < 100) {
            $status = 'Inprogress';
        } else if ($progress == 100) {
            $status = 'Completed';
        }
        if($status=='Inactive'){
            $last = $DB->get_record_sql("SELECT * from {user_lastaccess} where courseid = $values->courseid and userid=$values->userid");

    
        if ($last) {
            $status = 'Inprogress';
        }

        }
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {

            return $status;
        } else {
            return $status;
        }
    }
}
