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

use moodle\Util\Logger;
use moodle_local_directory_helper;
use core\output\download;
use core\text;
use core\output\html;
use core\output\pdf;

use local_logsnapshot\transfer;

require_once('classes/transfer.php');

/**
 * Class query
 *
 * @package    local_logsnapshot
 * @copyright  2024 Stephan Lorbek <stephan.lorbek@uni-graz.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query {
    /**
     * The single instance of the class.
     *
     * @var query
     */
    private static $instance;

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct() {
    }

    /**
     * Returns the single instance of the class.
     *
     * @return query
     */
    public static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new query();
        }

        return self::$instance;
    }

    /**
     * Queries the database and exports the result into a CSV file.
     *
     * @param string $table The name of the table to query.
     * @param array $fields The fields to include in the query.
     * @param string $password The password to use for compression.
     * @return object
     */
    public function export_to_csv($table): object {
        global $DB, $USER;
        $records = $DB->get_records($table);
        $context = \context_system::instance();

        $fs = get_file_storage();
        $filename = 'logstorage.csv';
        $filearea = 'pluginfiles';
        $component = 'local_logsnapshot';
        $itemid = 0;
        $metadata = [
            'contextid' => $context->id,
            'component' => $component,
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => '/',
            'filename'  => $filename,
            'userid'    => $USER->id,
        ];

        $content = "";
        $header = true;
        foreach ($records as $record) {
            $recordarray = (array)$record;

            if ($header) {
                $content .= implode(',', array_keys($recordarray)) . "\n";
                $header = false;
            }
            $content .= implode(',', array_map('addslashes', $recordarray)) . "\n";
        }

        try {
            if ($fs->file_exists($context->id, $component, $filearea, $itemid, '/', $filename)) {
                $file = $fs->get_file($context->id, $component, $filearea, $itemid, '/', $filename);
                $file->delete();
            }
            $file = $fs->create_file_from_string($metadata, $content);
            echo "File created successfully!";
        } catch (\Exception $e) {
            echo "Error creating file: " . $e->getMessage();
        }

        $file = $fs->get_file($context->id, $component, $filearea, $itemid, '/', $filename);
        return $file;
    }
}
