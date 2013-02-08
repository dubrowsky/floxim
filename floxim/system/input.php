<?php
/* $Id: nc_input.class.php 4755 2011-06-01 13:45:23Z denis $ */


class fx_system_input extends fx_system {


  public function __construct () {
    // load parent constructor
    parent::__construct();

    $this->prepare_extract ();

  }


  public function prepare_extract () {
    // system superior object
    $fx_core = fx_core::get_object();

    $fx_core->REQUEST_URI = isset($_GET['REQUEST_URI']) ? $_GET['REQUEST_URI'] : (
                            isset($_POST['REQUEST_URI']) ? $_POST['REQUEST_URI'] : (
                            isset($_ENV['REQUEST_URI']) ? $_ENV['REQUEST_URI'] :
                            getenv("REQUEST_URI")));
    if ( substr($fx_core->REQUEST_URI, 0, 1) != "/" ) $fx_core->REQUEST_URI="/".$fx_core->REQUEST_URI;
    $fx_core->REQUEST_URI = trim($fx_core->REQUEST_URI);
    $url = "http".( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "s":"")."://".getenv("HTTP_HOST").$fx_core->REQUEST_URI;
    // parse entire url
    $parsed_url = @parse_url($url);

    // validate query parameter
    if ( is_array($parsed_url) && array_key_exists('query', $parsed_url) && $parsed_url['query']) {
      parse_str($parsed_url['query'], $parsed_query_arr);
      $_GET = $parsed_query_arr ? $parsed_query_arr : array();
    }


    // superglobal arrays
    $superglobals = array("_COOKIE" => $_COOKIE, "_GET" => $_GET, "_POST" => $_POST, "_FILES" => $_FILES, "_ENV" => $_ENV, "_SERVER" => $_SERVER);
    // set default

    // merge superglobals arrays
    foreach ($superglobals as $key => $super_array) {
      // set internal data from superglobal arrays
      $this->$key = $this->prepare_superglobals($super_array);
    }

    return false;
  }

  public function recursive_add_slashes ($input) {
    if ( !is_array($input) ) {
      return addslashes($input);
    }
    $output = array();

    foreach ($input as $k => $v) {
       $output[$k] = $this->recursive_add_slashes($v);
    }

    return $output;
  }


  public function prepare_superglobals ( $array ) {
    if ( !get_magic_quotes_gpc() ) return $array;
    return $this->recursive_stripcslashes($array);
  }

  public function recursive_stripcslashes ($input) {
    if ( !is_array($input) ) {
      return stripcslashes($input);
    }
    $output = array();

    foreach ($input as $k => $v) {
       $output[$k] = $this->recursive_stripcslashes($v);
    }

    return $output;
  }

  public function fetch_get ($item = "") {

    if ( empty($this->_GET) ) return array();

    if ( $item ) {
      return array_key_exists($item, $this->_GET) ? $this->_GET[$item] : null;
    }
    else {
      return $this->_GET;
    }

  }

  public function fetch_post ($item = "") {

    if ( empty($this->_POST) ) return array();

    if ( $item ) {
      return array_key_exists($item, $this->_POST) ? $this->_POST[$item] : null;
    }
    else {
      return $this->_POST;
    }

  }

  public function fetch_cookie ($item = "") {

    if ( empty($this->_COOKIE) ) return array();

    if ( $item ) {
      return array_key_exists($item, $this->_COOKIE) ? $this->_COOKIE[$item] : null;
    }
    else {
      return $this->_COOKIE;
    }

  }

  public function fetch_session ($item = "") {

    if ( empty($this->_SESSION) ) return array();

    if ( $item ) {
      return array_key_exists($item, $this->_SESSION) ? $this->_SESSION[$item] : null;
    }
    else {
      return $this->_SESSION;
    }

  }

  public function fetch_files ($item = "") {

    if ( empty($this->_FILES) ) return array();

    if ( $item ) {
      return array_key_exists($item, $this->_FILES) ? $this->_FILES[$item] : null;
    }
    else {
      return $this->_FILES;
    }

  }

  public function fetch_get_post ($item = "") {

    if ( empty($this->_GET) && empty($this->_POST) ) return array();

    if ( $item ) {
      return array_key_exists($item, $this->_GET) ? $this->_GET[$item] : (array_key_exists($item, $this->_POST) ? $this->_POST[$item] : null);
    }
    else {
      return array_merge($this->_POST, $this->_GET);
    }

  }

  public function get_service_session ( $item ) {
      $key = fx::config()->SESSION_KEY;
      $data = $_SESSION[$key];
      return $data[$item];
  }

  public function set_service_session ( $item, $value ) {
      $key = fx::config()->SESSION_KEY;
	  $data = isset($_SESSION[$key]) ? $_SESSION[$key] : array();
	  $data[$item] = $value;
      $_SESSION[$key] = $data;
  }

  public function unset_service_session ( $item ) {
      $key = fx::config()->SESSION_KEY;
      $data = $_SESSION[$key];
      unset($data[$item]);
      $_SESSION[$key] = $data;
  }

  public function GET($item = "") {
    $fx_core = fx_core::get_object();

    if (empty($this->_GET))
        return array();

    if ($item) {
        return array_key_exists($item, $this->_GET) ? $fx_core->db->escape($this->_GET[$item]) : null;
    } else {
        $get = $this->_GET;
        foreach ($get as $k => &$v) {
            $v = $fx_core->db->escape($v);
        }
        return $get;
    }
  }

  public function POST($item = "") {
    $fx_core = fx_core::get_object();

    if (empty($this->_POST))
        return array();

    if ($item) {
        return array_key_exists($item, $this->_POST) ? $fx_core->db->escape($this->_POST[$item]) : null;
    } else {
        $post = $this->_POST;
        foreach ($post as $k => &$v) {
            $v = $fx_core->db->escape($v);
        }
        return $post;
    }
  }

  public function GET_POST($item = "") {
    $fx_core = fx_core::get_object();

    if ( empty($this->_GET) && empty($this->_POST) )
            return array();

    if ($item) {
        return array_key_exists($item, $this->_GET) ? $fx_core->db->escape($this->_GET[$item]) : (array_key_exists($item, $this->_POST) ? $fx_core->db->escape($this->_POST[$item]) : null);
    } else {
        $data = array_merge($this->_POST, $this->_GET);
        foreach ($data as $k => &$v) {
            $v = $fx_core->db->escape($v);
        }
        return $data;
    }
  }


  public function make_input () {
      $files = $this->fetch_files();
      $post = $this->fetch_get_post();

      // массивы надо объединить, но ничего не потерять, array_merge не подходит
      // ex, POST['foto']['link'] = 'x', FILES['foto']['name'] = 'y' => input['foto']['link']='x',input['foto']['name']='y'
      /*@todo переделать по-нормальному*/
      $result = $post;
      if ( $files ) foreach ( $files as $k => $v ) {
          if ( isset($result[$k]) ) {
              if (is_array($v) ) {
                  foreach ( $v as $key => $value ) {
                      $result[$k][$key] = $value;
                  }
              }
              else {
                  $result[$k] = $v;
              }
          }
          else {
              $result[$k] = $v;
          }
      }

      return $result;
  }

}

?>
