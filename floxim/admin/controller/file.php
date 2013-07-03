<?php

/**
 * Внимание! Редактор изображений работает по относительным путям ( относительно /floxim/ )
 */
class fx_controller_admin_file extends fx_controller_admin {

    public function upload_save($input) {
        $path = 'content';
        $result = fx::files()->save_file($input['file'], $path);
        return $result;
    }

    public function image_editor ( $input ) {
        // PhpImageEditor берет путь до изображения из imagesrc
        if ( $input['path'] ) {
            $_GET['imagesrc'] = '..'.$input['path'];
        }


        ob_start();
        include fx::config()->INCLUDE_FOLDER.'phpimageeditor/lite/shared/floxim.php';
        $html = ob_get_contents();
        ob_end_clean();

        if ( $input['fx_admin'] ) {
            $result['html'] = $html;
            return $result;
        }
        else {
            echo $html;
        }
    }

    public function get_filelist ( $input ) {
        $fx_core = fx_core::get_object();

        $files = $fx_core->db->get_results("select * from {{filetable}} where type like '%image%'", ARRAY_A);
        $values = array();
        $files_path = array();
        $files_md5 = array();
        if ($files) {
            foreach ($files as $v) {
                $path = fx::config()->HTTP_FILES_PATH.$v['path'];
                if (!file_exists(fx::config()->FILES_FOLDER.$v['path'])) continue;
                if (!is_file(fx::config()->FILES_FOLDER.$v['path'])) continue;

                $size = getimagesize(fx::config()->FILES_FOLDER.$v['path']);
                if ( !$size ) continue;

                // исключение дубликатов
                if ( in_array($path, $files_path)) continue;
                $md5 = md5_file(fx::config()->FILES_FOLDER.$v['path']);
                if ( in_array($md5, $files_md5))continue;
                $files_path[] = $path;
                $files_md5[] = $md5;

                $img = array('src' => $path, 'width' => $size[0], 'height' => $size[1]);
                $values[$v['id']] = $img;
            }
        }

        return $values;
    }
}

?>
