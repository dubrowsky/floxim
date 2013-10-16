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
      if (is_numeric($page)) {
        $page = fx::data('content_page', $page);
      }
      $this->current['page'] = $page;
  }

  public function get_page ( ) {
      return $this->current['page'];
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
  
  public function get_home_id() {
      if (!isset($this->current['home_id'])) {
        $site = $this->get_site();
        $home_page = fx::data('content_page')
            ->where('parent_id', 0)
            ->where('site_id', $site['id'])
            ->one();
        $this->current['home_id'] = $home_page['id'];
      }
      return $this->current['home_id'];
  }
  
  public function is_admin() {
      return ($user = $this->get_user()) ? $user->is_admin() : false;
  }
  
  public function get_layout() {
        if (!$this->current['layout']) {
            $page_id = $this->get_page()->get('id');
            if ($page_id) {
                $page = fx::data('content_page', $page_id);
                if ($page['layout_id']) {
                    $this->current['layout'] = $page['layout_id'];
                }
            }
            if (!$this->current['layout']) {
                $this->current['layout'] = $this->get_site()->get('layout_id');
            }
            if (!$this->current['layout']) {
                $this->current['layout'] = fx::data('layout')->one()->get('id');
            }
        }
        return $this->current['layout'];
    }
}

?>