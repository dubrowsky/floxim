<?php
class fx_template_html {
    protected $_string = null;
    public function __construct($string) {
        $string = $string;
        $this->_string = $string;
    }
    
    public function tokenize() {
        $tokenizer = new fx_template_html_tokenizer_dev();
        $tokens = $tokenizer->parse($this->_string);
        return $tokens;
    }
    
    public function add_meta($meta = array(), $skip_parsing = false) {
        // добавляем сразу обертку
        if ($skip_parsing) {
            $wrapper = fx_template_html_token::create_standalone('<div class="fx_wrapper">');
            $wrapper->add_meta($meta);
            return $wrapper->serialize().$this->_string."</div>";
        }
        $tree = $this->make_tree($this->tokenize());
        $children = $tree->get_children();
        if (count($children) == 1) {
            if ($children[0]->name != 'text') {
                $root = $children[0];
                $root->add_meta($meta);
                return $tree->serialize();
            }
        }
        $wrapper = fx_template_html_token::create('<div>');
        $wrapper->add_meta($meta);
        foreach ($children as $child) {
            $wrapper->add_child($child);
        }
        return $wrapper->serialize();
    }
    
    public function transform_to_floxim() {
        $tokens = $this->tokenize();
        $tree = $this->make_tree($tokens);
        
        $unnamed_replaces = array();
        
        $tree->apply( function(fx_template_html_token $n) use (&$unnamed_replaces) {
            if ($n->name == 'text') {
                /*
                // удаляем пробелы в начале строки, 
                // если она начинается с {...}
                $n->source = preg_replace(
                    "~\s*?[\n\r]+\s*?(\{.+?\})~s", 
                    '\1', 
                    $n->source
                );
                // или заканчивается на {...}
                $n->source = preg_replace(
                    "~(\{.+?\}\s*?)[\n\r]+\s*~s",
                    '\1',
                    $n->source
                );
                */
                return;
            }
            if (preg_match('~\{[\%|\$]~', $n->source)) {
                $n->source = fx_template_html::parse_floxim_vars_in_atts($n->source);
            }
            if ($n->name == 'meta' && ($layout_id = $n->get_attribute('fx:layout'))) {
                $layout_name = $n->get_attribute('fx:name');
                $tpl_tag = '{template id="'.$layout_id.'" name="'.$layout_name.'" of="layout.show"}';
                $tpl_tag .= '{call id="_layout_body" include="true"}';
                $content = $n->get_attribute('content');
                $vars = explode(",", $content);
                foreach ($vars as $var) {
                    $var = trim($var);
                    $negative = false;
                    if (preg_match("~^!~", $var)) {
                        $negative = true;
                        $var = preg_replace("~^!~", '', $var);
                    }
                    $tpl_tag .= '{$'.$var.' select="'.($negative ? 'false' : 'true').'" /}';
                }
                $tpl_tag .= '{/call}{/template}';
                $n->parent->add_child_before(fx_template_html_token::create($tpl_tag), $n);
                $n->remove();
                return;
            }
            if ( ($fx_replace = $n->get_attribute('fx:replace')) ){
                $replace_atts = explode(",", $fx_replace);
                foreach ($replace_atts as $replace_att) {
                    if (!isset($unnamed_replaces[$replace_att])) {
                        $unnamed_replaces[$replace_att] = 0;
                    }
                    $var_name = 'replace_'.$replace_att.'_'.$unnamed_replaces[$replace_att];
                    $unnamed_replaces[$replace_att]++;
                    $default_val = $n->get_attribute($replace_att);
                    switch($replace_att) {
                        case 'src':
                            $var_title = fx::alang('Picture','system');
                            break;
                        case 'href':
                            $var_title = fx::alang('Link','system');
                            break;
                        default:
                            $var_title = $replace_att;
                            break;
                    }
                    $n->set_attribute($replace_att, '{%'.$var_name.' title="'.$var_title.'"}'.$default_val.'{/%'.$var_name.'}');
                    $n->remove_attribute('fx:replace');
                }
            }
            if ( ($var_name = $n->get_attribute('fx:var')) ) {
                if (!preg_match("~^[\$\%]~", $var_name)) {
                    $var_name = '%'.$var_name;
                }
                $n->add_child_first(fx_template_html_token::create('{'.$var_name.'}'));
                $n->add_child(fx_template_html_token::create('{/'.$var_name.'}'));
                $n->remove_attribute('fx:var');
            }
            if ( ($tpl_id = $n->get_attribute('fx:template'))) {
                $tpl_macro_tag = '{template id="'.$tpl_id.'" ';
                if (!$n->get_attribute('fx:omit')) {
                    $tpl_macro_tag .= ' subroot="true" ';
                }
                if ( ($tpl_for = $n->get_attribute('fx:of')) ) {
                    $tpl_macro_tag .= ' of="'.$tpl_for.'"';
                    $n->remove_attribute('fx:of');
                }
                if ( ($tpl_name = $n->get_attribute('fx:name'))) {
                    $tpl_macro_tag .= ' name="'.$tpl_name.'"';
                    $n->remove_attribute('fx:name');
                }
                if ( $n->offset && $n->end_offset) {
                    $tpl_macro_tag .= ' offset="'.$n->offset[0].','.$n->end_offset[1].'" ';
                }
                if ( ($tpl_size = $n->get_attribute('fx:size'))) {
                    $tpl_macro_tag .= ' size="'.$tpl_size.'" ';
                    $n->remove_attribute('fx:size');
                }
                if ( ($tpl_suit = $n->get_attribute('fx:suit'))) {
                    $tpl_macro_tag .= ' suit="'.$tpl_suit.'"';
                    $n->remove_attribute('fx:suit');
                }
                $tpl_macro_tag .= '}';
                $n->parent->add_child_before(fx_template_html_token::create($tpl_macro_tag), $n);
                $n->parent->add_child_after(fx_template_html_token::create('{/template}'), $n);
                $n->remove_attribute('fx:template');
            }
            if ( ($each_id = $n->get_attribute('fx:each')) ) {
                $each_id = trim($each_id, '{}');
                $each_id = str_replace('"', '\\"', $each_id);
                $each_macro_tag = '{each ';
                if (!$n->get_attribute('fx:omit')) {
                    $each_macro_tag .= ' subroot="true" ';
                }
                if (!empty($each_id)) {
                    $each_macro_tag .= ' select="'.$each_id.'"';
                }
                if ( ($each_as = $n->get_attribute('fx:as'))) {
                    $each_macro_tag .= ' as="'.$each_as.'"';
                    $n->remove_attribute('fx:as');
                }
                if (($each_key = $n->get_attribute('fx:key'))) {
                    $each_macro_tag .= ' key="'.$each_key.'"';
                    $n->remove_attribute('fx:key');
                }
                if (( $prefix = $n->get_attribute('fx:prefix')) ) {
                    $each_macro_tag .= ' prefix="'.$prefix.'"';
                    $n->remove_attribute('fx:prefix');
                }
                if ( ($extract = $n->get_attribute('fx:extract'))) {
                    $each_macro_tag .= ' extract="'.$extract.'"';
                    $n->remove_attribute('fx:extract');
                }
                $each_macro_tag .= '}';
                $n->parent->add_child_before(fx_template_html_token::create($each_macro_tag), $n);
                $n->parent->add_child_after(fx_template_html_token::create('{/each}'), $n);
                $n->remove_attribute('fx:each');
            }
            if ( ($area_id = $n->get_attribute('fx:area'))) {
                $n->remove_attribute('fx:area');
                $area = '{area id="'.$area_id.'" ';
                if ( ($area_size = $n->get_attribute('fx:size')) ) {
                    $area .= 'size="'.$area_size.'" ';
                    $n->remove_attribute('fx:size');
                }
                if ( ($area_suit = $n->get_attribute('fx:suit'))) {
                    $area .= 'suit="'.$area_suit.'" ';
                    $n->remove_attribute('fx:suit');
                }
                $area .= '}';
                $n->add_child_first(fx_template_html_token::create($area));
                $n->add_child(fx_template_html_token::create('{/area}'));
            }
            if ( ($if_test = $n->get_attribute('fx:if'))) {
                $n->remove_attribute('fx:if');
                $if = '{if test="'.$if_test.'"}';
                $n->parent->add_child_before(fx_template_html_token::create($if), $n);
                $n->parent->add_child_after(fx_template_html_token::create('{/if}'), $n);
            }
            if ( ($omit = $n->get_attribute('fx:omit'))) {
                $n->omit = $omit;
                $n->remove_attribute('fx:omit');
            }
        });
        $res = $tree->serialize();
        return $res;
    }
    
    public static function parse_floxim_vars_in_atts($input_source) {
        $res = '';
        $source = preg_split("~(\s|=[\'\"]|:|<\?.+?\?>|\{[^\}]+?\})~", $input_source, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $c_tag = null;
        $c_att = null;
        $c_prop = null;
        $c_mode = null;
        foreach ($source as $part) {
            if (!$c_tag && preg_match("~^<~", $part)) {
                $c_tag = preg_replace("~^<~", '', $part);
                $res .= $part;
                $c_mode = 'tag';
                continue;
            }
            if (preg_match("~^<\?~", $part)) {
                $res .= $part;
                continue;
            }
            if (preg_match('~^\{[^\%\$]~', $part)) {
                $res .= $part;
                continue;
            }
            if (preg_match("~^\s+$~", $part)) {
                $res .= $part;
                continue;
            }
            if ($c_mode == 'tag') {
                $res .= $part;
                $c_att = $part;
                $c_mode = 'att';
                continue;
            }
            if ($c_mode == 'att') {
                if (preg_match("~^=[\'\"]$~", $part)) {
                    $c_prop = null;
                    $c_mode = 'attval';
                } else {
                    $c_att .= $part;
                }
                $res .= $part;
                continue;
            }
            if ($c_mode == 'attval') {
                // конец атрибута
                if (preg_match('~\"$~', $part)) {
                    $c_att = '';
                    $c_mode = 'tag';
                    $res .= $part;
                    continue;
                }
                // запоминаем css-свойство
                if ($c_att == 'style' && !$c_prop) {
                    $c_prop = preg_replace('~^\"~', '', $part);
                    $res.= $part;
                    continue;
                }
                if ($c_att == 'style' && preg_match("~;$~", $part)) {
                    $c_prop = null;
                    $res .= $part;
                    continue;
                }
                if (preg_match('~^\{[\%\$]~', $part)) {
                    if (preg_match("~^fx:~", $c_att)) {
                        $res .= $part;
                        continue;
                    }
                    $part = preg_replace("~^([^\s\|\}]+)~", '\1 inatt="true" ', $part);
                    if (preg_match("~type=~", $part)) {
                        $res .= $part;
                        continue;
                    }
                    
                    $c_type = '';
                    if ($c_att == 'style') {
                        if (
                            ($c_prop == 'background' 
                            && preg_match('~url\([\'\"]?$~', $res) ) ||
                            $c_prop == 'background-image'
                        ) {
                            $c_type = 'image';
                        } elseif (
                            $c_prop == 'background' || 
                            $c_prop == 'background-color' || 
                            $c_prop == 'color'
                        ) {
                            $c_type = 'color';
                        } elseif ($c_prop == 'width' || $c_prop == 'height') {
                            $c_type = 'number';
                        }
                    } elseif ($c_att == 'src') {
                        $c_type = 'image';
                    } elseif (
                            ($c_att == 'href' || $c_att == 'title' || $c_att == 'alt') && 
                            !preg_match("~editable=~", $part) &&
                            !preg_match("~^\{\%~", $part)
                        ) {
                        $part = preg_replace("~^([^\s\|\}]+)~", '\1 editable="false" ', $part);
                    }
                    if ($c_type) {
                        $part = preg_replace("~^([^\s\|\}]+)~", '\1 type="'.$c_type.'" ', $part);
                    }
                    $res .= $part;
                    continue;
                }
            }
            $res .= $part;
        }
        return $res;
    }
    
    public function make_tree($tokens) {
        $root = new fx_template_html_token();
        $root->name = 'root';
        $stack = array($root);
        $token_index = -1;
        while ($token = array_shift($tokens)) {
            $token_index++;
            switch ($token->type) {
                case 'open':
                    if (count($stack) > 0) {
                        end($stack)->add_child($token);
                    }
                    $stack []= $token;
                    break;
                case 'close':
                    $closed_tag = array_pop($stack);
                    if ($closed_tag->name != $token->name) {
                        $msg = "HTML parser error: ".
                                "start tag ".htmlspecialchars($closed_tag->source)." (".$closed_tag->offset[0]."-".$closed_tag->offset[1].")".
                                "doesn't match end tag &lt;/".$token->name.'&gt; ('.$token->offset[0].')';
                        
                        throw new Exception($msg);
                    }
                    if ($token->offset) {
                        $closed_tag->end_offset = $token->offset;
                    }
                    break;
                case 'single': default:
                    $stack_last = end($stack);
                    if (!$stack_last) {
                        dev_log("fx_template_html tree error", $tokens, $root);
                        echo fx_debug(
                                "fx_template_html error: stack empty, trying to add: ",
                                '#'.$token_index,
                                $token,
                                $tokens,
                                $root);
                        echo "fx_template_html error: stack empty, trying to add: ";
                        echo "<pre>" . htmlspecialchars(print_r($token, 1)) . "</pre>";
                        die();
                    }
                    $stack_last->add_child($token);
                    break;
            }
        }
        // в стеке должен остаться только <root>
        if (count($stack) > 1) {
            dev_log("All closed, but stack not empty!", $stack);
            //die();
        }
        return $root;
    }
    
    public static function add_class_to_tag($tag_html, $class) {
        if (preg_match("~class\s*=[\s\'\"]*[^\'\"\>]+~i", $tag_html, $class_att)) {
            $class_att_new = preg_replace(
                "~class\s*=[\s\'\"]*~", 
                '$0'.$class.' ', 
                $class_att[0]
            );
            $tag_html = str_replace($class_att, $class_att_new, $tag_html);
        } else {
            $tag_html = self::add_att_to_tag($tag_html, 'class', $class);
        }
        return $tag_html;
    }
    
    public static function add_att_to_tag($tag_html, $att, $value) {
        $tag_html = preg_replace("~^<[^\s>]+~", '$0 '.$att.'="'.htmlentities($value).'"', $tag_html);
        return $tag_html;
    }
}
?>