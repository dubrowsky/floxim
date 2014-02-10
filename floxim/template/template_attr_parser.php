<?php
require_once (dirname(__FILE__).'/template_fsm.php');

class fx_template_attr_parser extends fx_template_fsm {

    public $split_regexp = "~(\s|=[\'\"]|:|\"|<\?.+?\?>|\{[^\}]+?\})~";

    const TAG = 1;
    const PHP = 2;
    const FX = 3;
    const ATT = 4;
    const ATT_NAME = 5;
    const ATT_VAL = 6;


    protected $res = '';

    protected $att_quot;

    public function __construct() {
        $this->add_rule(self::TAG, '~^\s+$~', self::ATT_NAME, 'start_att');
        $this->add_rule(self::ATT_NAME, '~^=[\'\"]$~', self::ATT_VAL, 'start_val');
        $this->add_rule(self::ATT_VAL, '~^\s+|[\'\"]$~', self::TAG, 'end_att');
        $this->init_state = self::TAG;
    }

    protected $stack = '';

    public function start_att($ch) {
        $this->stack = '';
        $this->res .= $ch;
    }

    public function start_val($ch) {
        $this->current_attr = $this->stack;
        $this->res .= $ch;
        if (preg_match("~[\'\"]$~", $ch, $att_quote)) {
            $this->att_quote = $att_quote[0];
        }
    }
    public function end_att ($ch) {
        switch ($ch) {
            case '"': case "'":
            if ($this->att_quote !== $ch) {
                return false;
            }
            break;
            case ' ':
                if ($this->att_quote) {
                    return false;
                }
                break;
            case '>':
                if ($this->att_quote) {
                    return false;
                }
                break;
        }
    }

    public function default_callback($ch) {
        $this->stack .= $ch;
        $this->res .= $ch;
    }

} 