<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   mod_forum
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_admin_reports_extend_navigation(global_navigation $nav3)
{
    

    global $CFG, $PAGE, $DB, $USER;
   if(is_siteadmin()){
        $nav3->add(
            "Course Completion Report",
            new moodle_url($CFG->wwwroot . '/local/admin_reports/course_completion_report/index.php'),
            navigation_node::TYPE_SYSTEM,
            null,
            'local_admin_reports',
            new pix_icon('i/course', '')
        )->showinflatnavigation = true;


        $nav3->add(
            "Course Progress Report ",
            new moodle_url($CFG->wwwroot . '/local/admin_reports/course_progress_report/index.php'),
            navigation_node::TYPE_SYSTEM,
            null,
            'local_admin_reports',
            new pix_icon('i/course', '')
        )->showinflatnavigation = true;
    
        }

    
    

}
