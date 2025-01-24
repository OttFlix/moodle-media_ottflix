<?php
/**
 * User: Eduardo Kraus
 * Date: 18/01/2024
 * Time: 21:44
 */

namespace media_ottflix;

class ottflix_media_video {
    /**
     * Call for list videos in ottflix.
     *
     * @param int $page
     * @param int $pasta
     * @param string $titulo
     *
     * @return array
     * @throws dml_exception
     */
    public static function listing($page, $pasta, $titulo) {
        $post = array(
            "page" => $page,
            "pastaid" => $pasta,
            "titulo" => $titulo
        );

        $baseurl = "api/v2/video";
        $json = self::load($baseurl, $post, "GET");

        return json_decode($json);
    }

    /**
     * Call for get player code.
     *
     * @param int $cmid
     * @param string $identifier
     * @param string $safetyplayer
     *
     * @return string
     * @throws dml_exception
     */
    public static function getplayer($cmid, $identifier, $safetyplayer) {
        global $USER, $OUTPUT;
        $config = get_config('ottflix');

        $payload = array(
            "identifier" => $identifier,
            "matricula" => $cmid,
            "nome" => fullname($USER),
            "email" => $USER->email,
            "safetyplayer" => $safetyplayer
        );

        require_once __DIR__ . "/jwt.php";
        $token = jwt::encode($config->token, $payload);

        return $OUTPUT->render_from_template('media_ottflix/player', [
            'identifier' => $identifier,
            'token' => $token
        ]);
    }

    /**
     * Curl execution.
     *
     * @param string $baseurl
     * @param array $query
     *
     * @param string $protocol
     * @return string
     * @throws dml_exception
     */
    private static function load($baseurl, $query = null, $protocol = "GET") {
        $config = get_config('ottflix');

        $ch = curl_init();

        $query = http_build_query($query, '', '&');

        if ($protocol == "POST") {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);

            $queryUrl = "";
        } else if ($query) {
            $queryUrl = "?{$query}";
        }

        curl_setopt($ch, CURLOPT_URL, "https://app.ottflix.com.br/{$baseurl}{$queryUrl}");

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $protocol);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "authorization:{$config->token}"
        ));

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}