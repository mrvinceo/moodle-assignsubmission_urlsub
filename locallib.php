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
        global $PAGE;

        // Enqueue JavaScript module for dynamic URL-title pairs
        $PAGE->requires->js_call_amd('assignsubmission_urlsub/urlsubform', 'init');

        $mform->addElement('html', '<div id="urlsub_container"></div>');
        $mform->addElement('button', 'add_url', get_string('addurl', 'assignsubmission_urlsub'), array('id' => 'add_url_button'));

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
            // Assume $data->urls is an array of ['url' => '...', 'title' => '...']
            $urlsubsubmission->urlsub = json_encode($data->urls);
            $updatestatus = $DB->update_record('assignsubmission_urlsub', $urlsubsubmission);
            return $updatestatus;
        } else {
            $urlsubsubmission = new stdClass();
            $urlsubsubmission->urlsub = json_encode($data->urls);

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
     * @param bool $showviewlink - If the summary has been truncated set to true
     * @return string
     */
    public function view_summary(stdClass $submission, &$showviewlink) {
        $urlsubsubmission = $this->get_urlsub_submission($submission->id);

        if ($urlsubsubmission && !empty($urlsubsubmission->urlsub)) {
            $urls = json_decode($urlsubsubmission->urlsub, true);
            $displayHtml = '';
            foreach ($urls as $pair) {
                $displayHtml .= html_writer::link(new moodle_url($pair['url']), $pair['title']) . '<br>';
            }
            return $displayHtml;
        }
        return '';
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        $urlsubsubmission = $this->get_urlsub_submission($submission->id);
        if ($urlsubsubmission && !empty($urlsubsubmission->urlsub)) {
            $urls = json_decode($urlsubsubmission->urlsub, true);
            $loginfo = array_map(function($pair) {
                return "{$pair['title']}: {$pair['url']}";
            }, $urls);
            return implode(", ", $loginfo);
        }
        return '';
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // No change is necessary for this method unless you have a separate table for URLs.
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
        return empty($urlsubsubmission) || empty($urlsubsubmission->urlsub);
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