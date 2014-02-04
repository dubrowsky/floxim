<?php
/*
 * Класс разбивает шаблон на токены и строит дерево
 */
class fx_template_parser {
    
    /**
     * Преобразовать шаблон в php-код
     * @param string $source исходник шаблона
     * @param string $code код для класса
     * @return string php-код
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
     * Определить тип токена (открывающий/единичный) на основе следующих за ним токенов
     * @param fx_template_token $token токен с неизвестным типом
     * @param array $tokens следующие за ним токены
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
                    $closed_token = array_pop($stack);
                    if ($closed_token->name =='template') {
                        $this->_template_to_each($closed_token);
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
        $subtemplates = array();
        $each_token = false;
        $tpl_id = $token->get_prop('id');
        $each_select = '.';
        if (substr($tpl_id, 0, 1) == '$') {
            $token->name = 'if';
            $token->set_prop('test', $tpl_id);
            $each_select = $tpl_id;
        }
        if (count($children) == 1 && $children[0]->name == 'if') {
            $token = $children[0];
            $children = $token->get_children();
        }
        $is_subroot = true;
        foreach ($children as $child_num => $child) {
            if (
                    $child->name == 'template' && 
                    in_array($child->get_prop('id'), array(
                        'active', 
                        'inactive',
                        'active_link',
                        'separator',
                        'item'
                    ))
                ) {
                $subtemplates[$child->get_prop('id')] = $child;
                if (!$child->get_prop('subroot')) {
                    $is_subroot = false;
                }
                if (!$each_token) {
                    $each_token = new fx_template_token(
                        'each', 
                        'open', 
                        array('select' => $each_select)
                    );
                    $token->set_child($each_token, $child_num);
                } else {
                    $token->set_child(NULL, $child_num);
                }
            }
        }
        if (count($subtemplates) == 0) {
            return;
        }
        if ($is_subroot){
            $each_token->set_prop('subroot', true);
        }
        
        // выбираем дефолтный шаблон - либо inactive, либо item
        $basic_tpl = null;
        $basic_cond = null;
        if (isset($subtemplates['inactive'])) {
            $basic_tpl = $subtemplates['inactive'];
        } elseif (isset($subtemplates['item'])) {
            $basic_tpl = $subtemplates['item'];
        }
        
        $conds = array();
        if ($basic_tpl) {
            
            // собираем дефолтное условие
            // если есть шаблоны для active | active_link, 
            // дефолтный для таких не используем
            if (isset($subtemplates['active'])) {
                $conds['active']= '$item["active"]';
            }
            if (isset($subtemplates['active_link'])) {
                $conds ['active_link']= '$item["active_link"]';
            }
            $basic_cond = count($conds) == 0 ? null : '!('.join(" && ", $conds).')';
        }
        
        // есть варианты
        if ($basic_cond) {
            $basic_cond_token = new fx_template_token('if', 'open', array('test' => $basic_cond));
            $basic_cond_token->add_children($basic_tpl->get_children());
            $each_token->add_child($basic_cond_token);
            if (isset($subtemplates['active_link'])) {
                $active_link_cond = new fx_temlate_token(
                    'if', 'open', array('test' => $conds['active_link'])
                );
                $active_link_cond->add_children($subtemplates['active_link']->get_children());
                $each_token->add_child($active_link_cond);
            }
            if (isset($subtemplates['active'])) {
                $active_cond_test = $conds['active'];
                if (isset($conds['active_link'])) {
                    $active_cond_test .= ' && !'.$conds['active_link'];
                } else {
                    $active_cond_test .= ' || $item["active_link"]';
                }
                $active_cond = new fx_template_token('if', 'open', 
                        array('test' => $active_cond_test)
                );
                $active_cond->add_children($subtemplates['active']->get_children());
                $each_token->add_child($active_cond);
            }
        }
        // только один подшаблон
        else {
            $each_token->add_children($basic_tpl->get_children());
        }
        
        if (isset($subtemplates['separator'])) {
            $each_token->add_child($subtemplates['separator']);
        }
    }
    
    
}
