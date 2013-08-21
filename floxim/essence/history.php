<?php
/*$Id: history.php 8536 2012-12-17 10:55:01Z myasin $*/
defined("FLOXIM") || die("Unable to load file.");

class fx_history extends fx_essence {

    protected static $_history;

    static public function set_history_obj(fx_history $history) {
        self::$_history = $history;
    }

    static public function delete_old() {
        $history = fx::data('history')->where('marker', 1)->all();
        foreach ($history as $v) {
            $v->delete(true);
        }
    }

    static public function add_operation($action, $essence, $essence_id, $prestate = '', $poststate = '') {
        if (!is_object(self::$_history))
            return false;

        $id = self::$_history->get_id();
        $history_item = new fx_data_history_item( );

        $item = $history_item->create(array('history_id' => $id));
        $item['action'] = $action;
        $item['prestate'] = $prestate;
        $item['poststate'] = $poststate;
        $item['essence'] = $essence;
        $item['essence_id'] = $essence_id;

        $item->save(true);
    }

    public function undo() {
        $this->_apply_undo_redo('undo');
        return $this;
    }

    public function redo() {
        $this->_apply_undo_redo('redo');
        return $this;
    }

    protected function _apply_undo_redo($action) {
        $history_item = new fx_data_history_item( );

        $items = $history_item->get_all('history_id', $this->get_id());

        foreach ($items as $item) {
            $item->make($action);
        }
    }

}

