<?
fx_run_remote();

function fx_run_remote() {
	$release = 'http://floxim.org/floxim_123213123213.zip';
	if (!fx_check_docroot()) {
		echo "Please, put installer file into your document root directory (<b>".$_SERVER['DOCUMENT_ROOT'].'</b>)';
		die();
	}
	if (fx_check_writable()) {
		echo "Target directory is not writable by the script. Please change permissions and try again.";
		die();
	}
	
}

function fx_check_docroot() {
	return $_SERVER['DOCUMENT_ROOT'] === realpath(dirname(__FILE__));
}

function fx_check_writable() {
	return is_writable(dirname(__FILE__));
}
?>