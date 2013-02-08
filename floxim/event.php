<?php

abstract class fx_event {

    protected $data;
    protected $message = '';
    protected $stopped;

    public function __construct($data) {

        $this->data = $data;
        $this->stopped = false;
        $this->message = '';
    }

    public function get_data() {
        return $this->data;
    }

    public function stop($message=null) {
        $this->message = $message;
        $this->stopped = true;
    }

    public function is_stopped() {
        return $this->stopped;
    }

    public function get_message() {
        return $this->message ? $this->message : null;
    }

}

?>
