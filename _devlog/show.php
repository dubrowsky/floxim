<?
//define("DEV_LOG_PATH", dirname(__FILE__).'/log');
require_once 'log.php';

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'list';

$files = get_log_files();

if ($mode == 'clear') {
	foreach ($files as $lf) {
		unlink($lf['path']);
	}
	header("Location: show.php");
	die();
}


?><a href="show.php">к списку</a> | <a href="show.php?mode=clear">удалить все</a><hr /><?

if ($mode == 'list') {
	?><table><?
	foreach ($files as $lf) {
		show_log_file_header($lf);
	}
	?></table><?
	die();
}

function show_log_file_header($lf) {
	?><tr>
		<?if (isset($lf['file'])) {?><td><a href="show.php?mode=show&amp;file=<?=$lf['file']?>"><?=$lf['file']?></a></td><?}?>
			<td><?=$lf['method']?></td>
			<td><?=$lf['url']?></td>
			<td><?=date('H:i:s', $lf['date'])?></td>
	</tr><?
}



if ($mode == 'show') {
	$lf = $files[$_GET['file']];
	$file_content = file_get_contents($lf['path']);
	list($file_header, $file_data) = preg_split("~[\n\r]~", $file_content, 2);
	$file_header = unserialize(trim($file_header));
    fx_debug_start();
	?>
	<table><?show_log_file_header($file_header)?></table><?
	echo $file_data;
	die();
}


function get_log_files() {
	$files = array();
	$dir = DEV_LOG_PATH;
	if ($handle = opendir($dir)) {
		$path = realpath($dir);
		
		while (false !== ($entry = readdir($handle))) {
			if (preg_match("~^log_~", $entry)) {
				$file_path = $path.DIRECTORY_SEPARATOR.$entry;
				$file_key = preg_replace("~^log_~", '', preg_replace("~\.html$~", '', $entry));
				$fh = fopen($file_path, 'r');
				$file_header = fgets($fh);
				$file_header = trim($file_header);
				$file_header = unserialize($file_header);
				fclose($fh);
				$files [$file_key]= array(
					'path' => $file_path,
					'file' => $file_key,
					'url' => $file_header['url'],
					'method' => $file_header['method'],
					'date' => $file_header['date']
				);
			}
		}
		
		closedir($handle);
	}
	
	uasort($files, 'sort_log_files');
	
	return $files;
}


function sort_log_files($a, $b) {
	return $b['date'] - $a['date'];
}
?>