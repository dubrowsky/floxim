<?php
dev_log('front content controller, skip!!!');
die();
class fx_controller_content extends fx_controller {

    protected $component, $subdivision, $infoblock, $content, $user, $fields, $tpl;
    protected $ajax;

    protected function _load_data($input) {
        $fx_core = fx_core::get_object();
        $match = array();
        if (preg_match("/(\d+)-(\d+)/", $input['id'], $match)) {
            $component_id = $match[1];
            $content_id = $match[2];
        } else if ($input['class_id']) {
            $component_id = $input['class_id'];
            $content_id = ($input['content_id'] ? $input['content_id'] : $input['id']);
        } else {
            $infoblock = $fx_core->infoblock->get_by_id($input['fx_infoblock']);
            $component_id = $infoblock['essence_id'];
            $content_id = $input['content_id'];
        }

        $this->component = $fx_core->component->get_by_id($component_id);
        if ($content_id) {
            $this->content = $fx_core->content->get_by_id($component_id, $content_id);
        }

        if ($infoblock) {
            if ($infoblock['essence_id'] != $this->component['id']) {
                die("Не найден инфоблок");
            } else {
                $this->infoblock = $infoblock;
            }
        } else {
            $infoblock = $fx_core->infoblock->get_by_id($content['infoblock_id']);
        }

        $this->subdivision = $fx_core->subdivision->get_by_id($this->infoblock['subdivision_id']);
        $this->user = $fx_core->env->get_user();
        $this->fields = $this->component->fields();
        $this->tpl = $this->component->load_tpl_object($this->infoblock['list_ctpl_id']);
        $this->ajax = (bool) $input['fx_ajax'];
    }

    public function add_save($input) {
        $fx_core = fx_core::get_object();
        $result = array('status' => 'ok');

        $this->_load_data($input);
        if (!$this->infoblock) {
            die("Инфоблок не существует");
        }

        $this->content = $fx_core->content->create($this->component['id']);
        $this->content['parent_id'] = intval($input['parent_id']);
        $this->content['infoblock_id'] = $this->infoblock['id'];

        $result = $this->do_cond('add', $input);

        return $this->_after_action('add', $result, $input);
    }

    public function edit_save($input) {
        $this->_load_data($input);

        $result = $this->do_cond('edit', $input);

        return $this->_after_action('edit', $result, $input);
    }

    protected function do_cond($action, $input) {
        $this->tpl->set_vars('input', $input);
        $this->tpl->set_vars('fx_content', $this->content);
        $this->tpl->set_vars('fx_infoblock', $this->infoblock);
        $this->tpl->set_vars('fx_subdivision', $this->subdivision);
        $this->tpl->set_vars('fx_component', $this->component);
        $this->tpl->set_vars('fx_fields', $this->fields);

        call_user_func(array($this->tpl, $action.'_cond'));

        $err = $this->tpl->get_error();
        if ($err) {
            $result = $err;
            $result['status'] = 'error';
        } else {
            // системные проверки
            $result = $this->content->set_field_values($this->fields, $input);
        }

        return $result;
    }

    protected function _after_action($action, $result, $input) {
        if ($result['status'] == 'ok') {
            $this->content->save();
            foreach ($this->fields as $v) {
                $v->post_save($this->content);
            }
        }

        ob_start();
        if ($result['status'] == 'ok') {
            call_user_func(array($this->tpl, 'after_'.$action));
        } else if (!$this->ajax && !$input['fx_admin']) {
            $this->tpl->set_vars('result', $result);
            call_user_func(array($this->tpl, 'begin_'.$action.'_form'));
            call_user_func(array($this->tpl, $action.'_form'));
            call_user_func(array($this->tpl, 'end_'.$action.'_form'));
        }
        $content = ob_get_clean();

        if ($this->ajax || $input['fx_admin']) {
            if ($content) {
                $result['aftertext'] = $content;
            }
            if (!$input['fx_admin']) {
                echo json_encode($result);
            }
        } else {
            if ($input['fx_naked']) {
                echo $content;
            } else {
                $page = new fx_controller_page();
                $page->set_main_content($content);
                $page->index();
            }
        }

        return $result;
    }
}