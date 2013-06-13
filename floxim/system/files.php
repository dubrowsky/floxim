<?php

class fx_system_files extends fx_system {

    protected $core;
    public $ftp_host;
    public $ftp_port;
    public $ftp_path;
    public $ftp_user;
    public $new_file_mods;
    public $new_dir_mods;
    protected $password;
    protected $base_url;
    protected $base_path;
    protected $tmp_files;

    protected function mkdir_ftp($path, $recursive = true) {
        if (!$path) {
            return 1;
        }

        $parent_path = dirname($path);

        if (!is_dir($this->base_path.$parent_path)) {
            if ($recursive) {
                $res = $this->mkdir($parent_path, true);
                if ($res) {
                    return $res;
                }
            } else {
                return 2;
            }
        }

        $dir_name = basename($path);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url.'/'.$parent_path.'/');
        curl_setopt($ch, CURLOPT_POSTQUOTE, array(
                "MKD ".$dir_name,
                "SITE CHMOD ".sprintf("%o", $this->new_dir_mods)." ".$dir_name)
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== false) {
            curl_close($ch);
            return 0;
        } else {
            $info = curl_getinfo($ch);
            curl_close($ch);
            return $info["http_code"];
        }
    }

    protected function ls_not_recursive($path) {
        if (!$path) {
            return null;
        }

        $local_path = realpath($this->base_path.$path);
        if (!$local_path) {
            return null;
        }

        $result = array();

        $handle = opendir($local_path);
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if ($file != "." && $file != "..") {
                    $file_path = realpath($local_path.'/'.$file);
                    $result[] = array(
                            "name" => $file,
                            "path" => $file_path,
                            "dir" => is_dir($file_path) ? 1 : 0
                    );
                }
            }
            closedir($handle);
        }
        return $result;
    }

    protected function ls_recursive($path) {
        if (!$path) {
            return null;
        }

        $local_path = realpath($this->base_path.$path);
        if (!$local_path) {
            return null;
        }

        $dirs = array();
        $result = array();

        array_push($dirs, $local_path.'/');

        while ($dir = array_pop($dirs)) {
            $handle = opendir($dir);
            if ($handle) {
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != "..") {
                        $file_path = realpath($dir.$file);
                        $is_dir = is_dir($file_path) ? 1 : 0;
                        $result[] = array(
                                "name" => $file,
                                "path" => $file_path,
                                "dir" => $is_dir
                        );
                        if ($is_dir) {
                            array_push($dirs, $file_path.'/');
                        }
                    }
                }
                closedir($handle);
            }
        }

        return $result;
    }

    protected function chmod_not_recursive($filename, $perms) {
        if (!$filename) {
            return 1;
        }

        $local_filename = $this->base_path.$filename;

        if (!file_exists($local_filename)) {
            return 1;
        }

        // пробуем поменять через локальную ФС
        if (@chmod($local_filename, $perms)) {
            return 0;
        }

        // в случае неудачи ломимся по ftp
        return $this->chmod_ftp(dirname($filename), array(basename($filename)), $perms);
    }

    /**
     * Меняет в указанном каталоге права на указанные файлы
     * @param string $dir катаог, в котором лежат файлы
     * @param array $files массив имен файлов (только имена, без пути)
     * @param array $files новые права доступа (помнить про восьмиричную систему счисления)
     * @return int 0 - успех, в противном случае код ошибки ftp
     */
    protected function chmod_ftp($dir, $files, $perms) {
        if (!$files || empty($files)) {
            return 1;
        }

        $ftp_pathname = $this->base_url.'/'.$dir.'/';
        $ftp_cmds = array();
        foreach ($files as $file) {
            $ftp_cmds[] = "SITE CHMOD ".sprintf("%o", $perms)." ".$file;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ftp_pathname);
        curl_setopt($ch, CURLOPT_POSTQUOTE, $ftp_cmds);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== false) {
            curl_close($ch);
            return 0;
        } else {
            $info = curl_getinfo($ch);
            curl_close($ch);
            return $info["http_code"];
        }
    }

    protected function chmod_recursive($filename, $perms) {
        if (!$filename) {
            return 1;
        }

        $local_filename = $this->base_path.$filename;

        if (!file_exists($local_filename)) {
            return 1;
        }

        if (!is_dir($local_filename)) {
            return $this->chmod_not_recursive($filename, $perms);
        }

        $result = 0;

        $dirs = array();

        if (!@chmod($local_filename, $perms)) {
            $result |= $this->chmod_ftp(dirname($filename), array(basename($filename)), $perms);
        }

        array_push($dirs, $filename.'/');

        while ($dir = array_pop($dirs)) {
            $handle = @opendir(realpath($this->base_path.$dir));
            if ($handle) {
                $chmod_failed = array();  // то, что не смогли поменять через локальную ФС
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != "..") {
                        $file_path = $dir.$file;
                        $local_file_path = $this->base_path.$file_path;

                        // пробуем поменять через локальную ФС
                        if (!@chmod(realpath($local_file_path), $perms)) {
                            $chmod_failed[] = $file;
                        }

                        if (is_dir($local_file_path)) {
                            array_push($dirs, $file_path.'/');
                        }
                    }
                }
                closedir($handle);

                if (!empty($chmod_failed)) {
                    $result |= $this->chmod_ftp($dir, $chmod_failed, $perms);
                }
            }
        }

        return $result;
    }

    protected function _copy_file($local_old_filename, $local_new_filename) {
        $res = @copy($local_old_filename, $local_new_filename);

        if ($res !== false) {
            @chmod($local_new_filename, $this->new_file_mods);
            return 0;
        }

        $content = $this->readfile($old_filename);
        if ($content !== null) {
            $res = $this->writefile($new_filename, $content);
        }

        return $res;
    }

    /*
     * @param string логин пользователя ftp
     * @param string пароль пользователя ftp
     * @param string имя хоста ftp
     * @param string ftp-порт
     * @param string ftp-каталог с корнем сайта
     * @return object
     */

    public function __construct($user = '', $password = '', $host = null, $port = 21, $ftp_path = '') {
        // load parent constructor
        parent::__construct();
        $this->core = fx_core::get_object();

        $this->ftp_user = $user;
        $this->ftp_password = $password;
        $this->ftp_port = $port;
        if ($ftp_path && ($ftp_path[strlen($ftp_path) - 1] == '/')) {
            $ftp_path = substr($ftp_path, 0, -1);
        }
        if ($ftp_path && $ftp_path[0] != '/') {
            $ftp_path = '/'.$ftp_path;
        }
        $this->ftp_path = $ftp_path;
        $this->ftp_host = $host ? $host : $_SERVER['HTTP_HOST'];
        $this->base_url = "ftp://".$this->ftp_user.":".$this->ftp_password."@".
                $this->ftp_host.":".$this->ftp_port."/".$this->ftp_path;
        $this->base_path = fx::config()->FLOXIM_FOLDER;
        $this->new_file_mods = 0666;
        $this->new_dir_mods = 0777;

        $this->tmp_files = array();
    }

    public function __destruct() {
        foreach ($this->tmp_files as $f) {
            $this->rm($f);
        }
    }

    public function writefile($filename, $filedata = '', $make_dir = true) {
        if (!$filename) {
            return 1;
        }

        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;

        // Проверяем существует ли каталог и создаем его если можно
        if ($local_filename && file_exists($local_filename)) {
            $path_success = is_writeable($local_filename);
        } else {
            $path_success = false;
            $filepath = dirname($filename);
            $local_filepath = dirname($local_filename);
            if (!is_dir($local_filepath)) {
                if ($make_dir) {
                    $this->mkdir($filepath, true);
                }
            }

            $path_success = is_writeable($local_filepath);
        }

        if ($path_success) {
            // Пробуем записать через локальную ФС
            $file = @fopen($local_filename, "w");
            if ($file) {
                $success = !(fwrite($file, $filedata) === false);
                fclose($file);
                @chmod($file, $this->new_file_mods);
            }
        }

        if (isset($success) && ($success !== false)) {
            return 0;
        }

        throw new fx_exception_files("Не могу произвести запись в файл ".$filename);

        // В противном случае пишем через ftp
        $tmpfile = tmpfile();
        fwrite($tmpfile, $filedata);
        fseek($tmpfile, 0);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url.$filename);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_UPLOAD, 1);
        curl_setopt($ch, CURLOPT_INFILE, $tmpfile);
        curl_setopt($ch, CURLOPT_POSTQUOTE, array("SITE CHMOD ".sprintf("%o", $this->new_file_mods)." ".$this->ftp_path.$filename));
        curl_setopt($ch, CURLOPT_TRANSFERTEXT, 1);
        if (curl_exec($ch) !== false) {
            curl_close($ch);
            fclose($tmpfile);
            return 0;
        } else {
            $info = curl_getinfo($ch);
            curl_close($ch);
            fclose($tmpfile);
            return $info["http_code"];
        }
    }

    public function chmod($filename, $perms, $recursive = false) {
        if (!$filename) {
            return 1;
        }

        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        if ($recursive) {
            return $this->chmod_recursive($filename, $perms);
        } else {
            return $this->chmod_not_recursive($filename, $perms);
        }
    }

    public function mkdir($path, $recursive = true) {
        if (!$path) {
            return 1;
        }

        if ($path[0] != '/') {
            $path = '/'.$path;
        }

        $local_path = $this->base_path.$path;

        if (is_dir($local_path)) {
            return 0;
        }

        // сначала пробуем через ФС
        if (@mkdir($local_path, $this->new_dir_mods, $recursive)) {
            chmod($local_path, $this->new_dir_mods);
            return true;
        }
        else {
            throw new fx_exception_files("Cannot create directory ".$path);
        }

        return 1;
        // пробуем по ftp
        return $this->mkdir_ftp($path, $recursive);
    }

    /**
     * Чтение файла
     * @param string $filename имя файла
     */
    public function readfile($filename) {

    	$local_filename = $this->get_full_path($filename);

        // Проверяем существование, возможность чтения и не является ли каталогом
        if (!file_exists($local_filename) || !is_readable($local_filename) || is_dir($local_filename)) {
            throw new fx_exception_files("Unable to read file $local_filename");
        }

        return file_get_contents($local_filename);
    }

    public function ls($path, $recursive = false, $sort = false) {
        if (!$path) {
            return null;
        }

        if ($path[0] != '/') {
            $path = '/'.$path;
        }

        $local_path = $this->base_path.$path;

        if (!is_dir($local_path) || !is_readable($local_path)) {
            return null;
        }

        if (!$recursive) {
            $result = $this->ls_not_recursive($path);
        } else {
            $result = $this->ls_recursive($path);
        }

        if ($sort && !empty($result)) {
            // helpfull arrays to sorting
            $dirs = $names = array();
            // get arrays
            foreach ($result as $file) {
                $dirs[] = $file['dir'];
                $names[] = $file['name'];
            }
            // sorting
            array_multisort($dirs, SORT_DESC, SORT_NUMERIC, $names, SORT_ASC, SORT_STRING, $result);
        }

        return $result;
    }

    protected function rm_ftp($dir, $files) {  // только пустой
        if (!$files || empty($files)) {
            return 1;
        }

        $ftp_pathname = $this->base_url.'/'.$dir.'/';
        $ftp_cmds = array();
        foreach ($files as $file) {
            $ftp_cmds[] = "DELE ".$file;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ftp_pathname);
        curl_setopt($ch, CURLOPT_POSTQUOTE, $ftp_cmds);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== false) {
            curl_close($ch);
            return 0;
        } else {
            $info = curl_getinfo($ch);
            curl_close($ch);
            return $info["http_code"];
        }
    }

    public function rm($filename) {
        if (is_array($filename)) {
            foreach ($filename as $file) {
                $result = $this->rm($file);
            }

            return 0;
        }

        if (!$filename) {
            return 1;
        }

        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;

        if (!file_exists($local_filename)) {
            return 1;
        }

        $result = 0;

        if (is_dir($local_filename)) {

            if ($filename[strlen($filename) - 1] != '/') {
                $filename .= '/';
                $local_filename .= '/';
            }

            $handle = opendir($local_filename);
            if ($handle) {
                $failed_files = array();  // то, что не смогли удалить через локальную ФС
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != "..") {

                        $local_file = $local_filename.$file;

                        if (is_dir($local_file)) {
                            $result |= $this->rm($filename.$file);
                        } else {
                            if (!@unlink($local_file)) {  // пробуем удалить через локальную ФС
                                $failed_files[] = $file;
                            }
                        }
                    }
                }
                closedir($handle);

                if (!empty($failed_files)) {
                    $result |= $this->rm_ftp($filename, $failed_files);
                }
            }

            if (is_writable($local_filename)) {
                $success = @rmdir($local_filename);
            }

            if (isset($success) && $success) {
                return 0;
            } else {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $this->base_url.'/'.dirname($filename).'/');
                curl_setopt($ch, CURLOPT_POSTQUOTE, array("RMD ".basename($filename)));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                if (curl_exec($ch) !== false) {
                    curl_close($ch);
                    return $result;
                } else {
                    $info = curl_getinfo($ch);
                    curl_close($ch);
                    return $result | $info["http_code"];
                }
            }
        } else {
            if (is_writable($local_filename)) {
                $success = @unlink($local_filename);
            }

            if (isset($success) && $success) {
                return 0;
            } else {
                return $this->rm_ftp(dirname($filename), array(basename($filename)));
            }
        }
    }

    public function get_perm($filename, $ret_str = false) {

        if (!$filename) {
            return null;
        }

        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;

        // Проверяем существование и возможность чтения
        if (!file_exists($local_filename) || !is_readable($local_filename)) {
            return null;
        }

        $perms = fileperms($local_filename);
        if ($ret_str) {
            $perms = $perms & 0777;
            $res = ($perms & 0400) ? 'r' : '-';
            $res .= ($perms & 0200) ? 'w' : '-';
            $res .= ($perms & 0100) ? 'x' : '-';
            $res .= ($perms & 040) ? 'r' : '-';
            $res .= ($perms & 020) ? 'w' : '-';
            $res .= ($perms & 010) ? 'x' : '-';
            $res .= ($perms & 04) ? 'r' : '-';
            $res .= ($perms & 02) ? 'w' : '-';
            $res .= ($perms & 01) ? 'x' : '-';
            return $res;
        } else {
            return $perms;
        }
    }

    public function rename($filename, $new_filename) {

        $new_filename = trim($new_filename, "/");

        if (!$filename || !$new_filename) {
            return null;
        }

        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;
        $local_new_filename = dirname($local_filename).'/'.$new_filename;

        if (!file_exists($local_filename)) {
            return 1;
        }

        if (is_writable($local_filename)) {
            $success = @rename($local_filename, $local_new_filename);
        }

        if (isset($success) && $success) {
            return 0;
        }

        $ftp_path = $this->base_url.'/'.dirname($filename).'/';

        // пробуем переименовать через ftp
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ftp_path);
        curl_setopt($ch, CURLOPT_POSTQUOTE, array("RNFR ".basename($filename), "RNTO ".$new_filename));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== false) {
            curl_close($ch);
            return 0;
        } else {
            $info = curl_getinfo($ch);
            curl_close($ch);
            return $info["http_code"];
        }
    }

    public function resize_image ( $image_path, $sizes = array(), $new_path = false, $new_type = false, $jpeg_compression = false ) {
        
        list($width, $height, $type, $attr, $mime) = getimagesize($image_path);        
        switch ( $type ) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($image_path);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($image_path);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($image_path);
                break;
            default:
                return false;
                break;
        }

        if ( empty($sizes) ) {
            if ( $width > fx::config()->IMAGE_MAX_WIDTH || $height > fx::config()->IMAGE_MAX_WIDTH ) {
                $bearings = ( $width > $height ) ? 'horizontal' : 'vertical';
                switch ($bearings) {
                    case 'horizontal':
                        $result_width = fx::config()->IMAGE_MAX_WIDTH;
                        $result_height = floor(($height*$result_width)/$width);
                        break;
                    default:
                        $result_height = fx::config()->IMAGE_MAX_HEIGHT;
                        $result_width = floor(($width*$result_height)/$height);
                        break;
                }
            }
        } elseif ( !empty($sizes['width']) && !empty($sizes['height']) ) {
            $result_width = $sizes['width'];
            $result_height = $sizes['height'];
        } else {
            if ( empty($sizes['width']) ) {
                $result_height = $sizes['height'];
                $result_width = floor(($width*$result_height)/$height);
            } else {
                $result_width = $sizes['width'];
                $result_height = floor(($height*$result_width)/$width);
            }
        }
        if (!$result_width || !$result_height) {
            return true;
        }
        $result_image = imagecreatetruecolor($result_width, $result_height);
        imagecopyresized($result_image,$image,0,0,0,0,$result_width,$result_height,imagesx($image),imagesy($image));
        $new_type = !empty($new_type) ? $new_type : $type;
        $new_path = !empty($new_path) ? $new_path : $image_path;

        switch ( $new_type ) {
            case IMAGETYPE_JPEG:
                $jpeg_compression = empty($jpeg_compression) ? 80 : $jpeg_compression;
                $image = imagejpeg($result_image, $new_path, $jpeg_compression);
                break;
            case IMAGETYPE_GIF:
                $image = imagepng($result_image, $new_path);
                break;
            case IMAGETYPE_PNG:
                $image = imagegif($result_image, $new_path);
                break;
            default:
                return false;
                break;
        }
        return true;
    }

    public function create_thumb ( $image_path, $thumb_path = null, $thumb_size = array() ) {
        if ( empty($thumb_path) ) {
            $path = explode('/',$image_path);
            $filename = array_pop($path);
            $filename = explode('.',$filename);
            $filename[count($filename)-2] = $filename[count($filename)-2] . '-thumb';
            $filename = implode('.',$filename);
            array_push($path, $filename);
            $thumb_path = implode('/',$path);
            $thumb_path = $thumb_path;
        }
        list($width, $height, $type, $attr, $mime) = getimagesize($image_path);
        $bearings = ( $width > $height ) ? 'horizontal' : 'vertical';
        if ( empty($thumb_size) ) {
            $thumb_size = ( $bearings == 'horizontal' ) ? array('width' => fx::config()->THUMB_MAX_WIDTH) : array('height' => fx::config()->THUMB_MAX_HEIGHT);
        }
        $this->resize_image( $image_path, $thumb_size, $thumb_path );        
        return $thumb_path;
    }

    public function move_uploaded_file($tmp_file, $destination) {

        if (!$tmp_file || !$destination) {
            return null;
        }

        if ($destination[0] != '/') {
            $destination = '/'.$destination;
        }

        $local_destination = $this->base_path.$destination;

        $res = move_uploaded_file($tmp_file, $local_destination);
        $this->resize_image($local_destination);


        if (($res === false) && is_uploaded_file($tmp_file)) {
            $content = file_get_contents($tmp_file);
            if ($content !== false) {
                $res = $this->writefile($destination, $content, false);
            }
        } else {
            $res = 0;
        }

        return $res;
    }

    public function copy($old_filename, $new_filename, $make_dir = true) {

        if (!$old_filename || !$new_filename) {
            return null;
        }

        if ($old_filename[0] != '/') {
            $old_filename = '/'.$old_filename;
        }
        $local_old_filename = $this->base_path.$old_filename;

        if ($new_filename[0] != '/') {
            $new_filename = '/'.$new_filename;
        }
        $local_new_filename = $this->base_path.$new_filename;

        $local_parent_dir = substr($local_new_filename, 0, strrpos($local_new_filename, '/'));

        if (!is_dir($local_parent_dir)) {  // проверяем существует ли каталог назначения
            if ($make_dir) {
                $parent_dir = substr($new_filename, 0, strrpos($new_filename, '/'));
                $res = $this->mkdir($parent_dir);
            } else {
                return null;
            }
        }


        if (!is_dir($local_old_filename)) {  // копируем 1 файл
            return $this->_copy_file($local_old_filename, $local_new_filename);
        }

        // копируем каталог
        $res = $this->mkdir($new_filename);

        if ($new_filename[strlen($new_filename) - 1] != '/') {
            $new_filename .= '/';
            $local_new_filename .= '/';
        }
        if ($old_filename[strlen($old_filename) - 1] != '/') {
            $old_filename .= '/';
            $local_old_filename .= '/';
        }

        $ls = $this->ls($old_filename);
        foreach ($ls as $v) {
            if ($v['dir']) {
                $res = $this->copy($old_filename.$v['name'], $new_filename.$v['name'], true);
            } else {
                $res = $this->_copy_file($v[path], $local_new_filename.$v['name']);
            }
        }

        return 0;
    }

    public function move($old_filename, $new_filename, $make_dir = true) {

        if (!$old_filename || !$new_filename) {
            return null;
        }

        $res = $this->copy($old_filename, $new_filename, $make_dir);
        if ($res !== 0) {
            return null;
        }

        $res = $this->rm($old_filename);

        return $res;
    }

    public function filesize($filename) {
        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;

        return filesize($local_filename);
    }

    public function file_exists($filename) {
        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }
        $local_filename = $this->base_path.$filename;
        return file_exists($local_filename);
    }

     public function is_writable($filename) {
        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }
        $local_filename = $this->base_path.$filename;
        return is_writable($local_filename);
    }

    public function file_include ( $filename, $vars = array() ) {
        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;

        extract($vars);
        $fx_core = fx_core::get_object();

        include($local_filename);
    }

    public function is_dir($filename) {
        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;

        return is_dir($local_filename);
    }

    /**
     * Создает временный файл (файл автоматически удалится в деструкторе класса)
     * @return mixed путь к файлу относительно корня сайта
     */
    public function create_tmp_file() {
        do {  // генерируем уникальное имя файла
            $local_filename = fx::config()->TMP_FOLDER.uniqid();
        } while (file_exists($local_filename));

        $h = fopen($local_filename, "w");
        if (!$h) {
            throw new fx_exception_files("Cannot create temporary file");
        }
        fclose($h);

        $count = 1;
        $filename = str_replace($this->base_path, '', $local_filename, $count);

        $this->tmp_files[] = $filename;
        return $filename;
    }

    /**
     * Создает временный каталог (файл автоматически удалится в деструкторе класса)
     * @return mixed путь к каталогу относительно корня сайта
     */
    public function create_tmp_dir() {
        do {  // генерируем уникальное имя файла
            $local_filename = fx::config()->TMP_FOLDER.uniqid();
        } while (file_exists($local_filename));

        $res = mkdir($local_filename);
        if (!$res) {
            throw new fx_exception_files("Cannot create temporary directory");
        }

        $count = 1;
        $filename = str_replace($this->base_path, '', $local_filename, $count).'/';

        $this->tmp_files[] = $filename;
        return $filename;
    }

    //--------------------------------------------------------------------

    public function save_file($file, $dir) {
        $dir = trim($dir, '/').'/';
        $this->mkdir(fx::config()->HTTP_FILES_PATH.$dir);

        // обычный FILES
        if (is_array($file) && !$file['link'] && !$file['source_id'] && !$file['path']) {
            $type = 1;
            $filename = $file['name'];
            $filetype = $file['type'];
        }
        else if ( is_array($file) && $file['real_name'] && $file['path']) {
            $type = 4;
            $filename = $file['real_name'];
            $filetype = $file['type'];
        }
        else if (is_array($file) && $file['source_id']) {
            $type = 2;
            $fileinfo = fx::db()->get_row("SELECT `real_name`, `type`, `path` FROM {{filetable}} WHERE id = '".intval($file['source_id'])."' ");
            $filename = $fileinfo['real_name'];
            $filetype = $fileinfo['type'];
            $link = fx::config()->FILES_FOLDER.$fileinfo['path'];
        } else if (($link = $file['link']) || ( is_string($file) && $link = $file)) {
            $type = 3;
            $filename = substr($link, strrpos($link, '/') + 1);
            $filetype = 'Image';
        }

        $put_file = $this->get_put_filename($dir, $filename);
        $thumb_path = NULL;

        if ($type == 1) {
            $destination = fx::config()->HTTP_FILES_PATH.$dir.$put_file;
            if ($destination[0] != '/') {
                $destination = '/'.$destination;
            }
            $this->move_uploaded_file($file['tmp_name'], $destination);
            $destination = $this->base_path . $destination;
            //$thumb_path = fx::files()->create_thumb($destination);
            // dev_log('thumb path in saving file',$thumb_path);
        } else if ( $type == 2 || $type == 3) {
            $content = file_get_contents($link);
            file_put_contents(fx::config()->FILES_FOLDER.$dir.$put_file, $content);
        }
        else {
            fx::files()->copy($file['path'], fx::config()->HTTP_FILES_PATH.$dir.$put_file);
        }

        fx::db()->query("INSERT INTO `{{filetable}}` SET
        `real_name` = '" . fx::db()->escape($filename) . "',
        `path` = '" . $destination . "',
        `type` = '" . $filetype . "',
        `size` = '" . filesize(fx::config()->FILES_FOLDER.$dir.$put_file) . "'");/*,
        `thumb_path` = '" . ( empty($thumb_path) ? NULL : $thumb_path ) . "'");*/

        return array(   
            'id' => fx::db()->insert_id(), 
            'path' => $destination,
            //'thumb_path' => ( empty($thumb_path) ? NULL : $thumb_path ),
            'filename' => $filename
        );
    }

    protected function get_put_filename($dir, $name) {
        $fx_core = fx_core::get_object();

        if ( $this->file_exists(fx::config()->HTTP_FILES_PATH.$dir.$name) ) {
            return $name;
        }

        $point_pos = strrpos($name, '.');
        $without_ext = substr($name, 0, $point_pos);
        $ext = substr($name, $point_pos + 1);
        $i = 0;

        while ( $this->file_exists(fx::config()->HTTP_FILES_PATH.$dir.$without_ext.'_'.$i++.'.'.$ext)){
            ;
        }

        return $without_ext.'_'.--$i.'.'.$ext;
    }

    private function tar_check() {
        static $res;
        if ($res !== null) {
            return $res;
        }

        if ( !preg_match("/Windows/i", php_uname())) {  // it's not Windows
            $err_code = 127;
            @exec("tar --version", $output, $err_code);
            $res = $err_code ? false : true;
        } else {
            $res = false;
        }

        return $res;
    }

    /**
     *
     * @param string $out_file - выходной файл
     * @param string $dir - каталог, который будет корнем для архива
     * @return mixed 0 в случае удачи, либо null в случае ошибки
     */
    public function tgz_create($out_file, $dir) {
        $fx_core = fx_core::get_object();
        require_once fx::config()->INCLUDE_FOLDER.'tar.php';

        if (!$out_file || !$dir) {
            return null;
        }

        if ($out_file[0] != '/') {
            $out_file = '/'.$out_file;
        }
        $local_out_file = $this->base_path.$out_file;

        if ($dir[0] != '/') {
            $dir = '/'.$dir;
        }
        $local_dir = $this->base_path.$dir;
        @set_time_limit(0);

        if ($this->tar_check()) {  // сначала пробуем через внешнюю программу tar
            exec("cd '$local_dir'; tar  -zchf '$local_out_file' * 2>&1", $output, $err_code);
            if (!$err_code && file_exists($local_out_file)) {
                return 0;
            }
        }

        // в случае неудачи - сами мудрим с tar-ом
        if ($local_dir[strlen($local_dir) - 1] == '/') {  // hack for OS windows
            $local_dir = substr($local_dir, 0, -1);
        }

        $tar_object = new Archive_Tar($local_out_file, "gz");
        $tar_object->setErrorHandling(PEAR_ERROR_RETURN);
        $res = $tar_object->createModify($local_dir, '', $local_dir);
        return $res ? 0 : null;
    }

    /**
     *
     * @param string $tgz_file - файл с архивом
     * @param string $dir - каталог, в который извылекаем данные архива
     * @return mixed 0 в случае удачи, либо null в случае ошибки
     */
    public function tgz_extract($tgz_file, $dir) {
        $fx_core = fx_core::get_object();
        require_once fx::config()->INCLUDE_FOLDER.'tar.php';

        if (!$tgz_file || !$dir) {
            return null;
        }

        if ($tgz_file[0] != '/') {
            $tgz_file = '/'.$tgz_file;
        }
        $local_tgz_file = $this->base_path.$tgz_file;

        if ($dir[0] != '/') {
            $dir = '/'.$dir;
        }
        $local_dir = $this->base_path.$dir;

        if (!is_dir($local_dir)) {  // если каталога не существует, создадим его
            $res = $this->mkdir($dir);
        }

        @set_time_limit(0);

        if ($this->tar_check()) {  // сначала пробуем через внешнюю программу tar
            exec("tar -zxf '$local_tgz_file' -C '$local_dir' 2>&1", $output, $err_code);
            if (!$err_code || strpos($output[0], "time")) { // ignore "can't utime, permission denied"
                return 0;
            }
        } else {
            $tar_object = new Archive_Tar($local_tgz_file, "gz");
            $tar_object->setErrorHandling(PEAR_ERROR_PRINT);
            $res = $tar_object->extract($local_dir);
            return $res ? 0 : null;
        }
    }

    public function get_full_path($filename) {
        if (!$filename) {
            return null;
        }

        if ($filename[0] != '/' && strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        	$filename = '/'.$filename;
        }

        $filename = preg_replace("~^".preg_quote($this->base_path)."~", '', $filename);
		$filename = $this->base_path.DIRECTORY_SEPARATOR.preg_replace("~^".preg_quote(DIRECTORY_SEPARATOR)."~", '', $filename);
        return $filename;
    }

    public function mime_content_type($filename) {
        if (!$filename) {
            return null;
        }

        if ($filename[0] != '/') {
            $filename = '/'.$filename;
        }

        $local_filename = $this->base_path.$filename;

        if (!file_exists($local_filename) || is_dir($local_filename)) {
            return null;
        }

        // Пытаемся средствами ПХП
        if (extension_loaded('fileinfo')) {
            $finfo = new finfo;
            $fileinfo = $finfo->file($local_filename, FILEINFO_MIME_TYPE);
            return $fileinfo;
        }

        // Пытаемся через шелл
        $shell_filename = escapeshellarg($local_filename);
        @exec('file -b --mime-type '.$shell_filename.' 2>/dev/null', $output, $err_code);
        if (!$err_code && $output && $output[0]) {
            return $output[0];
        }

        // Сами мутим =((
        return $this->my_mime_content_type($local_filename);
    }

    private function my_mime_content_type($local_filename) {
        preg_match("#\.([a-z0-9]{1,7})$#i", $local_filename, $fileSuffix);

        switch (strtolower($fileSuffix[1])) {
            case "js" :
                return "application/x-javascript";

            case "html" :
            case "htm" :
            case "php" :
                return "text/html";

            case "txt" :
                return "text/plain";

            case "mpeg" :
            case "mpg" :
            case "mpe" :
                return "video/mpeg";

            case "jpg" :
            case "jpeg" :
            case "jpe" :
                return "image/jpg";

            case "png" :
            case "gif" :
            case "bmp" :
            case "tiff" :
                return "image/".strtolower($fileSuffix[1]);

            case "css" :
                return "text/css";

            case "xml" :
                return "application/xml";

            case "doc" :
            case "docx" :
                return "application/msword";

            case "json" :
                return "application/json";

            case "xls" :
            case "xlt" :
            case "xlm" :
            case "xld" :
            case "xla" :
            case "xlc" :
            case "xlw" :
            case "xll" :
                return "application/vnd.ms-excel";

            case "ppt" :
            case "pps" :
                return "application/vnd.ms-powerpoint";

            case "rtf" :
                return "application/rtf";

            case "pdf" :
                return "application/pdf";

            case "mp3" :
                return "audio/mpeg3";

            case "wav" :
                return "audio/wav";

            case "aiff" :
            case "aif" :
                return "audio/aiff";

            case "avi" :
                return "video/msvideo";

            case "wmv" :
                return "video/x-ms-wmv";

            case "mov" :
                return "video/quicktime";

            case "zip" :
                return "application/zip";

            case "tar" :
                return "application/x-tar";

            case "swf" :
                return "application/x-shockwave-flash";

            default :
                return "application/".strtolower($fileSuffix[1]);
        }
    }

    public function is_image($filename) {
        return (strpos($this->mime_content_type($filename), 'image/') !== false);
    }

    public function get_file_error($error_num) {
        $text[UPLOAD_ERR_OK] = 'There is no error, the file uploaded with success.';
        $text[UPLOAD_ERR_INI_SIZE] = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
        $text[UPLOAD_ERR_FORM_SIZE] = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
        $text[UPLOAD_ERR_PARTIAL] = 'The uploaded file was only partially uploaded.';
        $text[UPLOAD_ERR_NO_FILE] = 'No file was uploaded.';
        $text[UPLOAD_ERR_NO_TMP_DIR] = 'Missing a temporary folder.';
        $text[UPLOAD_ERR_CANT_WRITE] = 'Failed to write file to disk.';
        $text[UPLOAD_ERR_EXTENSION] = 'A PHP extension stopped the file upload.';

        return $text[$error_num];
    }

}

class fx_exception_files extends fx_exception {

}

?>
