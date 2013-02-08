<?php
/* $Id: crontask.php 8096 2012-09-05 13:24:14Z alive $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_crontask extends fx_essence {
    const SEND_TYPE_NONE = 0, SEND_TYPE_RESULT = 1, SEND_TYPE_ALWAYS = 2;
    protected $max_block_time = 86400;
    protected $block_path;

    public function __construct($input = array()) {
        parent::__construct($input);
        $this->block_path = fx::config()->HTTP_FILES_PATH.'cron/'.$this['id'].'.block';
    }

    public function run() {
        if ( $this->is_blocked() ) {
            return false;
        }
        $this->set_block();
        if ( strpos($this['path'], 'http:') !== false ) {
            $result = $this->exec_url();
        }
        else {
            $result = $this->exec_file();
        }
        $this->unset_block();
        $this->update_launch_time();
        $this->attempt_to_send($result);
    }

    protected function exec_url () {
        return fx_core::get_object()->util->http_request($this['path']);
    }

    protected function exec_file () {
        $fx_core = fx_core::get_object();
        ob_start();
        $fx_core->files->file_include($this['path']);
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }

    protected function update_launch_time(){
        $this->set('last_launch', time())->save();
    }

    protected function is_blocked() {
        $fx_core = fx_core::get_object();

        try {
            $block = $fx_core->files->readfile($this->block_path);
        } catch (Exception $e) {
            return false;
        }

        return  (intval($block) + $this->max_block_time) > time();
    }

    protected function set_block () {
        $fx_core = fx_core::get_object();
        try {
            $fx_core->files->writefile($this->block_path, time());
        } catch (Exception $e) {
            return false;
        }
    }

     protected function unset_block () {
        $fx_core = fx_core::get_object();
        try {
            $fx_core->files->rm($this->block_path);
        } catch (Exception $e) {
            return false;
        }
    }

    protected function attempt_to_send ( $result ) {
        $fx_core = fx_core::get_object();

        $result = trim($result, " \t\r\n");
        if ( $this['send_email_type'] == self::SEND_TYPE_NONE || ($this['send_email_type'] == self::SEND_TYPE_RESULT && !$result) ) {
            return false;
        }

        $to = $this['email'] ? $this['email'] : $fx_core->get_settings('spam_from_email');
        $subject = "Cron: выполнена задача ".$this['name'];
        $body = "Дорогой администратор! \n Задача по расписанию \"".$this['name']."\" выполнена.\n";
        if ( $result ) {
            $body .= "Вывод:\n".$result;
        }

        $fx_core->mail->set_body($body);
        $fx_core->mail->send($to, $subject);
    }

    public function validate() {
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Укажите название');
            $res = false;
        }

        if (!$this['path']) {
            $this->validate_errors[] = array('field' => 'path', 'text' => 'Укажите путь');
            $res = false;
        }

        return $res;
    }

}
