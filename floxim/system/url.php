<?php

/* $Id: nc_url.class.php 4483 2011-04-13 15:41:35Z denis $ */

class fx_system_url extends fx_system {

    private $_parsed_url;

    public function build_url($query_arr) {
        if (!empty($query_arr)) {
            return http_build_query($query_arr, "", "&");
        }
        return false;
    }

    /**
     * parse REQUEST_URI
     */
    public function parse_url() {
        $fx_core = fx_core::get_object();
        // надо сохранить get-параметры из окружения
        $uri = urldecode($fx_core->REQUEST_URI);
        $get_env = '';
        if (($start = strpos(getenv("REQUEST_URI"), '?')) !== false)
                $get_env = '&'.substr(urldecode(getenv("REQUEST_URI")), $start + 1);


        $url = "http".( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "s" : "")."://".getenv("HTTP_HOST").$uri;

        $parsed_url = @parse_url($url);
        $parsestr = (isset($parsed_url['query']) ? $parsed_url['query'] : $get_env);

        if ($parsestr) {
            parse_str($parsestr, $parsed_query_arr);
            // in error_document $_GET is empty, so set them at this line
            $_GET = $parsed_query_arr ? $parsed_query_arr : array();
            // build new query
            $parsed_url['query'] = $this->build_url($parsed_query_arr);
        }

        $this->_parsed_url = $parsed_url;

        return $this->_parsed_url;
    }

    public function get_parsed_url($item = "") {

        if (empty($this->_parsed_url)) return false;

        if ($item) {
            return array_key_exists($item, $this->_parsed_url) ? $this->_parsed_url[$item] : "";
        } else {
            return $this->_parsed_url;
        }
    }

}

?>