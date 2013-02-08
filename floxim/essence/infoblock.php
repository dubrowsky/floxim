<?php

defined("FLOXIM") || die("Unable to load file.");

class fx_infoblock extends fx_essence {

    
    protected $infoblock2layout = null;
    
    public function set_infoblock2layout(fx_infoblock2layout $visual) {
        $this->infoblock2layout = $visual;
    }
    
    public function get_infoblock2layout() {
        return $this->infoblock2layout;
    }
    
    public function get_type() {
        return 'infoblock';
    }
    
    public function render() {
        $params = $this->get('params');
        if (!is_array($params)) {
            $params = array();
        }
        if (!isset($params['infoblock_id'])) {
            $params['infoblock_id'] = $this->get('id');
        }
        $controller = fx::controller($this->get('controller'), $params, $this->get('action'));
        $result = $controller->process();
        if ($vis = $this->get_infoblock2layout()) {
            $tpl = fx::template($vis['template_name']);
            $tpl_action = $vis['template_variant'] ? $vis['template_variant'] : $this->get('action');
            //$tpl_params = $vis['template_visual'] ? $vis['template_visual'] : array();
            //$tpl_params['input'] = $result;
            if (is_array($vis['template_visual'])) {
                foreach ($vis['template_visual'] as $vis_key => $vis_value) {
                    $tpl->set_var($vis_key, $vis_value);
                    $tpl->set_var_meta($vis_key, array(
                        'var_type' => 'visual'
                    ));
                }
            }
            $output = $tpl->render($tpl_action, array('input' => $result));
        } else {
            ob_start();
            echo "<pre>" . htmlspecialchars(print_r($result, 1)) . "</pre>";
            $output = ob_get_clean();
        }
        $infoblock_sign = "infoblock[".$this["id"].'] '.$this->get('controller').'.'.$this->get('action');
        $output = "\n<!--".$infoblock_sign."-->\n".$output."\n<!--//".$infoblock_sign."-->\n";
        return $output;
    }

    public function get_access($item = '', $consider_inheritance = true) {
        $fx_core = fx_core::get_object();

        $items = fx_rights::get_user_types();
        $types = fx_rights::get_rights_types();
        $access = $this['access'];

        // права по умолчанию
        foreach ($types as $type) {
            if (!in_array($access[$type], $items)) {
                $access[$type] = 'inherit';
            }
        }

        if ($consider_inheritance && $this['type'] == 'content') {
            $ctpl = fx::data('ctpl')->get_by_id($this['list_ctpl_id']);
            if ($ctpl) {
				$ctpl_access = $ctpl->get_access();
				foreach ($access as $type => $v) {
					if ($v == 'inherit') {
						$access[$type] = $ctpl_access[$type];
					}
				}
			}
        }

        return $item ? $access[$item] : $access;
    }

    public function check_rights($action) {
        return true;
    }

}