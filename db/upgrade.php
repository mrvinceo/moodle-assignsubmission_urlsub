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
 * Upgrade code for install
 *
 * @package    assignsubmission_urlsub
 * @author     Paul Vincent
 * @copyright  2021 {@link https://spaces.oca.ac.uk/telteam}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Stub for upgrade code
 * @param int $oldversion
 * @return bool
 */
function xmldb_assignsubmission_urlsub_upgrade($oldversion) {

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion <2021071500) {
    
        $table = new xmldb_table('assignsubmission_urlsub');
             $field = new xmldb_field('urlsubtitle', XMLDB_TYPE_TEXT, 64, null, null, null, null);

        upgrade_mod_savepoint(true, 2021071501, 'assignsubmission_urlsub');
 }
    return true;
}


