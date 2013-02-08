<?php

class fx_infoblock_widget extends fx_infoblock {

    public function show_index() {
        $fx_core = fx_core::get_object();

        $wigdet = fx::data('widget')->get_by_id($this['essence_id']);
        $tpl = $wigdet->load_tpl_object();


        foreach ($wigdet->fields() as $field) {
            $fields['f_'.$field->get_name()] = $field['default'];
        }
        if ($this['visual'])
                foreach ($this['visual'] as $k => $v) {
                $fields['f_'.$k] = $v;
            }

        if ($fields) $tpl->set_vars($fields);

        $tpl->set_vars('fx_core', $fx_core);
        $tpl->set_vars('fx_widget', $wigdet);
        $tpl->set_vars('fx_infoblock', $this);
        $tpl->set_vars('fx_path', $wigdet->get_path());

        if ($this['subdivision_id'])
                $tpl->set_vars('subdivision', fx::data('subdivision')->get_by_id($this['subdivision_id']));

        $tpl->settings();

        ob_start();

        $tpl->record();
        $fx_main_content = ob_get_contents();

        ob_end_clean();

        return $fx_main_content;
    }

}
