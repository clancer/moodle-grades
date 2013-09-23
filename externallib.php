<?php

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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_wstemplate_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function hello_world_parameters() {
        return new external_function_parameters(
                array('welcomemessage' => new external_value(PARAM_TEXT, 'The welcome message. By default it is "Hello world,"', VALUE_DEFAULT, 'Hello world, '))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function hello_world($welcomemessage = 'Hello world, ') {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::hello_world_parameters(),
                array('welcomemessage' => $welcomemessage));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }


global $CFG, $DB; 

$context = get_context_instance(CONTEXT_COURSE, 3);
$users = get_enrolled_users($context);
$myuser = -1;
foreach ($users as $user ) {
    if ($user->username == "rohan") {
        $myuser = $user->id;
    }
}
//$users = array($users[1]);
if ($myuser != -1) {

require_once($CFG->libdir.'/gradelib.php');
    $courseid = 3;
    $userids = array($myuser);
    $item = new stdClass();
    if ($grade_items = grade_item::fetch_all(array('courseid'=>$courseid))) {
        foreach ($grade_items as $grade_item) {
            $decimalpoints = null;

            if (empty($grade_item->outcomeid)) {
                // prepare information about grade item
                $item = new stdClass();
                $item->itemnumber = $grade_item->itemnumber;
                $item->scaleid    = $grade_item->scaleid;
                $item->name       = $grade_item->get_name();
                $item->grademin   = $grade_item->grademin;
                $item->grademax   = $grade_item->grademax;
                $item->gradepass  = $grade_item->gradepass;
                $item->locked     = $grade_item->is_locked();
                $item->hidden     = $grade_item->is_hidden();
                $item->grades     = array();

                $grade_grades = grade_grade::fetch_users_grades($grade_item, $userids, true);
                    foreach ($userids as $userid) {
                        $grade_grades[$userid]->grade_item =& $grade_item;

                        $grade = new stdClass();
                        $grade->grade          = $grade_grades[$userid]->finalgrade;
                        $grade->locked         = $grade_grades[$userid]->is_locked();
                        $grade->hidden         = $grade_grades[$userid]->is_hidden();
                        $grade->overridden     = $grade_grades[$userid]->overridden;
                        $grade->feedback       = $grade_grades[$userid]->feedback;
                        $grade->feedbackformat = $grade_grades[$userid]->feedbackformat;
                        $grade->usermodified   = $grade_grades[$userid]->usermodified;
                        $grade->datesubmitted  = $grade_grades[$userid]->get_datesubmitted();
                        $grade->dategraded     = $grade_grades[$userid]->get_dategraded();

                        $item->grades[$userid] = $grade;
                    }
            }    
        }
    }


ob_start();
var_dump($item);
$result = ob_get_clean();
return $params['welcomemessage'] . $result .  $USER->firstname . $USER->id;
}
return "error username not found";
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function hello_world_returns() {
        return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }



}
