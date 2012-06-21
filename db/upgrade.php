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
 * Upgrade script
 *
 * @package    core
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function xmldb_plagiarism_programming_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    /// Add a new column newcol to the mdl_myqtype_options
    if ($oldversion < 2012062001) {

        // Define field token to be added to programming_jplag
        $table = new xmldb_table('programming_jplag');
        $field = new xmldb_field('token', XMLDB_TYPE_CHAR, '32', null, null, null, null, 'progress');

        // Conditionally launch add field token
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field token to be added to programming_moss
        $table = new xmldb_table('programming_moss');
        $field = new xmldb_field('token', XMLDB_TYPE_CHAR, '32', null, null, null, null, 'progress');

        // Conditionally launch add field token
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Changing type of field message on table programming_jplag to text
        $table = new xmldb_table('programming_jplag');
        $field = new xmldb_field('message', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'directory');

        // Launch change of type for field message
        $dbman->change_field_type($table, $field);

        $table = new xmldb_table('programming_jplag');
        $field = new xmldb_field('error_detail', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'token');

        // Conditionally launch add field error_detail
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field error_detail to be added to programming_moss
        $table = new xmldb_table('programming_moss');
        $field = new xmldb_field('error_detail', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'token');

        // Conditionally launch add field error_detail
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('programming_plagiarism');
        $field = new xmldb_field('scandate');

        // Conditionally launch drop field scandate
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $table = new xmldb_table('programming_plagiarism');
        $field = new xmldb_field('latestscan', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'starttime');

        // Conditionally launch add field latestscan
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('programming_plagiarism');
        $field = new xmldb_field('notification_text', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'latestscan');

        // Conditionally launch add field notification_text
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('programming_scan_date');

        // Adding fields to table programming_scan_date
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('scan_date', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('finished', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('settingid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table programming_scan_date
        $table->add_key('date_primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for programming_scan_date
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define index cmid_idx (not unique) to be dropped form programming_result
        $table = new xmldb_table('programming_result');
        $index = new xmldb_index('cmid_idx', XMLDB_INDEX_NOTUNIQUE, array('cmid'));

        // Conditionally launch drop index cmid_idx
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Define field marked to be dropped from programming_result
        $table = new xmldb_table('programming_result');
        $field = new xmldb_field('marked');

        // Conditionally launch drop field marked
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Define field detector to be dropped from programming_result
        $table = new xmldb_table('programming_result');
        $field = new xmldb_field('detector');

        // Conditionally launch drop field detector
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Rename field cmid on table programming_result to NEWNAMEGOESHERE
        $table = new xmldb_table('programming_result');
        $field = new xmldb_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'comments');

        // Launch rename field cmid
        $dbman->rename_field($table, $field, 'reportid');

         // Define table programming_report to be created
        $table = new xmldb_table('programming_report');

        // Adding fields to table programming_report
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('time_created', XMLDB_TYPE_INTEGER, '15', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('version', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('detector', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

        // Adding keys to table programming_report
        $table->add_key('report_primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table programming_report
        $table->add_index('cmid_index', XMLDB_INDEX_NOTUNIQUE, array('cmid'));

        // Conditionally launch create table for programming_report
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Rename field courseid on table programming_plagiarism to NEWNAMEGOESHERE
        $table = new xmldb_table('programming_plagiarism');
        $field = new xmldb_field('courseid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, 'id');

        // Launch rename field courseid
        $dbman->rename_field($table, $field, 'cmid');

        // programming savepoint reached
        upgrade_plugin_savepoint(true, 2012062001, 'plagiarism', 'programming');

    }

    return true;
}
