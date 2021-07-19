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
 * This file contains the definition for the library class for urlsub submission plugin
 *
 * @package    assignsubmission_urlsub
 * @author     Paul Vincent
 * @copyright  2021 TEL, Open College of the Arts {@link https://spaces.oca.ac.uk/telteam}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class assign_submission_urlsub extends assign_submission_plugin {

    /**
     * Get the name of the online text submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('urlsub', 'assignsubmission_urlsub');
    }

    /**
     * Get submission urlsub from the database
     *
     * @param  int $submissionid
     * @return mixed
     */
    private function get_urlsub_submission($submissionid) {
        global $DB;

        return $DB->get_record('assignsubmission_urlsub', array('submission' => $submissionid));
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        $elements = array();

        $submissionid = $submission ? $submission->id : 0;

        if (!isset($data->urlsub)) {
            $data->urlsub = '';
            $data->urlsubtitle = '';
        }

        if ($submission) {
            $urlsubsubmission = $this->get_urlsub_submission($submission->id);
            if ($urlsubsubmission) {
                $data->urlsub = $urlsubsubmission->urlsub;
                $data->urlsubtitle = $urlsubsubmission->urlsubtitle;
            }
        }

        $mform->addElement('text', 'urlsubtitle', get_string('urlsubtitle', 'assignsubmission_urlsub'), null);
        $mform->setType('urlsubtitle', PARAM_TEXT);

        $mform->addElement('text', 'urlsub', $this->get_name(), null);
        $mform->setType('urlsub', PARAM_TEXT);
        $mform->addHelpButton('urlsub', 'urlsub', 'assignsubmission_urlsub');

        return true;
    }

    /**
     * Save URL Submission data to the database.
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB;

        $urlsubsubmission = $this->get_urlsub_submission($submission->id);

        if ($urlsubsubmission) {

            $urlsubsubmission->urlsub = $data->urlsub;
            $urlsubsubmission->urlsubtitle = $data->urlsubtitle;
            $updatestatus = $DB->update_record('assignsubmission_urlsub', $urlsubsubmission);
            return $updatestatus;
        } else {

            $urlsubsubmission = new stdClass();
            $urlsubsubmission->urlsub = $data->urlsub;
            $urlsubsubmission->urlsubtitle = $data->urlsubtitle;

            $urlsubsubmission->submission = $submission->id;
            $urlsubsubmission->assignment = $this->assignment->get_instance()->id;
            $urlsubsubmission->id = $DB->insert_record('assignsubmission_urlsub', $urlsubsubmission);
            return $urlsubsubmission->id > 0;
        }
    }

     /**
      * Display URL Submission
      *
      * @param stdClass $submission
      * @param bool $showviewlink - If the summary has been truncated set this to true
      * @return string
      */
    public function view_summary(stdClass $submission, &$showviewlink) {
        $urlsubsubmission = $this->get_urlsub_submission($submission->id);

        if ($urlsubsubmission) {
            $sub = $urlsubsubmission->urlsub;
            $subtitle = $urlsubsubmission->urlsubtitle;
            if (empty($subtitle)) {
                $subtitle = $sub;
            }
            if (!empty($sub)){
                $urlsub = get_string('urlsubheading', 'assignsubmission_urlsub') . '<br>' .
                get_string('urlbodytext', 'assignsubmission_urlsub',['link' => $sub, 'name' => $subtitle]);
                
                return $urlsub;
            }
        }
        return '';
    }
    //TODO: Need to check for https:// at the start of URLs and prepend that if not exists.


    /**
     * Formatting for log info
     *
     * @param stdClass $submission The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        // Format the info for each submission plugin (will be logged).
        $urlsubsubmission = $this->get_urlsub_submission($submission->id);
        $urlsubloginfo = get_string('urlsubforlog',
                                         'assignsubmission_urlsub',
                                         $urlsubsubmission->urlsub);

        return $urlsubloginfo;
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        $DB->delete_records('assignsubmission_urlsub',
                            array('assignment' => $this->assignment->get_instance()->id));

        return true;
    }

    /**
     * No text is set for this plugin
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        $urlsubsubmission = $this->get_urlsub_submission($submission->id);

        return empty($urlsubsubmission->urlsub);
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        global $DB;

        // Copy the assignsubmission_urlsub record.
        $urlsubsubmission = $this->get_urlsub_submission($sourcesubmission->id);
        if ($urlsubsubmission) {
            unset($urlsubsubmission->id);
            $urlsubsubmission->submission = $destsubmission->id;
            $DB->insert_record('assignsubmission_urlsub', $urlsubsubmission);
        }
        return true;
    }

}
