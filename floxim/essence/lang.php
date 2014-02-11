<?php
class fx_lang extends fx_essence {

    public function validate() {
        $res = true;
        if (!$this['en_name']) {
            $this->validate_errors[] = array('field' => 'en_name', 'text' => fx::alang('Enter the name of the language','system'));
            $res = false;
        }
        if (!$this['lang_code']) {
            $this->validate_errors[] = array('field' => 'lang_code', 'text' => fx::alang('Enter the code language','system'));
            $res = false;
        }
        return $res;
    }
    
    protected function _before_delete() {
        fx::db()->query("ALTER TABLE `{{component}}`
                    DROP COLUMN `name_".$this['lang_code']."`,
                    DROP COLUMN `item_name_".$this['lang_code']."`,
                    DROP COLUMN `description_".$this['lang_code']."`");
        fx::db()->query("ALTER TABLE `{{lang_string}}`
                    DROP COLUMN `lang_".$this['lang_code']."`");
    }

    protected function _before_insert() {
        fx::db()->query("ALTER TABLE `{{component}}`
                    ADD COLUMN `name_".$this['lang_code']."` VARCHAR(255),
                    ADD COLUMN `item_name_".$this['lang_code']."` VARCHAR(255),
                    ADD COLUMN `description_".$this['lang_code']."` TEXT ");
        fx::db()->query("ALTER TABLE `{{lang_string}}`
                    ADD COLUMN `lang_".$this['lang_code']."` TEXT");
    }

}

?>