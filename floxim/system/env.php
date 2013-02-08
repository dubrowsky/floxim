<?php

class fx_system_env extends fx_system {
  protected $current = array();


  public function set_site ( $env ) {
    $this->current['site'] = $env;
  }

  public function set_sub ( $env ) {
    $this->current['sub'] = $env;
  }

  public function set_ibs ( $env ) {
    $this->current['ibs'] = $env;
  }

  public function set_content ( $env ) {
    $this->current['content'] = $env;
  }

  public function set_template ( $template ) {
    $this->current['template'] = $template;
  }

  public function get_sub ($item=null) {
    return $item ? $this->current['sub'][$item] : $this->current['sub'];
  }

  public function get_content ($item=null) {
    return $item ? $this->current['content'][$item] : $this->current['content'];
  }

  public function get_ibs ($item=null) {
    return $item ? $this->current['ibs'][$item] : $this->current['ibs'];
  }

  public function get_template ($item=null) {
  	  if (!isset($this->current['template']) || !$this->current['template'] || !is_numeric($this->current['template'])) {
  	  	  $first_template = current(fx_core::get_object()->template->get_all());
  	  	  $this->current['template'] = $first_template['id'];
  	  	  /*
        	if ($template_id != $first_template['id']) {
        		return $this->load_tpl($first_template['id']);
        	}
		  */
  	  }
    return $item ? $this->current['template'][$item] : $this->current['template'];
  }

  /**
   * @return fx_site
   */
  public function get_site ($item=null) {
    return $item ? $this->current['site'][$item] : $this->current['site'];
  }

  public function set_action ( $action ) {
      $this->current['action'] = $action;
  }

  public function get_action ( ) {
      return $this->current['action'];
  }

  public function set_page ( $page ) {
      $this->current['page'] = $page;
  }

  public function get_page ( ) {
      return (int)$this->current['page'];
  }

  public function set_tpl ( $tpl ) {
      $this->current['tpl'] = $tpl;
  }

  public function get_tpl ($item=null) {
      return $item ? $this->current['tpl'][$item] : $this->current['tpl'];
  }

  public function set_user ( $user ) {
      $this->current['user'] = $user;
  }

  public function get_user ($item=null) {
      return $item ? $this->current['user'][$item] : $this->current['user'];
  }

  public function set_main_content ( $str ) {
      $this->current['main_content'] = $str;
  }

  public function get_main_content () {
      return $this->current['main_content'];
  }
  
  public function is_admin() {
      $user = $this->get_user();
      if (!$user) {
          return false;
      }
      return $user->perm()->is_supervisor();
  }
}

?>