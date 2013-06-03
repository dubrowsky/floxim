<?php

class fx_admin_configjs {
  protected $options;

  public function  __construct() {
    $this->options['login'] = 'admin';
    $this->options['action_link'] = fx::config()->HTTP_ACTION_LINK;

    $this->options['history'] = array();
    $undo = fx_controller_admin_history::get_undo_obj();
    $redo = fx_controller_admin_history::get_redo_obj();
    if ( $undo ) $this->options['history']['undo'] = $undo['name'];
    if ( $redo ) $this->options['history']['redo'] = $redo['name'];
  }

  public function get_config() {
    return json_encode($this->options);
  }

  public function add_menu ( $structure ) {
    $this->options['menu'] = $structure;
  }

  public function add_main_menu ( $structure ) {
    $this->options['mainmenu'] = $structure;
  }

  public function add_more_menu ( $structure ) {
    $this->options['more_menu'] = $structure;
  }

  public function add_buttons ( $buttons ) {
    $this->options['buttons'] = $buttons;
  }

  public function add_additional_text ( $text ) {
      $this->options['additional_text'] = $text;
  }

  // Additional admin panel
  // e.g. layout preview navigation
  public function add_additional_panel ( $data ) {
      $this->options['additional_panel'] = $data;
  }
}

