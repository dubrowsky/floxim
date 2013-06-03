<?php

/* $Id: subdivision.php 8536 2012-12-17 10:55:01Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_subdivision extends fx_essence {

    protected $parent_sub_tree = array();
    protected $data_inherit = array();

    public function __construct($input = array()) {
        $this->inherit_fields = array('template_id' => 0, 'disallow_indexing' => -1);


        parent::__construct($input);
        // todo объединить
        if ($this->data && !$this->data[$this->parent_field]) {
            $parent = fx::data('site')->get_by_id($this->data['site_id']);
            foreach ($this->inherit_fields as $k => $v) {
                if ($this->data[$k] == $v) {
                    //$this->data[$k] = $parent[$k];
                    $this->data_inherit[$k] = $parent[$k];
                }
            }
        } else if ($this->data) {
            $parent = fx::data('subdivision')->get_by_id($this->data[$this->parent_field]);
            $this->parent_sub_tree[] = $parent;
            foreach ($this->inherit_fields as $k => $v) {
                if ($this->data[$k] == $v) {
                    $this->data_inherit[$k] = $parent->get_data_inherit($k);
                    //$this->data[$k] = $parent[$k];  
                }
            }
        }
    }

    public function get_data_inherit($item) {
        return isset($this->data_inherit[$item]) ? $this->data_inherit[$item] : $this->data[$item];
    }

    public function get_parents($only_id = false) {
        if ($only_id) {
            foreach ($this->parent_sub_tree as $v) {
                $ret[] = $v['id'];
            }
        } else {
            $ret = $this->parent_sub_tree;
        }

        return $ret;
    }

    public function get_site() {
        return fx::data('site')->get_by_id($this['site_id']);
    }

    public function info() {
        dump($this->data);
    }

    protected function _before_insert() {
        if (!isset($this['hidden_url'])) {
            $this['hidden_url'] = $this->get_real_hidden_url();
        }
    }

    protected function _before_update() {
        if (isset($this->modified_data['keyword']) && $this->modified_data['keyword'] != $this->data['keyword']) {
            $this['hidden_url'] = $this->get_real_hidden_url();
        }
    }

    protected function _before_delete() {
        $child_subs = fx::data('subdivision')->get_all('parent_id', $this['id']);
        foreach ($child_subs as $subdivision) {
            $subdivision->delete();
        }

        $infoblocks = fx::data('infoblock')->get_all('subdivision_id', $this['id']);
        foreach ($infoblocks as $infoblock) {
            $infoblock->delete();
        }
    }

    protected function _after_update() {
        if (isset($this->modified_data['keyword']) && $this->modified_data['keyword'] != $this->data['keyword']) {
            $submenu = fx::data('subdivision')->get_all('parent_id', $this['id']);
            foreach ($submenu as $sub_item) {
                $sub_item->update_hidden_url();
            }
        }
    }

    public function update_hidden_url() {
        $this['hidden_url'] = $this->get_real_hidden_url();
        $this->save();

        $submenu = fx::data('subdivision')->get_all('parent_id', $this['id']);
        foreach ($submenu as $sub_item) {
            $sub_item->update_hidden_url();
        }
    }

    protected function get_real_hidden_url() {
        $url = '/';
        $sub = $this;
        while ($sub['parent_id']) {
            $url = '/'.$sub['keyword'].$url;
            $sub = fx::data('subdivision')->get_by_id($sub['parent_id']);
        }
        $url = '/'.$sub['keyword'].$url;
        $this['hidden_url'] = $url;

        return $url;
    }

}

