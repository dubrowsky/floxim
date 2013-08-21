<?php
class fx_patch extends fx_essence {
    public function install() {
        if ($this['status'] != 'ready') {
            return false;
        }
        if (!$this['url']) {
            return false;
        }
        
        $dir = fx::config()->DOCUMENT_ROOT.
               fx::config()->HTTP_FILES_PATH.
               'patches/'.$this['from'].'-'.$this['to'];
        
        if (!file_exists($dir)) {
            $saved = fx::files()->save_file(
                    array('link' => $this['url']),
                    'patches/'
            );
            fx::files()->unzip($saved['fullpath'], 'patches/');
            unlink($saved['fullpath']);
        }
        
        if (!file_exists($dir) || !is_dir($dir)) {
            return false;
        }
        
        $patch_script = $dir.'/patch.php';
        if (file_exists($patch_script)) {
            require_once($patch_script);
        }
        $method_pre = 'patch_'.str_replace(".", '_', $this['to']);
        $method_after = $method_pre.'_after';
        if (function_exists($method_pre)) {
            call_user_func($method_pre);
        }
        $files_dir = $dir.'/files';
        if (file_exists($files_dir)) {
            $this->_update_files($files_dir, $files_dir);
        }
        
        $this->_update_version_number($this['to']);
        
        
        $this['status'] = 'installed';
        $this->save();
        $next_patch = fx::data('patch')->where('from', $this['to'])->one();
        if ($next_patch) {
            $next_patch->set('status', 'ready')->save();
        }
        if (function_exists($method_after)) {
            call_user_func($method_after);
        }
        return true;
    }
    
    protected function _update_files($dir, $base) {
        $items = glob($dir."/*");
        dev_log("Updating files", $items);
        if (!$items) {
            return;
        }
        
        foreach ($items as $item) {
            $item_target = fx::config()->DOCUMENT_ROOT.str_replace($base, '', $item);
            if (is_dir($item)) {
                if (!file_exists($item_target)){
                    mkdir($item_target, 0777);
                }
                $this->_update_files($item, $base);
            } else {
                $fh = fopen($item_target, 'w');
                fputs($fh, file_get_contents($item));
                fclose($fh);
            }
        }
    }
    
    protected function _update_version_number($new_version) {
        $config_file = fx::config()->DOCUMENT_ROOT.'/floxim/system/config.php';
        $new_full = $new_version.".".fx::version('build');
        $config_content = file_get_contents($config_file);
        $config_content = preg_replace_callback(
            "~['\"]FX_VERSION['\"]\s*=>\s*['\"]".fx::version('full')."['\"]~is", 
            function($matches) use ($new_full) {
                return str_replace(fx::version('full'), $new_full, $matches[0]);
            }, 
            $config_content
        );
        $fh = fopen($config_file, 'w');
        fputs($fh, $config_content);
        fclose($fh);
    }
}