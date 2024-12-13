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
 * TODO describe file index
 *
 * @package    local_logsnapshot
 * @copyright  2024 Stephan Lorbek <stephan.lorbek@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_logsnapshot;

require('../../config.php');

require_once('classes/query.php');
require_once('classes/filehandler.php');
require_once($CFG->libdir . '/formslib.php');
require_once('classes/form/data_exporter_sftp_form.php');
require_login();

$url = new \moodle_url('/local/logsnapshot/index.php', []);
$PAGE->set_url($url);
$PAGE->set_context(\context_system::instance());

$PAGE->set_heading($SITE->fullname);
$mform = new local_logsnapshot_data_exporter_sftp_form();

echo $OUTPUT->header();

if ($mform->is_cancelled()) {
    redirect('/', "", 10);
} else if ($formdata = $mform->get_data()) {
    $query = query::get_instance();
    $file = $query->export_to_csv('logstore_standard_log');

    $tempzip = (new filehandler)->archive($file, $formdata);
    (new transfer)->transfer_file_with_sftp($formdata->hostname, $formdata->username, $formdata->password, $tempzip,
    "export.zip", $formdata->port);

    if (!empty($tempzip)) {
        unlink($tempzip);
    }
} else {
    $mform->display();
}

echo $OUTPUT->footer();
