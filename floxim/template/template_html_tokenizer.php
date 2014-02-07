<?php
class fx_template_html_tokenizer {
    const STATE_TEXT = 1;
    const STATE_TAG = 2;
    const STATE_PHP = 3;
    const STATE_ANY = 4;
    const STATE_ATT_NAME = 5;
    const STATE_ATT_VAL = 6;
    const STATE_FX = 7;
    const STATE_FX_COMMENT = 8;
    
    public function __construct() {
        $this->state = self::STATE_TEXT;
        // fx comments
        $this->add_rule(self::STATE_ANY, '{*', self::STATE_FX_COMMENT, 'fx_comment_start');
        $this->add_rule(self::STATE_FX_COMMENT, '*}', false, 'fx_comment_end');

        // php
        $this->add_rule(self::STATE_ANY, '<?', self::STATE_PHP, 'php_start');
        $this->add_rule(self::STATE_PHP, '?>', false, 'php_end');

        $this->add_rule(self::STATE_TAG, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_TEXT, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_ATT_NAME, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_ATT_VAL, '{', self::STATE_FX, 'fx_start');
        $this->add_rule(self::STATE_FX, '}', false, 'fx_end');

        $this->add_rule(self::STATE_TEXT, '<', self::STATE_TAG, 'text_to_tag');
        $this->add_rule(self::STATE_TAG, '>', self::STATE_TEXT, 'tag_to_text');
        $this->add_rule(self::STATE_TAG, ' ', self::STATE_ATT_NAME);
        $this->add_rule(self::STATE_ATT_NAME, '="', self::STATE_ATT_VAL, 'att_value_start');
        $this->add_rule(self::STATE_ATT_NAME, "='", self::STATE_ATT_VAL, 'att_value_start');
        $this->add_rule(self::STATE_ATT_NAME, "=", self::STATE_ATT_VAL, 'att_value_start');
        $this->add_rule(self::STATE_ATT_NAME, '>', self::STATE_TEXT, 'tag_to_text');
        $this->add_rule(self::STATE_ATT_VAL, '"', self::STATE_TAG, 'att_value_end');
        $this->add_rule(self::STATE_ATT_VAL, "'", self::STATE_TAG, 'att_value_end');
        $this->add_rule(self::STATE_ATT_VAL, ' ', self::STATE_TAG, 'att_value_end');
        $this->add_rule(self::STATE_ATT_VAL, '>', self::STATE_TAG, 'att_value_end');
    }
    protected $rules = array();
    public function add_rule($first_state, $char, $new_state = false, $callback = null) {
        $this->rules [] = array($first_state, $char, $new_state, $callback, $char[0], strlen($char));
    }

    public function set_state($state) {
        $this->prev_state = $this->state;
        $this->state = $state;
    }

    protected $stack = '';
	
    public function parse($string) {
        $this->position = 0;
        $parts = preg_split(
            "~(<[a-z0-9\/\?]+|>|\{\*|\*\}|<\?|\?>|[\{\}]|=[\'\"]?|[\'\"]|\s+)~", 
            $string, 
            -1, 
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        foreach ($parts as $ch) {
            $this->position += mb_strlen($ch);
            foreach($this->rules as $rule) {
                list($old_state, $r_char, $new_state, $callback, $r_first, $test_length) = $rule;		
                if ($old_state != $this->state && $old_state != self::STATE_ANY) { 
                    continue;
                }
                if (substr($ch, 0, $test_length) != $r_char) {
                    continue;
                }
                $callback_ok = true;
                if ($callback) {
                    $callback_ok = $this->$callback($ch);
                }
                if ($callback_ok !== false) {
                    if ($new_state) {
                        $this->prev_state = $this->state;
                        $this->state = $new_state;
                    }
                    if (!$callback) {
                        $this->stack .= $ch;
                    }
                    continue 2;
                }
            }
            $this->stack .= $ch;
        }
        if (!empty($this->stack)) {
            $this->text_to_tag('');
        }
        return $this->res;
    }
    
    protected $res = array();
    
    protected function _add_token($source, $end) {
    	$start = $end - mb_strlen($source);
        $token = fx_template_html_token::create($source);
        $token->offset = array($start, $end);
        $this->res []= $token;
    }
    
	protected function text_to_tag($ch) {
        if ($this->stack !== '') {
            $this->_add_token($this->stack, $this->position- mb_strlen($ch));
        }
		$this->stack = $ch;
	}
	
	protected function tag_to_text($ch) {
        $this->_add_token($this->stack.$ch, $this->position);
		$this->stack = '';
	}
    
    protected function fx_comment_start($ch) {
        $this->prev_stack = $this->stack;
    }
    
    protected function fx_comment_end($ch) {
        $this->stack = $this->prev_stack;
        $this->set_state($this->prev_state);
    }
	
	protected function php_start($ch) {
        if ($this->state == self::STATE_FX_COMMENT) {
            return false;
        }
		$this->stack .= $ch;
	}
	
	protected function php_end($ch) {
		$this->stack .= $ch;
		$this->set_state($this->prev_state);
	}
    
    protected  function fx_start($ch) {
        $this->stack .= $ch;
    }
    
    protected function fx_end($ch) {
        $this->stack .= $ch;
        $this->set_state($this->prev_state);
    }


    protected $att_quote = null;
	protected function att_value_start($ch) {
		if (preg_match("~[\'\"]$~", $ch, $att_quote)) {
			$this->att_quote = $att_quote[0];
		}
		$this->stack .= $ch;
	}
	
	protected function att_value_end($ch) {
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
		$this->att_quote = null;
		if ($ch == '>') {
			$this->tag_to_text($ch);
		} else {
			$this->stack .= $ch;
		}
	}
}