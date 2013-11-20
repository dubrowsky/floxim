<?
fx_run_remote();

function fx_run_remote() {
	$get_floxim_path = 'http://floxim.org/getfloxim/';
	$floxim_version = '0.1.0';
	$file = 'floxim_'.$floxim_version.'.zip';
	
	if (!fx_check_docroot()) {
		echo "Please, put installer file into your document root directory (<b>".$_SERVER['DOCUMENT_ROOT'].'</b>)';
		die();
	}
	if (fx_check_writable()) {
		echo "Target directory is not writable by the script. Please change permissions and try again.";
		die();
	}
	
	if (file_exists($file)) {
		fx_unzip($file, $_SERVER['DOCUMENT_ROOT'].'/');
		echo "Package extracted, now you can <a href='/install/'>run installer</a>.";
		die();
	}
	
	$file_data = file_get_contents($get_floxim_path.$file);
	$fh = fopen($file, 'w');
	fputs($fh, $file_data);
	fclose($fh);
	header("Location: /floxim.php");
	die();
}

function fx_check_docroot() {
	$c_dir = preg_replace('~[/\\\]~', '/', realpath(dirname(__FILE__)));
	return $_SERVER['DOCUMENT_ROOT'] === $c_dir;
}

function fx_check_writable() {
	$test_name = 'test'.md5(time().rand(0,100000)).'.txt';
	@ $test_f = fopen($test_name, 'w');
	if (!$test_f) {
		return false;
	}
	fclose($test_f);
	unlink($test_name);
}

function fx_unzip($file, $dir) {
    if ( !file_exists($dir) ) {
        fx_installer_safe_mkdir($dir);
    }
    $zip_handle = zip_open($dir . $file);
    if (!is_resource($zip_handle)) {
    	die("Problems while reading zip archive");
    }
	while ($zip_entry = zip_read($zip_handle)) {
		$zip_name = zip_entry_name($zip_entry);
		$zip_dir = dirname( zip_entry_name($zip_entry) );
		$zip_size = zip_entry_filesize($zip_entry);
		if (preg_match("~/$~", $zip_name)) {
			$new_dir_name = preg_replace("~/$~", '', $dir . $zip_name);
			fx_installer_safe_mkdir($new_dir_name);
			chmod($new_dir_name, 0777);
		}
		else {
			zip_entry_open($zip_handle, $zip_entry, 'r');
			if (is_writable($dir . $zip_dir)) {
				$fp = @fopen($dir . $zip_name, 'w');
				if (is_resource($fp)) {
					@fwrite($fp, zip_entry_read($zip_entry, $zip_size));
					@fclose($fp);
					chmod($dir.$zip_name, 0666);
				}
			}
			zip_entry_close($zip_entry);
		}
	}
	zip_close($zip_handle);
	return true;
}

function fx_installer_safe_mkdir($dir, $chmod = 0755) {
	$slash = "/";
	if (substr(php_uname(), 0, 7) == "Windows") {
		$slash = "\\";
		$dir = str_replace("/", $slash, $dir);
	}
	
	$tree = explode($slash, $dir);
	
	$path = $slash;
	// win path begin from C:\
	if (substr(php_uname(), 0, 7) == "Windows") $path = "";
	
	foreach($tree as $row) {
		
		if($row === false) continue;
		
		if( !@is_dir($path . $row) ) {
			@mkdir( strval($path . $row), $chmod );
		}
		
		$path .= $row . $slash;
	}
}
?>