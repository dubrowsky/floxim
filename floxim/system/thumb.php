<?php
class fx_thumb {
    
    protected $source_path = null;
    protected $config = array();
    
    public function __construct($source_http_path, $config) {
        $doc_root = fx::config()->DOCUMENT_ROOT;
        $source_path = $doc_root.'/'.preg_replace('~^[\\/]~', '', $source_http_path);
        if (!file_exists($source_path)) {
            throw new Exception('File not found: '.$source_path);
        }
        $source_path = realpath($source_path);
        $this->source_path = $source_path;
        $this->set_config($config);
    }
    
    public function get_result_path() {
        $hash = md5(serialize($this->config).$this->source_path);
        preg_match("~\.([^\.]+)$~", $this->source_path, $ext);
        if (!isset($ext[1])) {
            return false;
        }
        $ext = $ext[1];
        $thumb_dir = 'fx_thumb';
        if (!file_exists(fx::config()->FILES_FOLDER.$thumb_dir)) {
            mkdir(fx::config()->FILES_FOLDER.$thumb_dir);
        }
        $rel_path = $thumb_dir.'/'.$hash.'.'.$ext;
        $full_path = fx::config()->FILES_FOLDER.$rel_path;
        if (!file_exists($full_path)) {
            $this->process($full_path);
        }
        $path = fx::config()->HTTP_FILES_PATH.$rel_path;
        return $path;
    }
    
    public function process($target_path) {
    	ini_set('memory_limit', '64m');
        $defaults = array(
            'w' => false,
            'h' => false,
            'q' => 90
        );

        $settings = array_merge($defaults, $this->config);
        if ($settings['w'] && $settings['h'] && !isset($settings['c'])) {
            $settings['c'] = true;
        }
       
        list($width, $height, $type, $attr) = getimagesize($this->source_path); 
		
		$types = array(
			IMAGETYPE_GIF  => array(
				'ext' => 'gif',
				'create_func' => 'imagecreatefromgif',
				'save_func' => 'imagegif'
			),
			IMAGETYPE_JPEG  => array(
				'ext' => 'gif',
				'create_func' => 'imagecreatefromjpeg',
				'save_func' => 'imagejpeg'
			),
			IMAGETYPE_PNG  => array(
				'ext' => 'gif',
				'create_func' => 'imagecreatefrompng',
				'save_func' => 'imagepng'
			)
		);
		
		$it = isset($types[$type]) ? $types[$type] : false;
		
		// неправильный/неизвестный тип картинки
		if (!$it) {
			die('wrong image type');
		}
		
		// определяем коэф. масштабирования
		$scale = 1;
		// и отступы для обрезания
		$crop_x = 0;
		$crop_y = 0;
		// и ширину-высоту вырезаемого куска
		$crop_width = $width;
		$crop_height = $height;
		// указали и длину, и ширину
		if ($settings['w'] && $settings['h']) {
			if (isset($settings['c']) && $settings['c']) {
				$scale_x = $settings['w'] / $width;
				$scale_y = $settings['h'] / $height;
				$scale = max($scale_x, $scale_y);
				if (isset($settings['crop_offset']) && in_array($settings['crop_offset'], array('top', 'middle', 'bottom'))) {
					$crop_offset = $settings['crop_offset'];
				} else {
					$crop_offset = 'middle';
				}
				if ($scale == $scale_x) {
					// обрезаем по высоте
					$crop_height = $settings['h'] / $scale;
					switch ($crop_offset) {
						case 'top':
							$crop_y = 0;
							break;
						case 'middle':
							$crop_y = round( ($height - $crop_height) / 2 );
							break;
						case 'bottom':
							$crop_y = $height - $crop_height;
							break;
					}
					
				} else {
					// обрезаем по ширине
					$crop_width = $settings['w'] / $scale;
					switch ($crop_offset) {
						case 'top':
							$crop_x = 0;
							break;
						case 'middle':
							$crop_x = round( ($width - $crop_width) / 2 );
							break;
						case 'bottom':
							$crop_x = $width - $crop_width;
							break;
					}
				}
			}
		} elseif ($settings['w']) {
			$scale = $settings['w'] / $width;
			$settings['h'] = round($height * $scale);
		} elseif ($settings['h']) {
			$scale = $settings['h'] / $height;
			$settings['w'] = round($width * $scale);
		}
		
		$source_i = call_user_func($it['create_func'], $this->source_path);
		$target_i = imagecreatetruecolor($settings['w'],$settings['h']);
		
		
		if ( ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF)) {
			//$this->_addTransparency($target_i, $source_i, $type);
		}
		
		$icr_args = array(
			'target_image' 		=>	$target_i, 		//resource $dst_image ,
			'source_image' 		=>	$source_i, 		//resource $src_image , 
			'target_x' 			=>	0, 				//int $dst_x , 
			'target_y' 			=>	0, 				//int $dst_y , 
			'crop_x'			=> 	$crop_x, 		//int $src_x , 
			'crop_y'			=>	$crop_y,		//int $src_y , 
			'target_width' 		=> 	$settings['w'], 	//int $dst_w , 
			'target_height' 	=>	$settings['h'], 	//int $dst_h , 
			'source_width' 		=>	$crop_width, 	//int $src_w , 
			'source_height'		=>	$crop_height 	//int $src_h 
		);
		call_user_func_array('imagecopyresampled', $icr_args);
		
		if ($type == IMAGETYPE_PNG) {
			$settings['q'] = round($settings['q']/10);
		}
		call_user_func( $it['save_func'], $target_i, $target_path, $settings['q'] );
        //copy($this->source_path, $target_path);
    }


    public function set_config($config) {
        $config = explode(",", $config);
        foreach ($config as $props) {
            list($prop, $value) = explode(":", $props);
            $this->config[$prop] = $value;
        }
    }
}
?>