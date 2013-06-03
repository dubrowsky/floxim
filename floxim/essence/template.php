<?php

/* $Id: template.php 8576 2012-12-27 15:14:06Z myasin $ */
defined("FLOXIM") || die("Unable to load file.");

class fx_template extends fx_essence {

    /**
     * @return fx_template
     */
    public function get_parent() {
        return $this->finder->get_by_id($this['parent_id']);
    }

    public function get_layouts() {
        return $this->finder->get_all('parent_id', $this['id']);
    }

    public function get_path() {
        return fx::config()->SUB_FOLDER.fx::config()->HTTP_TEMPLATE_PATH.$this['keyword'].'/';
    }

    public function get_path_html() {
        $parent = $this->get_parent();
        return $parent->get_path().$this['keyword'].'.html';
    }

    public function get_path_php() {
        $parent = $this->get_parent();
        return $parent->get_path().$this['keyword'].'.tpl.php';
    }

    public function get_tpl_classname() {
        $parent = $this->get_parent();
        return 'template__'.$parent['keyword'].'__'.$this['keyword'];
    }

    public function _before_delete() {
        $fx_core = fx_core::get_object();
        if ($this['parent_id']) {
            $fx_core->files->rm($this->get_path_html());
            $fx_core->files->rm($this->get_path_php());
        } else {
            $layouts = $this->get_layouts();
            foreach ($layouts as $layout) {
                $layout->delete();
            }
            $fx_core->files->rm($this->get_path());
        }
    }

    public function get_default_layout($type) {
        $layouts = $this->get_layouts();
        $res = array();
        $index = 3;

        // поиск наиболее подходящего лэйаута
        foreach ( $layouts as $layout ) {
            if ( $layout['type'] == $type && $layout['default'] ) {
                $res[0] = $layout;
                $index = 0;
            }
            else if ( $layout['type'] == $type  ) {
                $res[1] = $layout;
                if ( $index > 1 ) $index = 1;
            }
            else if ( $layout['default']  ) {
                $res[2] = $layout;
                if ( $index > 2 ) $index = 2;
            }
            else {
                $res[3] = $layout;
            }
        }

        return $res[$index];
    }

    public function get_units( $source_id = null ) {
        $fx_core = fx_core::get_object();
        $result = array();

        $content = $fx_core->files->readfile($this->get_path_html());
        $parser = new fx_html2php();
        $parser->convert($content);
        $units = $parser->get_units();

        $result = array('infoblock' => array(), 'menu' => array());
        $result['infoblock'] = array();

        if ( $units['inherit'] ) {
            foreach ( $units['inherit'] as $inherit ) {
                $layouts = $this->get_parent()->get_layouts();
                foreach ( $layouts as $layout ) {
                    if ( $layout['keyword'] == $inherit['source'] ) {
                        $layout_units = $layout->get_units($inherit['id']);
                        if ( $layout_units['infoblock'] ) {
                            $result['infoblock'] = array_merge($result['infoblock'], $layout_units['infoblock']);
                        }
                        if ( $layout_units['menu'] ) {
                            $result['menu'] = array_merge($result['menu'], $layout_units['menu']);
                        }
                    }
                }
            }
        }


        if ( $units['infoblock'] ) {
            foreach ( $units['infoblock'] as $keyword => $infoblock ) {
                if ( $source_id && $source_id != $infoblock['source_id'] ) {
                    continue;
                }
                 unset($infoblock['blocks'],$infoblock['divider'],$infoblock['source_id']);
                 if ( !$infoblock['embed']) $infoblock['embed'] = 'large';

                $result['infoblock'][$keyword] = $infoblock;
            }
        }

        if ( $units['menu'] ) {
            foreach ( $units['menu'] as $keyword => $menu ) {
                if ( $source_id && $source_id != $menu['source_id'] ) {
                    continue;
                }

                unset($menu['source_id']);
                $result['menu'][$keyword] = $menu;
            }
        }

        return $result;
    }

    public function validate() {
        $fx_core = fx_core::get_object();
        $res = true;

        if (!$this['name']) {
            $this->validate_errors[] = array('field' => 'name', 'text' => 'Укажите название макета дизайна');
            $res = false;
        }
        if (!$this['keyword']) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Укажите keyword макета дизайна');
            $res = false;
        }
        if ($this['keyword'] && !preg_match("/^[a-z][a-z0-9-]*$/i", $this['keyword'])) {
            $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Keyword может сожержать только буквы и цифры');
            $res = false;
        }

        if ($this['keyword'] && !$this['parent_id']) {
            $templates = fx::data('template')->get_parents();
            foreach ($templates as $template) {
                if ($template['id'] != $this['id'] && $template['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Такой keyword уже используется в макете "'.$template['name'].'"');
                    $res = false;
                }
            }
        }
        if ($this['keyword'] && $this['parent_id']) {
            foreach ($this->get_parent()->get_layouts() as $template) {
                if ($template['id'] != $this['id'] && $template['keyword'] == $this['keyword']) {
                    $this->validate_errors[] = array('field' => 'keyword', 'text' => 'Такой keyword уже используется');
                    $res = false;
                }
            }
        }

        return $res;
    }

}
