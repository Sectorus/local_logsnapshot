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

require_once('../../lib/filelib.php');

class filehandler {
    /**
     * Archives a file and optionally encrypts it with a password.
     *
     * @param object $file        The file object to be archived.
     * @param object $formdata    The form data containing export settings.
     * @throws Exception          If ZIP file creation or encryption fails.
     * @return void
     */
    public static function archive($file, $formdata) {
        global $CFG;
        $tempzip = tempnam($CFG->tempdir . '/', 'export_');
        $zipper = new \zip_packer();
        $zipper->archive_to_pathname([], $tempzip);

        $contenthash = $file->get_contenthash();
        $localpath = $CFG->dataroot . '/filedir/' . substr($contenthash, 0, 2) . '/' . substr($contenthash, 2, 2) . '/'
            . $contenthash;

        $zip = new \ZipArchive();

        if ($zip->open($tempzip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \Exception("Cannot create ZIP file.");
        }
        if (!empty($formdata->packpassword) && !$zip->setPassword($formdata->packpassword)) {
            throw new \Exception("Failed to set password for ZIP file.");
        }
        if (!$zip->addFile($localpath, $formdata->exportname)) {
            throw new \Exception("Failed to add file: $formdata->exportname");
        }
        if (!$zip->setEncryptionName($formdata->exportname, \ZipArchive::EM_AES_256)) {
            throw new \Exception("Failed to encrypt file: $formdata->exportname");
        }
        $zip->close();
        $file->delete();
        return $tempzip;
    }
}