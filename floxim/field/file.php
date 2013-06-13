<?php

class fx_field_file extends fx_field_baze {

    protected $_to_delete_id = 0;

    protected static $filetable = array();

    protected function get_fileinfo (  fx_infoblock_content $infoblock, $file_id ) {
        $infoblock_id = $infoblock['id'];
        if ( !isset(self::$filetable[$infoblock_id.'-'.$this['id']]) ) {
            $this->init_filetable($infoblock);
        }

        return self::$filetable[$infoblock_id.'-'.$this['id']][$file_id];
    }

    protected function init_filetable ( fx_infoblock_content $infoblock ) {
        $fx_core = fx_core::get_object();

        $infoblock_id = $infoblock['id'];
        self::$filetable[$infoblock_id.'-'.$this['id']] = array();
        $db_res = $infoblock->get_db_result();
        if ( $db_res ) {
            $file_ids = array();
            foreach ( $db_res as $res ) {
                $file_ids[] = intval($res[$this->name]);
            }
            if ( $file_ids ) {
                $info = fx::data('filetable')->get_all("`id` IN (".join(',', $file_ids ).") ");
                foreach ( $info as $v ) {
                    self::$filetable[$infoblock_id.'-'.$this['id']][$v['id']] = $v->get();
                }
            }
        }

    }

    public function content_procces(fx_content $content, fx_infoblock_content $infoblock = null, $hash_obj = null) {
        $fx_core = fx_core::get_object();
        $name = $this->name;

        $info = $this->get_fileinfo($infoblock, $content[$this->name]);
        $var = $this->get_field_vars($info);

        $result = array();
        $result['f_'.$name.'_none'] = $var->get_path();
        $result['f_'.$name] = $var;

        if ($hash_obj) {
            $eit_in_place_info = $this->get_edit_jsdata($content);
            $hash = $fx_core->page->add_edit_field('f_'.$name, $eit_in_place_info, null, $hash_obj);

            $var->set_to_str_value('<'.$this->_wrap_tag.' class="'.$hash.'">'.$var->get_path().'</'.$this->_wrap_tag.'>');

            $result['f_'.$name.'_hash'] = $hash;
        } else {
            $result['f_'.$name.'_hash'] = '';
        }

        return $result;
    }

    protected function get_field_vars($info) {
        return new fx_field_vars_file($info);
    }

    public function get_edit_jsdata($content) {
        parent::get_edit_jsdata($content);

        $this->_edit_jsdata['type'] = 'file';

        $this->_edit_jsdata['fileinfo']['field_id'] = $this['id'];


        $file_id = $content[$this->name];
        if ($file_id) {
            $sql = "SELECT * FROM `{{filetable}}` WHERE `id` = '".$file_id."'";
            $fileinfo = fx::db()->get_row($sql);
            $this->_edit_jsdata['fileinfo']['real_name'] = $fileinfo['real_name'];
            $this->_edit_jsdata['fileinfo']['path'] = fx::config()->HTTP_FILES_PATH.$fileinfo['path'];
            $this->_edit_jsdata['fileinfo']['old'] = $file_id;
        }

        return $this->_edit_jsdata;
    }

    public function get_js_field($content, $tname = 'f_%name%', $layer = '', $tab = '') {
        parent::get_js_field($content, $tname, $layer, $tab);
        $this->_js_field['type'] = 'file';
        $this->_js_field['field_id'] = $this['id'];


        $file_id = $content[$this->name];
        if ($file_id) {
            $sql = "SELECT * FROM `{{filetable}}` WHERE `id` = '".$file_id."'";
            $fileinfo = fx::db()->get_row($sql);
            $this->_js_field['real_name'] = $fileinfo['real_name'];
            $this->_js_field['path'] = fx::config()->HTTP_FILES_PATH.$fileinfo['path'];
            $this->_js_field['old'] = $file_id;
        }
        return $this->_js_field;
    }

    public function get_input() {
        $fx_core = fx_core::get_object();
        $html =  "<input  class='fx_form_field fx_form_field_".fx_field::get_type_by_id($this->type_id)."' type='file' name='f_".$this->name."' />";
        if ( $this->value ) {
            $file = fx::data('filetable')->get_by_id($this->value);
            if ( $file ) {
                $html .= '<br/> <span>' . fx_lang('Текущий файл:') . ' </span><a href="'.fx::config()->HTTP_FILES_PATH.$file['path'].'">'.$file['real_name'].'</a>';
            }
        }

        return $html;
    }

    public function get_savestring(fx_essence $content = null) {
        if (is_numeric($this->value)) {
            return $this->value;
        }

        $current_file_id = $content ? $content[$this->name] : null;
        return $current_file_id;

        if ($this->value['delete'] && $current_file_id == $this->value['delete']) {
            $this->_to_delete_id = $current_file_id;
            $current_file_id = 0;
        }

        if (!$this->value || ($this->value['error'] == 4 || (isset($this->value['id']) && $this->value['id'] == 0))) {
            return $current_file_id;
        }

        if (($id = intval($this->value['id']))) {
            return $id;
        }

        if ($this->value) {
            $r = fx::files()->save_file($this->value, $content->get_upload_folder());
        }

        return +$r['id'];
    }

    public function post_save($content) {
        $fx_core = fx_core::get_object();

        if ($this->_to_delete_id) {
            $file_to_delete = fx::data('filetable')->get_by_id($this->_to_delete_id);
            $file_to_delete->set('to_delete', 1)->save();
        }
    }

    public function get_sql_type() {
        return "INT";
    }

    public function get_export_value($value, $dir = '') {
        $fx_core = fx_core::get_object();

        if ( !$value ) {
            return 0;
        }

        $fileinfo = fx::data('filetable')->get_by_id($value);
        if ( !$fileinfo || $fileinfo['to_delete'] ) {
            return 0;
        }

        $new_name = md5(rand().time().$fileinfo['real_name']).'_'.$fileinfo['real_name'];
        $fx_core->files->copy( fx::config()->HTTP_FILES_PATH.$fileinfo['path'], $dir.'/'.$new_name);

        $result = new stdClass();
        $result->real_name = $fileinfo['real_name'];
        $result->type = $fileinfo['type'];
        $result->size = $fileinfo['size'];
        $result->path = $new_name;


        return $result;
    }

    public function get_import_value ( $content, $value, $dir = '' ) {
        $fx_core = fx_core::get_object();

        if ( $value && is_array($value) ) {
            $value['path'] = $dir.$value['path'];
            $r = $fx_core->files->save_file($value, $content->get_upload_folder());
            return $r['id'];
        }
        else {
            return 0;
        }
    }

}

?>

