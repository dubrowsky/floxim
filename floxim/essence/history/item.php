<?php
/*$Id: item.php 6965 2012-05-12 15:18:46Z denis $*/
defined("FLOXIM") || die("Unable to load file.");

class fx_history_item extends fx_essence {
    protected $_map = array('add' => 'delete', 'update' => 'update', 'delete' => 'add');
    protected $_type = array('undo' => 'prestate', 'redo' => 'poststate');
    
    public function make ( $type ) { 
        $method = ($type == 'undo') ? $this->_map[ $this['action'] ] : $this['action']; 
        call_user_func(array($this, '_action_'.$method),$this->_type[$type] );
    }

    protected function _action_add($state) {
        $essence = $this['essence'];
        if ($essence == 'message') {
            $item = fx_core::get_object()->$essence->create($this['essence_id'][0], $this[$state]);
        } else {
            $item = fx_core::get_object()->$essence->create($this[$state]);
        }

        $item->save(true, 'add');
    }
    
    protected function _action_update ( $state ) {
        $essence = $this['essence'];

        $item = fx_core::get_object()->$essence->get_by_id($this['essence_id']);

        foreach ($this[$state] as $k => $v) {
            $item[$k] = $v;
        }

        $item->save(true);
    }
    
    protected function _action_delete () {
        $essence = $this['essence'];
        $item = fx_core::get_object()->$essence->get_by_id($this['essence_id'])->delete(true);
    }
}
