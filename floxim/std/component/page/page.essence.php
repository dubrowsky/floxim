<?php
class fx_content_page extends fx_content {
    /**
     * Получить id страниц-родителей
     * @return array
     */
    public function get_parent_ids() {
        $c_pid = $this->get('parent_id');
        // if page has null parent, hold it as if it was nested to index
        if ($c_pid === null && ($site = fx::env('site')) && ($index_id = $site['index_page_id'])) {
            return $index_id != $this['id'] ? array($index_id) : array();
        }
        $ids = array();
        while ($c_pid != 0) {
            array_unshift($ids, $c_pid);
            $c_pid = fx::data('content_page')->get_by_id($ids[0])->get('parent_id');
        }
        return $ids;
    }
    
    public function get_path() {
        $path_ids = $this->get_parent_ids();
        $path_ids []= $this['id'];
        $path = fx::data('content_page')->where('id', $path_ids)->all();
        return $path;
    }
    
    protected $_active;
    
    public function get_is_active() {
        return $this->is_active();
    }

    public function is_active () {
        if ($this->_active) {
            return $this->_active;
        }
        $c_page_id  = fx::env('page_id');
        if (!$c_page_id) {
            return false;
        }
        $path = fx::env('page')->get_parent_ids();
        $path []= $c_page_id;

        return $this->_active = in_array($this['id'], $path);
    }
    
    public function is_current() {
        return $this['id'] == fx::env('page_id');
    }
    
    public function get_is_current() {
        return $this->is_current();
    }
    
    protected function _before_save() {
        parent::_before_save();
        if (empty($this['url']) && !empty($this['name'])) {
            $this['url'] = $this['name'];
        }
        if (
                in_array('url', $this->modified) && 
                !empty($this['url']) && 
                !preg_match("~^https?://~", $this['url'])
            ) {
            $url = fx::util()->str_to_latin($this['url']);
            $url  = preg_replace("~[^a-z0-9_-]+~i", '-', $url);
            $url = trim($url, '-');
            $url = preg_replace("~\-+~", '-', $url);
            if (!preg_match("~^/~", $url)) {
                $url = '/'.$url;
            }
            $index = 1;
            while ( fx::data('content_page')->
                    where('url', $url)->
                    where('site_id', $this['site_id'])->
                    where('id', $this['id'], '!=')->
                    one()) {
                $index++;
                $url = preg_replace("~\-".($index-1)."$~", '', $url).'-'.$index;
            }
            $this['url'] = $url;
        }
    }
    
    protected function _after_insert() {
        parent::_after_insert();
        if (empty($this['url'])) {
            $this['url'] = '/page-'.$this['id'].'.html';
            $this->save();
        }
    }
    
    protected function _after_delete() {
        parent::_after_delete();
        $killer = function($n) {
            $n->delete();
        };
        fx::data('content')->where('parent_id', $this['id'])->all()->apply($killer);
        fx::data('infoblock')->where('page_id', $this['id'])->all()->apply($killer);
    }
}