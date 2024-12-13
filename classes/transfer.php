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

require_once('vendor/autoload.php');

use phpseclib3\Net\SFTP;
use moodle_exception;

require_login();


/**
 * Class transfer
 *
 * @package    local_logsnapshot
 * @copyright  2024 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class transfer {
    public static function transfer_file_with_sftp($host, $username, $password, $localfile, $remotefile, $port = 22) {
        $sftp = new SFTP($host, $port);
        if (!$sftp->login($username, $password)) {
            throw new moodle_exception('Login failed');
        }

        if (!$sftp->put($remotefile, $localfile, SFTP::SOURCE_LOCAL_FILE)) {
            throw new moodle_exception('File transfer failed');
        }

        return true;
    }

}
