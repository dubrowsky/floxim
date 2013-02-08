<?php

defined("FLOXIM") || die("Unable to load file.");

class fx_event_message extends fx_event {

    protected $class_id;

    public function __construct($class_id, $data) {

        parent::__construct($data);
        $this->class_id = intval($class_id);
    }

    public function get_class_id() {
        return $this->class_id;
    }

}

?>
