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

namespace local_logsnapshot;

/**
 * TODO describe file index
 *
 * @package    local_logsnapshot
 * @copyright  2024 Stephan Lorbek <stephan.lorbek@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_logsnapshot_data_exporter_sftp_form extends \moodleform {

    /**
     * {@inheritdoc}
     * @see moodleform::definition()
     *
     * The form is used to configure the SFTP data exporter.
     * The form is structured as follows:
     * 1. The hostname of the SFTP server.
     * 2. The username of the SFTP user.
     * 3. The password of the SFTP user.
     * 4. The port of the SFTP server. The default is 22.
     * 5. A submit button to transfer the data.
     */

    public function definition() {
        global $USER;
        $mform = $this->_form;

        $mform->addElement('text', 'hostname', get_string('host', 'local_logsnapshot'));
        $mform->setType('hostname', PARAM_TEXT);

        $mform->addElement('text', 'username', get_string('username', 'local_logsnapshot'),
        ['value' => "$USER->username@domain"]);
        $mform->setType('username', PARAM_TEXT);

        $mform->addElement('password', 'password', get_string('password', 'local_logsnapshot'));
        $mform->setType('password', PARAM_RAW);

        $mform->addElement('text', 'port', get_string('port', 'local_logsnapshot'), ['value' => 22]);
        $mform->setType('port', PARAM_INT);

        $mform->addElement('text', 'exportname', get_string('exportname', 'local_logsnapshot'), ['value' => 'export.csv']);
        $mform->setType('exportname', PARAM_RAW);

        $mform->addElement('checkbox', 'pack', get_string('pack', 'local_logsnapshot'));
        $mform->setType('pack', PARAM_BOOL);

        $mform->addElement('password', 'packpassword', get_string('packpassword', 'local_logsnapshot'));
        $mform->setType('packpassword', PARAM_RAW);

        $mform->addElement('submit', 'submit', get_string('transfer', 'local_logsnapshot'));
    }
}
