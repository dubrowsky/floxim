<?php

class fx_system_modules extends fx_system {

    public function get_data() {
        $fx_core = fx_core::get_object();
        static $all_modules_data = false;
        if ($all_modules_data === false) {
            $all_modules_data = $fx_core->db->get_results("SELECT * FROM `{{module}}`");
        }

        return $all_modules_data;
    }

    /**
     * Check installed module by keyword
     *
     * @param string module keyword
     * @param bool `Installed` column
     *
     * @return array module data or false
     */
    public function get_by_keyword($keyword) {
        $all_modules_data = $this->get_data();

        foreach ($all_modules_data AS $module_data) {
            if ($module_data['keyword'] == $keyword) {
                return $module_data;
            }
        }

        return false;
    }

    public function is_installed($keyword) {
        return (bool) $this->get_by_keyword($keyword);
    }

    public function load_env($language = "") {
        $fx_core = fx_core::get_object();
        static $result = array();

        if (empty($result)) {
            $modules_data = $this->get_data();
            if (empty($modules_data)) return false;

            // determine language
            if (!$language && $fx_core->env->get_site()) {
                $language = $fx_core->env->get_site()->get("language");
            }
            if (!$language) $language = $fx_core->lang->detect_lang(1);

            if (!$language) return false;

            foreach ($modules_data as $row) {
                $keyword = $row['keyword'];
                $lang_folder = fx::config()->MODULE_FOLDER.$keyword."/lang/";
                // include language file
                if (is_file($lang_folder.$language.".lang.php")) {
                    include_once (fx::config()->MODULE_FOLDER.$keyword."/".$language.".lang.php");
                } else if (is_file($lang_folder."en.lang.php")) {
                    include_once ($lang_folder."en.lang.php");
                }

                if (is_file(fx::config()->MODULE_FOLDER.$keyword."/boot.php")) {
                    include_once (fx::config()->MODULE_FOLDER.$keyword."/boot.php");
                }
            }
        }

        return true;
    }

}
