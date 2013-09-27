<?
class fx_thumb
{
    protected $source_path = null;
    protected $info = array();
    protected $image = null;
    protected $config = null;
    
    public function __construct($source_http_path, $config)
    {
        $this->config = $this->get_config($config);
        $doc_root     = fx::config()->DOCUMENT_ROOT;
        $source_path  = $doc_root . '/' . preg_replace('~^[\/]~', '', $source_http_path);
        if (!file_exists($source_path)) {
            throw new Exception('File not found: ' . $source_path);
        }
        $source_path = realpath($source_path);
        
        
        $this->source_path = $source_path;
        $info              = getimagesize($source_path);
        $info['width']     = $info[0];
        $info['height']    = $info[1];
        $info['imagetype'] = $info[2];
        unset($info[0]);
        unset($info[1]);
        unset($info[2]);
        unset($info[3]);
        $this->info = $info;
        if (!isset(self::$_types[$info['imagetype']])) {
            // неправильный/неизвестный тип картинки
            die('wrong image type');
        }
        $this->info += self::$_types[$info['imagetype']];
        $this->image = call_user_func($this->info['create_func'], $this->source_path);
    }
    
    protected function _calculateSize($params, $source = null)
    {
        if (!$source) {
            $source = $this->info;
        }
        $ratio = $source['width'] / $source['height'];
        
        $w   = $params['width'];
        $h   = $params['height'];
        $miw = $params['min-width'];
        $maw = $params['max-width'];
        $mih = $params['min-height'];
        $mah = $params['max-height'];
        
        // для начала: width:200 => min-width:200, max-width:200
        if ($w) {
            if (!$miw) {
                $miw = $w;
            }
            if (!$maw) {
                $maw = $w;
            }
        }
        
        // аналогично высота
        if ($h) {
            if (!$mah) {
                $mah = $h;
            }
            if (!$mih) {
                $mih = $h;
            }
        }
        
        // задали только ширину
        if ($w && !$h) {
            $h = $w * 1 / $ratio;
        }
        // задали только высоту
        elseif ($h && !$w) {
            $w = $h * $ratio;
        }
        // не задали ничего
            elseif (!$h && !$w) {
            $h = $source['height'];
            $w = $source['width'];
        }
        
        // добавляем мин/макс.
        
        if ($miw === false) {
            $miw = 0;
        }
        if ($mih === false) {
            $mih = 0;
        }
        if (!$maw) {
            $maw = $w;
        }
        if (!$mah) {
            $mah = $h;
        }
        /*
        echo '<table>';
        foreach (explode(',', 'w,h,miw,mih,maw,mah') as $vn) {
        echo "<tr><td>$".$vn."</td><td>".$$vn."</td></tr>";
        }
        echo "</table>";
        */
        
        // а теперь считаем по табличке http://www.w3.org/TR/CSS21/visudet.html#min-max-widths
        
        /**
        
        Constraint Violation	 Resolved Width	 Resolved Height
        #0	none	 w	 h
        #1	w > max-width	 max-width	 max(max-width * h/w, min-height)
        #2	w < min-width	 min-width	 min(min-width * h/w, max-height)
        #3	h > max-height	 max(max-height * w/h, min-width)	 max-height
        #4	h < min-height	 min(min-height * w/h, max-width)	 min-height
        #5	(w > max-width) and (h > max-height), where (max-width/w <= max-height/h)	max-width	 max(min-height, max-width * h/w)
        #6	(w > max-width) and (h > max-height), where (max-width/w > max-height/h)	max(min-width, max-height * w/h)	 max-height
        #7	(w < min-width) and (h < min-height), where (min-width/w <= min-height/h)	min(max-width, min-height * w/h)	 min-height
        #8	(w < min-width) and (h < min-height), where (min-width/w > min-height/h)	min-width	 min(max-height, min-width * h/w)
        #9	(w < min-width) and (h > max-height)	 min-width	 max-height
        #10	(w > max-width) and (h < min-height)	 max-width	 min-height
        
        */
        
        // шире, чем надо
        if ($w > $maw) {
            // и выше
            if ($h > $mah) {
                if ($maw / $w <= $mah / $h) {
                    // #5
                    $h = max($mih, ($maw * $h / $w));
                    $w = $maw;
                } else {
                    // #6
                    $w = max($miw, $mah * $w / $h);
                    $h = $mah;
                }
            }
            // и ниже
            elseif ($h < $mih) {
                // #10
                $w = $maw;
                $h = $mih;
            }
            // и норм. ширины
            else {
                // #1
                $h = max($maw * $h / $w, $mih);
                $w = $maw;
            }
        }
        // уже, чем надо
        elseif ($w < $miw) {
            // и ниже
            if ($h < $mih) {
                if ($miw / $w <= $mih / $h) {
                    // #7
                    $w = min($maw, $mih * $w / $h);
                    $h = $mih;
                } else {
                    // #8
                    $h = min($mah, $miw * $h / $w);
                    $w = $miw;
                }
            }
            // и выше
            elseif ($h > $mah) {
                // #9
                $w = $miw;
                $h = $mah;
            }
            // и норм. высоты
            else {
                // #2
                $h = min($miw * $h / $w, $mah);
                $w = $miw;
            }
        }
        // с шириной ок. проблемы только с высотой
            
        // выше
            elseif ($h > $mah) {
            // #3
            $w = max($mah * $w / $h, $miw);
            $h = $mah;
        }
        // ниже
            elseif ($h < $mih) {
            // #4
            $w = min($mih * $w / $h, $maw);
            $h = $mih;
        }
        
        return array(
            'width' => round($w),
            'height' => round($h)
        );
    }
    
    public function Resize($params = null)
    {
        if (isset($params))
            $params = $this->get_config($params);
        else
            $params = $this->config;
        $st = array_merge(array(
            'width' => false,
            'height' => false,
            'min-width' => false,
            'min-height' => false,
            'max-width' => false,
            'max-height' => false,
            'crop' => true,
            'quality' => 90
        ), $params);
        
        // вычисляем размеры исходя из min-max, размеров картинки и заданых w-h
        $st = array_merge($st, $this->_calculateSize($st));
        
        $width  = $this->info['width'];
        $height = $this->info['height'];
        $type   = $this->info['imagetype'];
        
        // определяем коэф. масштабирования
        $scale       = 1;
        // и отступы для обрезания
        $crop_x      = 0;
        $crop_y      = 0;
        // и ширину-высоту вырезаемого куска
        $crop_width  = $width;
        $crop_height = $height;
        
        
        if (isset($st['crop']) && $st['crop']) {
            $scale_x = $st['width'] / $width;
            $scale_y = $st['height'] / $height;
            $scale   = max($scale_x, $scale_y);
            if (isset($st['crop_offset']) && in_array($st['crop_offset'], array(
                'top',
                'middle',
                'bottom'
            ))) {
                $crop_offset = $st['crop_offset'];
            } else {
                $crop_offset = 'middle';
            }
            if ($scale == $scale_x) {
                // обрезаем по высоте
                $crop_height = $st['height'] / $scale;
                switch ($crop_offset) {
                    case 'top':
                        $crop_y = 0;
                        break;
                    case 'middle':
                        $crop_y = round(($height - $crop_height) / 2);
                        break;
                    case 'bottom':
                        $crop_y = $height - $crop_height;
                        break;
                }
            } else {
                // обрезаем по ширине
                $crop_width = $st['width'] / $scale;
                switch ($crop_offset) {
                    case 'top':
                        $crop_x = 0;
                        break;
                    case 'middle':
                        $crop_x = round(($width - $crop_width) / 2);
                        break;
                    case 'bottom':
                        $crop_x = $width - $crop_width;
                        break;
                }
            }
        }
        
        $source_i = $this->image;
        $target_i = imagecreatetruecolor($st['width'], $st['height']);
        
        
        if (($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF)) {
            $this->_addTransparency($target_i, $source_i, $type);
        }
        
        $icr_args = array(
            'target_image' => $target_i, //resource $dst_image ,
            'source_image' => $source_i, //resource $src_image , 
            'target_x' => 0, //int $dst_x , 
            'target_y' => 0, //int $dst_y , 
            'crop_x' => $crop_x, //int $src_x , 
            'crop_y' => $crop_y, //int $src_y , 
            'target_width' => $st['width'], //int $dst_w , 
            'target_height' => $st['height'], //int $dst_h , 
            'source_width' => $crop_width, //int $src_w , 
            'source_height' => $crop_height //int $src_h 
        );
        call_user_func_array('imagecopyresampled', $icr_args);
        $this->image = $target_i;
        
    }
    
    protected function _addTransparency($dst, $src, $type)
    {
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefill($dst, 0, 0, $transparent);
            return;
        }
        $t_index = imagecolortransparent($src);
        $t_color = array(
            'red' => 255,
            'green' => 0,
            'blue' => 0
        );
        
        if ($t_index >= 0) {
            $t_color = imagecolorsforindex($src, $t_index);
        }
        $t_index = imagecolorallocate($dst, $t_color['red'], $t_color['green'], $t_color['blue']);
        imagefill($dst, 0, 0, $t_index);
        imagecolortransparent($dst, $t_index);
    }
    
    public function Save($target_path = false, $quality = 90)
    {
        if ($this->info['imagetype'] == IMAGETYPE_PNG) {
            $quality = round($quality / 10);
        }
        if ($target_path === false) {
            $target_path = $this->source_path;
        } elseif ($target_path === null) {
            header("Content-type: " . $this->info['mime']);
        } else {
            $sub_path     = str_replace($_SERVER['DOCUMENT_ROOT'], '', $target_path);
            $sub_path     = preg_replace("~^[//]~", '', $sub_path);
            $target_parts = preg_split("~[//]~", $sub_path);
            $c_path       = $_SERVER['DOCUMENT_ROOT'];
            foreach ($target_parts as $pi => $pdir) {
                $c_path .= '/' . $pdir;
                if (file_exists($c_path)) {
                    continue;
                }
                if (!is_dir($c_path) && $pi != count($target_parts) - 1) {
                    mkdir($c_path, 0777);
                }
            }
            
        }
        $res_image = call_user_func($this->info['save_func'], $this->image, $target_path, $quality);
    }
    
    protected static $_types = array(IMAGETYPE_GIF => array('ext' => 'gif', 'create_func' => 'imagecreatefromgif', 'save_func' => 'imagegif'), IMAGETYPE_JPEG => array('ext' => 'gif', 'create_func' => 'imagecreatefromjpeg', 'save_func' => 'imagejpeg'), IMAGETYPE_PNG => array('ext' => 'gif', 'create_func' => 'imagecreatefrompng', 'save_func' => 'imagepng'));
    public function process($full_path)
    {
        $this->Resize();
        $this->Save($full_path);
    }
    public function get_result_path()
    {
        $hash = md5(serialize($this->config) . $this->source_path);
        preg_match("~.([^.]+)$~", $this->source_path, $ext);
        if (!isset($ext[1])) {
            return false;
        }
        
        $ext       = $ext[1];
        $thumb_dir = 'fx_thumb';
        if (!file_exists(fx::config()->FILES_FOLDER . $thumb_dir)) {
            mkdir(fx::config()->FILES_FOLDER . $thumb_dir);
        }
        $rel_path  = $thumb_dir . '/' . $hash . '.' . $ext;
        $full_path = fx::config()->FILES_FOLDER . $rel_path;
        if (!file_exists($full_path)) {
            $this->process($full_path);
        }
        $path = fx::config()->HTTP_FILES_PATH . $rel_path;
        return $path;
    }
    
    protected function get_config($config)
    {
        $config = explode(",", $config);
        $params = array();
        foreach ($config as $props) {
            list($prop, $value) = explode(":", $props);
            //$this->config[$prop] = $value;
            $params[$prop] = $value;
        }
        return $params;
    }
}
?>