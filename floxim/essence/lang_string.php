<?php
class fx_lang_string extends fx_essence {
    public function validate() {
        if (!parent::validate()){
            return false;
        }
        $exists = fx::lang()->check_string($this['string'], $this['dict']);
        if ($exists) {
            $this->validate_errors []= 
                    'String "'.$this['string'].'" already exists in the "'.
                        $this['dict'].'" dictionary';
            return false;
        }
        return true;
    }
    
    protected function _after_save() {
        parent::_after_save();
        fx::lang()->drop_dict_files($this['dict']);
    }
    
    protected function _after_delete() {
        parent::_after_delete();
        fx::lang()->drop_dict_files($this['dict']);
    }
}