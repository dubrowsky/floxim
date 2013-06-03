<?php

/* $Id: classificator.php 6965 2012-05-12 15:18:46Z denis $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_classificator extends fx_essence {

    protected $table;

    public function __construct($input = array()) {
        parent::__construct($input);
        $this->table = $input['table'];
    }
    
    /**
     *
     * @return fx_data_classificator_item 
     */
    public function get_item_finder(){
        $element_finder = new fx_data_classificator_item();
        $element_finder->set_table($this['table']);
        
        return $element_finder;
    }

    public function elements($output = 'object') {
        static $element_finder = false;
        if (!$element_finder) {
            $element_finder = new fx_data_classificator_item();
        }

        $elements = array();
        foreach ($element_finder->set_table($this->table ? $this->table : $this['table'])->get_all() as $el) {
            $elements [$el['id']] = $output == 'object' ? $el : $el['name'];
        }

        return $elements;
    }

    protected function _after_update() {
        $old_table = $this->modified_data['table'];
        $new_table = $this->data['table'];
        if ( $old_table != $new_table ) {
            $sql = "RENAME TABLE `{{classificator_".$old_table."}}` TO `{{classificator_".$new_table."}}`";
            fx_core::get_object()->db->query($sql);
            
        }
        
        return false;
    }
    
    protected function _after_insert() {
        $sql = "
            CREATE TABLE IF NOT EXISTS `{{classificator_".$this['table']."}}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `priority` int(11) DEFAULT NULL,
            `value` text,
            `checked` int(1) DEFAULT '1',
            PRIMARY KEY (`id`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
        fx_core::get_object()->db->query($sql);

        return false;
    }
    
     protected function _after_delete () {
        $sql = "DROP TABLE `{{classificator_".$this['table']."}}`";
        fx_core::get_object()->db->query($sql);
        
        return false;
    }

}

class fx_classificator_item extends fx_essence {
    
}

