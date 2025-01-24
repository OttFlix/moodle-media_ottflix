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
 * Main class for plugin 'media_ottflix'
 *
 * @package   media_ottflix
 * @copyright 2025 Eduardo Kraus  {@link http://ottflix.com.br}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Player that creates ottflix embedding.
 *
 * @package   media_ottflix
 * @copyright 2025 Eduardo Kraus  {@link http://ottflix.com.br}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class media_ottflix_plugin extends core_media_player_external {
    /**
     * List supported urls.
     *
     * @param array $urls
     * @param array $options
     *
     * @return array
     */
    public function list_supported_urls(array $urls, array $options = array()) {
        $result = array();
        foreach ($urls as $url) {
            // If OttFlix support is enabled, URL is supported.

            if (($url->get_host() === 'app.ottflix.com.br')) {
                $result[] = $url;
            } else if (($url->get_host() === 'player.ottflix.com.br')) {
                $result[] = $url;
            }
        }

        return $result;
    }

    /**
     * Embed external.
     *
     * @param moodle_url $url
     * @param string $name
     * @param int $width
     * @param int $height
     * @param array $options
     *
     * @return string
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function embed_external(moodle_url $url, $name, $width, $height, $options) {
        global $COURSE;

        preg_match('/\/\w+\/\w+\/([A-Z0-9\-\_]{3,255})/', $url->get_path(), $path);
        if (isset($path[0])) {
            $identifier = $path[1];
            return \mod_supervideo\ottflix\repository::getplayer($COURSE->id, $identifier);
        }

        preg_match('/\/\w+\/([A-Z0-9\-\_]{3,99})/', $url->get_path(), $path);
        if (isset($path[0])) {
            $identifier = $path[1];
            return \mod_supervideo\ottflix\repository::getplayer($COURSE->id, $identifier);
        }

        return null;
    }

    /**
     * Supports Text.
     *
     * @param array $usedextensions
     *
     * @return mixed|string
     * @throws coding_exception
     */
    public function supports($usedextensions = []) {
        return get_string('support_ottflix', 'media_ottflix');
    }

    /**
     * Get embeddable markers.
     *
     * @return array
     */
    public function get_embeddable_markers() {
        $markers = [
            'app.ottflix.com.br',
            'player.ottflix.com.br'
        ];

        return $markers;
    }


    /**
     * Default rank
     *
     * @return int
     */
    public function get_rank() {
        return 2001;
    }

    /**
     * Checks if player is enabled.
     *
     * @return bool True if player is enabled
     */
    public function is_enabled() {
        return \mod_supervideo\ottflix\repository::is_enable();
    }
}
