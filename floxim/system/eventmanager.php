<?php

/* $Id: nc_event.class.php 4325 2011-03-11 14:55:58Z evgen $ */

class fx_system_eventmanager extends fx_system {

    private $_binded_obj, $_events_arr;
    private $_events_name;

    public function __construct() {
        // load parent constructor
        parent::__construct();

        // collect objects for events
        $this->_binded_obj = array();

        // allowed events
        $this->_events_arr = array(
                // pre-add actions
                "pre_add_site", // TODO
                "pre_add_subdivision",
                "pre_add_infoblock",
                "pre_add_class", // TODO
                "pre_add_class_template", // TODO
                "pre_add_message",
                "pre_add_system_table", // TODO
                "pre_add_template", // TODO
                "pre_add_user",
                "pre_add_comment", // TODO
                "pre_add_widget_class", // TODO
                "pre_add_widget", // TODO
                // post-add actions
                "add_site", // TODO
                "add_subdivision",
                "add_infoblock",
                "add_class", // TODO
                "add_class_template", // TODO
                "add_message",
                "add_system_table", // TODO
                "add_template", // TODO
                "add_user",
                "add_comment", // TODO
                "add_widget_class", // TODO
                "add_widget", // TODO
                // pre-update actions
                "pre_update_site", // done
                "pre_update_subdivision",
                "pre_update_infoblock",
                "pre_update_class", // TODO
                "pre_update_class_template", // TODO
                "pre_update_message",
                "pre_update_system_table", // TODO
                "pre_update_template", // TODO
                "pre_update_user", // TODO
                "pre_update_comment", // TODO
                "pre_update_widget_class", // TODO
                "pre_update_widget", // TODO
                // post-update actions
                "update_site",
                "update_subdivision",
                "update_infoblock",
                "update_class", // TODO
                "update_class_template", // TODO
                "update_message",
                "update_system_table", // TODO
                "update_template", // TODO
                "update_user", // TODO
                "update_comment", // TODO
                "update_widget_class", // TODO
                "update_widget", // TODO
                // pre-drop actions
                "pre_drop_site", // TODO
                "pre_drop_subdivision",
                "pre_drop_infoblock",
                "pre_drop_class", // TODO
                "pre_drop_class_template", // TODO
                "pre_drop_message",
                "pre_drop_system_table", // TODO
                "pre_drop_template", // TODO
                "pre_drop_user", // TODO
                "pre_drop_comment", // TODO
                "pre_drop_widget_class", // TODO
                "pre_drop_widget", // TODO
                // post-drop actions
                "drop_site", // TODO
                "drop_subdivision",
                "drop_infoblock",
                "drop_class", // TODO
                "drop_class_template", // TODO
                "drop_message",
                "drop_system_table", // TODO
                "drop_template", // TODO
                "drop_user", // TODO
                "drop_comment", // TODO
                "drop_widget_class", // TODO
                "drop_widget", // TODO
                // pre-check actions
                "pre_check_site", // TODO
                "pre_check_subdivision",
                "pre_check_infoblock",
                "pre_check_message",
                "pre_check_user", // TODO
                "pre_check_comment", // TODO
                "pre_check_module", // TODO
                // post-check actions
                "check_site", // TODO
                "check_subdivision",
                "check_infoblock",
                "check_message",
                "check_user", // TODO
                "check_comment", // TODO
                "check_module", // TODO
                // pre-uncheck actions
                "pre_uncheck_site", // TODO
                "pre_uncheck_subdivision",
                "pre_uncheck_infoblock",
                "pre_uncheck_message",
                "pre_uncheck_user", // TODO
                "pre_uncheck_comment", // TODO
                "pre_uncheck_module", // TODO
                // post-uncheck actions
                "uncheck_site", // TODO
                "uncheck_subdivision",
                "uncheck_infoblock",
                "uncheck_message",
                "uncheck_user", // TODO
                "uncheck_comment", // TODO
                "uncheck_module", // TODO
                // other
                "pre_authorize_user", // TODO
                "authorize_user", // TODO
        );

        // имена пользовательских событий
        $this->_events_name = array();
    }

    /**
     * Add object to the listen mode
     *
     * @param object examine object
     */
    public function bind(&$object, $event_data) {
        // validate
        if (!(
                is_string($event_data) ||
                is_array($event_data)
                )) return false;

        // remap array
        $events_remap_arr = array();

        // имя метода совпадает с именем события
        if (is_string($event_data)) {
            $event_name = $event_data;
        } else {
            // get parameters
            list($event_name, $event_remap_name) = each($event_data);
            // для одного метода названачены несколько событий ( перечислены через запятую )
            if (strpos($event_name, ',') && ($events = explode(',', $event_name))) {
                foreach ($events as $v)
                    $this->bind($object, array($v => $event_remap_name));
                return true;
            }

            // remap array
            $events_remap_arr = $event_data;
        }

        // already binded
        if (isset($this->_binded_obj[$event_name]) && in_array($object, $this->_binded_obj[$event_name])) {
            return true;
        }

        // bind object with remap array
        $this->_binded_obj[$event_name][] = array('object' => $object, 'remap' => $events_remap_arr);
        //echo get_class($object)." - ".print_r($events_remap_arr, 1)."<br/>";

        return true;
    }

    /**
     * Event processor
     * call objects function for current event
     *
     */
    public function execute($event, $event_class) {

        if (!$event || !$event_class) {
            return false;
        }

        // check binded array
        if (empty($this->_binded_obj[$event])) {
            return false;
        }

        foreach ($this->_binded_obj[$event] as $object) {
            // check remaping
            if (!empty($object['remap'])) {
                // remap event method
                $event_method = $object['remap'][$event] ? $object['remap'][$event] : "";
            } else {
                // default event name
                $event_method = $event;
            }
            // check and execute observer method
            if (is_callable(array($object['object'], $event_method))) {
                // execute event method
                call_user_func(array($object['object'], $event_method), $event_class);
            }
        }

        return;
    }

    /**
     * Get all events as array
     *
     * @return array events list
     */
    public function get_all_events() {
        // return result
        return $this->_events_arr;
    }

    /**
     * Check event by event name
     *
     * @return bool result
     */
    public function check_event($event) {
        // check base system event
        if (in_array($event, $this->_events_arr)) {
            return true;
        }

        // return result
        return false;
    }

    public function register_event($event, $name) {
        // не существует ли событие уже
        if ($this->check_event($event)) {
            return false;
        }

        if (!preg_match("/^[_a-z0-9]+$/i", $event) || !$name) {
            return false;
        }

        $this->_events_arr[] = $event;
        $this->_events_name[$event] = $name;
    }

}

?>