<?php
class fx_template_attr_parser extends fx_template_html_tokenizer {
    
    public function parse_atts(fx_template_html_token $token) {
        $token->attributes = array();
        $s = $token->source;
        if (!$s || !preg_match("~\s~", $s)) {
            return;
        }
        $this->token = $token;
        $this->c_att = array('name' => null, 'value' => null);
        $this->parse($s);
    }
    
    protected function _add_att() {
        //fx::debug($this->state, $this->stack);
        //if ($this->state != self::TAG && !$this->c_att['name']) {
        if (!$this->c_att['name'] && !preg_match("~^<~", $this->stack)) {
            $this->c_att['name'] = $this->stack;
        }
        if ($this->c_att) {
            $att_name = trim($this->c_att['name']);
            // skip trailing backslash
            if ($att_name && $att_name != '/') {
                $att_val = $this->c_att['value'];
                if ($att_val) {
                    $att_val = trim($att_val);
                }
                $this->token->attributes[$att_name] = $att_val;
            }
        }
        $this->c_att = array('name' => null, 'value' => null);
    }
    
    public function att_name_start($ch) {
        $this->_add_att();
        $this->stack = '';
        parent::att_name_start($ch);
        $this->c_att = array('name' => '', 'value' => null);
    }
    
    public function att_value_start($ch) {
        $this->c_att['name'] = $this->stack;
        parent::att_value_start($ch);
        $this->stack = '';
    }
    
    public function att_value_end($ch) {
        $c_val = $this->stack;
        $res = parent::att_value_end($ch);
        if ($res !== false) {
            $this->c_att['value'] = $c_val;
            $this->_add_att();
            $this->stack = '';
        }
        return $res;
    }
    
    public function tag_to_text($ch) {
        if (!empty($this->stack)) {
            $this->c_att['name'] = $this->stack;
            $this->_add_att();
        }
    }
}