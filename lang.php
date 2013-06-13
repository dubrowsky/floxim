<?php
function fx_lang ( $string )
{	
	$cur_lang = fx::config()->LANGUAGE;
	$db_str = fx::db()->get_results('SELECT lang_' . $cur_lang . ' FROM fx_dictionary WHERE lang_string = "' . $string . '"');
	return empty($db_str[0]['lang_' . $cur_lang]) ? $string : $db_str[0]['lang_' . $cur_lang];
	// return $db_str[0]['lang_' . $cur_lang];
	
	// file cache
	/*
	if( file_exists('./dictionary.php') ) {
		$lang_arr = json_decode(file_get_contents('./dictionary.php'));
	} else {
		$db_str = fx::db()->get_results('SELECT * FROM fx_dictionary');
		$lang_arr = array();
		for ( $i = 0; $i < count($db_str); $i++ ) {
			$lang_arr[$db_str[$i]['lang_string']] = array();
			foreach ( $db_str[$i] as $lang_name => $lang_string ) {
				if ( $lang_name == 'lang_string' || $lang_name == 'id' ) continue;
				$lang = explode('_', $lang_name);
				$lang = $lang[1];
				$lang_arr[$db_str[$i]['lang_string']][$lang] = $lang_string;
			}
		}
		$lang_arr_to_file = json_encode($lang_arr);
		$lang_file = file_put_contents('./dictionary.php',$lang_arr_to_file);
	}
	$cur_lang = fx::config()->LANGUAGE;
	return empty($lang_arr->$string[$cur_lang]) ? $string : $lang_arr->$string[$cur_lang];
	*/
}
?>