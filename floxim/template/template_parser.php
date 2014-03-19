<?php
/*
 * Class breaks the template into tokens and builds a tree
 */
class fx_template_parser {
    
    /**
     * Convert the template in the php code
     * @param string $source source code of the template
     * @param string $code code for a class
     * @return string of php code
     */
    public function parse($source) {
        $source = str_replace("{php}", '<?', $source);
        $source = str_replace("{/php}", '?>', $source);
        
        $tokens = $this->_tokenize($source);
        $tree = $this->_make_tree($tokens);
        return $tree;
    }
    
    protected function _tokenize($source) {
        $parts = preg_split(
            '~(\{[\$\%\/a-z0-9]+[^\{]*?\})~', 
            $source, 
            -1, 
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );
        $tokens = array();
        foreach ($parts as $p) {
            $tokens []= fx_template_token::create($p);
        }
        return $tokens;
    }
    
    /**
     * To determine the type of the token (opening/unit) on the basis of the following tokens
     * @param fx_template_token $token the token with an unknown type
     * @param array $tokens following tokens
     * @return null
     */
    protected  function solve_unclosed($token, $tokens) {
        if (!$token  || $token->type != 'unknown') {
            return;
        }
        $token_info = fx_template_token::get_token_info($token->name);
        $stack = array();
        while ($next_token = array_shift($tokens)) {
            if ($next_token->type == 'unknown') {
                $this->solve_unclosed($next_token, $tokens);
            }
            switch ($next_token->type) {
                case 'open':
                    if (count($stack) == 0) {
                        if (!in_array($next_token->name, $token_info['contains'])) {
                            $token->type = 'single';
                            return;
                        }
                    }
                    $stack[]= $token;
                    break;
                case 'close':
                    if (count($stack) == 0) {
                        if ($next_token->name == $token->name) {
                            $token->type = 'open';
                            return;
                        } else {
                            $token->type = 'single';
                            return;
                        }
                    }
                    array_pop($stack);
                    break;
            }
        }
        echo "solving ".$token->name.
                " | stack has ".count($stack). 
                'items at the end of the method<br />';
    }


    protected function _make_tree($tokens) {
        $stack = array();
        $root = $tokens[0];
        while ($token = array_shift($tokens)) {
            if ($token->type == 'unknown') {
                $this->solve_unclosed($token, $tokens);
            }
            switch ($token->type) {
                case 'open':
                    if (count($stack) > 0) {
                        end($stack)->add_child($token);
                    }
                    $stack []= $token;
                    break;
                case 'close':
                    if ($token->name == 'if') {
                        do {
                            $closed_token = array_pop($stack);
                        } while ($closed_token->name != 'if');
                    } else {
                        $closed_token = array_pop($stack);
                    }
                    
                    if ($token->name == 'if' || $token->name == 'elseif') {
                        // reading forward to check if there is nearby {elseif} / {else} tag
                        $count_skipped = 0;
                        foreach ($tokens as $next_token) {
                            // skip empty tokens
                            if ($next_token->is_empty()) {
                                $count_skipped++;
                                continue;
                            }
                            if (
                                $next_token->type == 'open' && 
                                ($next_token->name == 'elseif' || $next_token->name == 'else')
                            ) {
                                $next_token->stack_extra = true;
                                $stack []= $closed_token;
                                foreach (range(1, $count_skipped) as $skip) {
                                    array_shift($tokens);
                                }
                            }
                            break;
                        }
                    }
                    if ($token->name == 'template' && $closed_token->name == 'template') {
                        $this->_template_to_each($closed_token);
                    }
                    if ($closed_token->stack_extra) {
                        array_pop($stack);
                    }
                    break;
                case 'single': default:
                    $stack_last = end($stack);
                    if (!$stack_last) {
                        echo "Template error: stack empty, trying to add: ";
                        echo "<pre>" . htmlspecialchars(print_r($token, 1)) . "</pre>";
                        die();
                    }
                    $stack_last->add_child($token);
                    break;
            }
        }
        return $root;
    }
    
    protected function _template_to_each(fx_template_token $token) {
        $children = $token->get_children();
        $has_items = false;
        foreach ($children as $child) {
            if ($child->name == 'item') {
                $has_items = true;
                break;
            }
        }
        if (!$has_items) {
            return;
        }
        $with_each_token = new fx_template_token('with_each', 'double', array('select' => '$items'));
        $with_each_token->set_children($children);
        $token->clear_children();
        $token->add_child($with_each_token);
    }
}
