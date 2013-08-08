<?php
class fx_data_content_section extends fx_data_content {
    public function relations() {
        return parent::relations() + array(
            'submenu' => array(
                self::HAS_MANY,
                'content_section',
                'parent_id'
            )
        );
    }
    
    protected $c_depth = 1;
    protected $max_depth = 10;
    public function with_submenu($max_depth = null) {
        if ($max_depth !== null) {
            $this->max_depth = $max_depth;
        }
        $this->with('submenu');
    }
    
    protected function _get_default_relation_finder($rel, $rel_name) {
        if ($rel_name == 'submenu') {
            $f = fx::data('content_section');
            if ($this->c_depth < $this->max_depth) {
                $f->with_submenu($this->max_depth);
                $f->c_depth = $this->c_depth+1;
            }
            dev_log('subm fin', $f, $this);
            return $f;
        }
        return parent::_get_default_relation_finder($rel);
    }
}
?>