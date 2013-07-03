<?php
header('Content-Type: text/html; charset=utf-8'); 
session_start();

// installation folder
$INSTALL_FOLDER = join( strstr(__FILE__, "/") ? "/" : "\\", array_slice( preg_split("/[\/\\\]+/", __FILE__), 0, -1 ) ).( strstr(__FILE__, "/") ? "/" : "\\" );
// main Floxim folder
$FLOXIM_FOLDER = fx_standardize_path_to_folder( realpath($INSTALL_FOLDER.'../') );
// custom installation folder
$CUSTOM_FOLDER = str_replace( rtrim($_SERVER['DOCUMENT_ROOT'], '/').'/', '', $FLOXIM_FOLDER);
// normalize path
if ($CUSTOM_FOLDER) $CUSTOM_FOLDER = '/'.trim($CUSTOM_FOLDER, '/').'/';

if (isset($_REQUEST['pwd'])) {
  $_SESSION['pwd'] = $_REQUEST['pwd'];
}

// unlimit time
@set_time_limit(0);
header("Content-type: text/html; charset=utf-8");

error_reporting(E_ALL);
$action = fx_post_get('action');
$ajax = false;

switch ($action) {
    case 0:
        fx_html_beg();
        echo fx_html_status_bar(array("'color': '#444', 'opacity': 1", "можно приступать"));
        echo "Вас приветствует программа-установщик системы управления сайтами Floxim! Сейчас я протестирую ваш сервер на совместимость с Floxim (это рядовая проверка, которая не займет много времени), после чего установлю систему, и вы сможете начать работу. На втором (из трех) шаге проверки мне потребуется доступ к базе данных MySQL.";
        echo fx_html_form(1, 'Приступить к проверке');
    break;
    case 1:
        if (!$ajax)
            fx_html_beg();
        // Проверка настроек сервера: основных и дополнительных
        $php_version = phpversion();
        $logHeader =
                '--------------------------------------------------' . PHP_EOL .
                'Дата ' . date("d.m.Y") . PHP_EOL .
                '--------------------------------------------------' . PHP_EOL .
                'Установка Floxim' . PHP_EOL .
                '--------------------------------------------------';
        fx_write_log($logHeader, false);

        fx_write_log("Этап №" . $action . ": Начало проверки параметров хостинга.");
        fx_write_log("Текущая версия PHP: " . $php_version);

        $errors = array();
        $notices = array();

        if (version_compare($php_version, "5.3.0") == '-1') {
            $errors['phpversion'] = "Версия PHP должна быть не ниже 5.3.0.";
            fx_write_log("Ошибка: " . $errors['phpversion']);
        }

        // проверка значений обязательных директив в php.ini
        $arr_ini = array(
			'allow_url_fopen' => 1,
            'safe_mode' => 0,
            'short_open_tag' => 1,
            'zend.ze1_compatibility_mode' => 0
		);
        foreach ($arr_ini as $key => $value) {
            if (intval(ini_get($key)) != $value)
                if (strtolower(ini_get($key)) != ($value ? 'on' : 'off')) {
                    echo $key . " - " . $value;
                    $errors[$key] = "Необходимо " . ($value ? "включить " : "отключить ") . "параметр " . $key . ".";
                    fx_write_log("Ошибка: " . $errors[$key]);
                }
        }
        if (intval(ini_get('memory_limit')) < 24) {
            $errors['memory_limit'] = "Необходимо увеличить параметр memory_limit.";
            fx_write_log("Ошибка: " . $errors['memory_limit']);
        }

        // 1 необязательная директива
        if (intval(ini_get('mbstring.func_overload')) != 0) {
            $notices['mbstring.func_overload'] = "Необходимо выключить параметр mbstring.func_overload.";
            fx_write_log("Предупреждение: " . $notices['mbstring.func_overload']);
        }
		
		$extensions_name = array(
			'mysql' => 'MySQL',
			'session' => 'Session',
			'mbstring' => 'MBstring'/*,
			'iconv' => 'iconv',
			'tokenizer' => 'Tokenizer',
			'ctype' => 'Ctype',
			'dom' => 'DOM',
			'json' => 'JSON',
			'libxml' => 'libxml',
			'simplexml' => 'SimpleXML',
			'curl' => 'cURL',
			'gmp' => 'GMP'*/
		);
		
        // проверка наличия обязательных расширений
        $arr_ext = array('session', 'mysql');
        foreach ($arr_ext as $ext) {
            if (!extension_loaded($ext)) {
                $errors[$ext] = "Не хватает расширения " . ( isset($extensions_name[$ext]) ? $extensions_name[$ext] : $ext ) . ".";
                fx_write_log("Ошибка: " . $errors[$ext]);
            }
        }

        // проверка наличия "необязательных" расширений
        $arr_ext_opt = array('mbstring', 'iconv'/*, 'ctype', 'curl', 'dom', 'gd', 'json', 'libxml', 'simplexml', 'gmp', 'tokenizer'*/);
        foreach ($arr_ext_opt as $ext) {
            if (!extension_loaded($ext)) {
                $notices[$ext] = "Не хватает расширения " . ( isset($extensions_name[$ext]) ? $extensions_name[$ext] : $ext ) . ".";
                fx_write_log("Предупреждение: " . $notices[$ext]);
            } else if ($ext == 'gd') {
                if (function_exists('gd_info')) {
                    $gd = gd_info();
                    preg_match('/\d/', $gd['GD Version'], $match);
                    $gd = $match[0];
                    if ($gd < 2) {
                        $notices['gd'] = "Расширения gd имеет версию ниже 2.0.0.";
                        fx_write_log("Предупреждение: " . $notices['gd']);
                    }
                } else {
                    $notices['gd'] = "Не удалось выяснить версию расширения gd.";
                    fx_write_log("Предупреждение: " . $notices['gd']);
                }
            }
        }

        $dir = dirname($_SERVER['PHP_SELF']);
        $test_dir = "testdirtestdir";
        if (!@mkdir($test_dir)) {
            $errors['mkdir'] = "Нет прав на создание директорий.";
            fx_write_log("Ошибка: " . $errors['mkdir']);
        }
        if (!is_writable($test_dir)) {
            $errors['is_writable'] = "Нет прав на изменение директорий.";
            fx_write_log("Ошибка: " . $errors['is_writable']);
        }
        if (!@rmdir($test_dir)) {
            $errors['rmdir'] = "Нет прав на удаление директорий.";
            fx_write_log("Ошибка: " . $errors['rmdir']);
        }

        if (empty($errors)) {
            if (empty($notices))
                fx_write_log("Этап №1: Проверка параметров завершена. Все параметры подходят.");
            if (!empty($notices))
                fx_write_log("Этап №1: Проверка параметров завершена.");
            $result = fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': '#444', 'opacity': 1", "можно начинать"), false, array(1, 0));
            $result .= "Ваш хостинг" . ( empty($notices) ? " прекрасно" : "" ) ." подходит для работы Floxim. Теперь я проверю базу данных MySQL. Введите параметры доступа к ней. Если вы их не знаете, обратитесь к сотруднику службы поддержки или системному администратору. В случае, когда вы используете обычный хостинг, эти параметры, скорее всего, были присланы вам по электронной почте после регистрации на сайте хостинг-провайдера.";
            
        	if ( !empty($notices) ) {
				$result .= "<br /><br />Эти предупреждения нужно учесть, чтобы обеспечить корректную работу системы в будущем:<br /><div style='margin: 7px 0; font-style: italic; color: orange;'>" . join("<br />", $notices) . "</div>";
				$result .= "Тем не менее система может быть установлена, но её функциональность может быть ограничена.";
			}
            
            $result .= "<div class='db'>";
            $opt_html = "
            <div class='cell'>
                <span class='item'>Хост (host)</span><br />
                <input type='text' name='host' id='host' value='" . (fx_post_get('host') ? fx_post_get('host') : "localhost") . "' />
            </div>
                    <div class='cell'>
                <span class='item'>Имя БД (name)</span><br />
                <input type='text' name='dbname' id='dbname' value='" . fx_post_get('dbname') . "' />
            </div>
            <div class='cell'>
                <span class='item'>Логин (user)</span><br />
                <input type='text' name='user' id='user' value='" . fx_post_get('user') . "' />
            </div>
            <div class='cell'>
                <span class='item'>Пароль (password)</span><br />
                <input type='password' name='pass' id='pass' value='" . fx_post_get('pass') . "' />
            </div>";
            $result .= fx_html_form(2, 'Проверить БД', $opt_html);
            $result .= "</div>";
        }
        else {
            $result = fx_html_status_bar(array("'color': 'red', 'opacity': 1", "возникли проблемы!"));
            $result .= "<span style='font-style: italic; padding: 10pt; color: red;'>" . join(" ", $errors) . "</span><br /><br />Эти проблемы не позволяют использовать Floxim на вашем хостинге. Если вы не можете решить их самостоятельно, перешлите их описание сотруднику технической поддержки или системному администратору. Извините, но это зависит не от меня. Я очень надеюсь, что эти проблемы можно решить, и тогда установку можно будет произвести еще раз. ";
        }

        echo $result;
    break;
    case 2:
        // Проверка настроек БД
        if (!$ajax)
            fx_html_beg();
        fx_write_log("Этап №" . $action . ": Начало проверки БД.");
        $error = "";
        $LinkID = fx_connect_db();
        // проверка соединения с БД
        if (!$LinkID) {
            $error = "Нет соединение с указанной БД. MySQL ошибка: " . mysql_error();
            fx_write_log($error);
            echo fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': 'red', 'opacity': 1", "возникли проблемы!"), false, array(1, 0));
            echo "Не удалось произвести подключение к указанной БД, возможно были указаны не все параметры подключения. Проверьте правильность и повторите попытку. Подробнее об ошибке читайте <a href='log.txt' target='_blank'>в логе</a>.";
            echo fx_html_form(1, "Вернуться и повторить");
            break;
        }
        else
            fx_write_log("Соединение с БД успешно установлено.");

        // проверка версии MySQL
        if (version_compare(mysql_get_server_info(), "4.1.0") == '-1') {
            $error = "Версия MySQL ниже требуемой. Обратитесь, пожалуйста, к системному администратору или хостинг-провайдеру для решения этой проблемы.";
            fx_write_log($error);
            echo fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': 'red', 'opacity': 1", "возникли проблемы!"), false, array(1, 0));
            echo $error;
            break;
        }

        // проверка прав заданного пользователя на действия в БД
        if (!fx_check_user_grants()) {
            echo fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': 'red', 'opacity': 1", "возникли проблемы!"), false, array(1, 0));
            echo "Указанный вами пользователь не имеет прав записи в базу данных. Может, есть какой-то другой? Обратитесь в службу поддержки вашего хостинг-провайдера или к системному администратору, чтобы решить эту проблему.";
            echo fx_html_form(1, 'Вернуться');
            break;
        }

        // найдены таблицы Floxim
        $deltables = fx_post_get('deltables');
        if ($deltables) {
            $repeatTables = fx_repeat_tables();
            foreach ($repeatTables as $d_t) {
                $query = "DROP TABLE `" . $d_t . "`";
                fx_query($query);
            }
        }

        if (fx_repeat_tables()) {
            $error = "В указанной БД найдены таблицы Floxim.";
            fx_write_log($error);
            echo fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': 'red', 'opacity': 1", "внимание!"), false, array(1, 0));
            echo "В указанной вами базе данных уже есть таблицы, которые хочет создать установщик. Хотите удалить их и продолжить установку?";
            echo fx_html_form(2, 'Удалить и продолжить', "<input type='hidden' id='deltables' name='deltables' value='1' />");
            break;
        }

        fx_write_log("Этап №" . $action . ": Проверка параметров БД успешно завершена.");
        echo fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': 'green', 'opacity': 1", "всё прекрасно!"), array("'color': '#444', 'opacity': 1", "осталось совсем немного!"), array(1, 1));
        echo "<div id='settings'>" . fx_html_settings() . "</div>";
    break;
    case 3:
        // Установка выбранной редакции
        if (!$ajax)
            fx_html_beg();
        fx_write_log("Этап №" . $action . ": Установка дистрибутива.");
        $errors = array();
        
        if ( !( fx_post_get('pwd') && fx_post_get('email') ) ) {
            fx_write_log('Не введены данные администратора системы.');
            echo fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': 'green', 'opacity': 1", "всё прекрасно!"), array("'color': 'red', 'opacity': 1", "возникла проблема!"), array(1, 1));
            echo "Не введены данные администратора системы. Вернитесь и введите эти данные.";
            echo fx_html_form(2, 'Вернуться');
            break;
        }
        
        echo "Установка может занять от нескольких секунд до нескольких минут, все зависит от произвоительности вашего сервера.<br />
        <ul>";
        @ob_flush();
        @flush();
        $notify['success0'] = "<script>$('#timer0').html('<span style=\'color: green;\'>готово!</span>');</script>";
        $notify['error0'] = "<script>$('#timer0').html('<span style=\'color: red;\'>ошибка!</span>');</script>";
        $notify['success1'] = "<script>$('#timer1').html('<span style=\'color: green;\'>готово!</span>');</script>";
        $notify['error1'] = "<script>$('#timer1').html('<span style=\'color: red;\'>ошибка!</span>');</script>";
        $notify['success2'] = "<script>$('#timer2').html('<span style=\'color: green;\'>готово!</span>');</script>";
        $notify['error2'] = "<script>$('#timer2').html('<span style=\'color: red;\'>ошибка!</span>');</script>";

        echo "<li>Разворачиваю базу данных... <span id='timer2'></span></li>";
        @ob_flush();
        @flush();
        if ( !fx_install_db() ) {
            echo fx_html_status_bar(array("'color': 'green', 'opacity': 1", "всё отлично!"), array("'color': 'green', 'opacity': 1", "всё прекрасно!"), array("'color': 'red', 'opacity': 1", "возникла проблема!"), array(1, 1));
            $errors['db'] = "При установке базы данных возникла проблема.";
            fx_write_log($errors['db']);
            echo $notify['error2'];
            $message['db'] = $errors['db'];
            echo "</ul>";
            echo $message['db'];
            echo fx_html_form(4, 'Повторить');
            break;
        }
        echo $notify['success2'];
        @ob_flush();
        @flush();
        $errors['db'] = "База данных успешно развернута.";
        fx_write_log($errors['db']);

        echo "</ul>";
        @ob_flush();
        @flush();
        
        // create config.php
        fx_write_config($FLOXIM_FOLDER, $CUSTOM_FOLDER);
        // update .htaccess
        fx_change_htaccess($FLOXIM_FOLDER, $CUSTOM_FOLDER);
        
        echo fx_html_form('4', 'Завершить установку');
    break;
    case 4:
        // Floxim установлен
        if (!$ajax) fx_html_beg();
        
        $dir = $_SERVER['HTTP_HOST'].$CUSTOM_FOLDER;
        
        $result = fx_html_status_bar(
          array("'color': 'green', 'opacity': 1", "всё отлично!"),
          array("'color': 'green', 'opacity': 1", "всё прекрасно!"),
          array("'color': 'green', 'opacity': 1", "готово!"),
          array(1, 1)
        );
        $result.= "Ура, Floxim установлен, теперь вы можете начать работу. Смотрите:<br />".
          "<ul>".
          "<li>Адрес сайта: <a href='http://".$dir."' target='_blank'>http://".$dir."</a></li>".
          "<li>Логин для <a href='http://".$dir."/floxim/'>входа</a>: <strong>admin</strong></li>".
          "<li>Пароль: тот, который вы ввели ".( fx_post_get('email') ? "(и еще он выслан на e-mail <a href='mailto:".fx_post_get('email')."'>".fx_post_get('email')."</a>)" : "" )."</li>".
          "</ul>".
          "<br />".
          "<em style='color: red;'>Обязательно удалите папку install с сервера!</em>";
        echo $result;
        fx_mail_to_user($dir);
    break;
}
fx_html_end();

function fx_html_beg() {
    echo <<<HEREDOC
<!doctype html>
<html>
<head>
    <title>Установка Floxim</title>
    <meta http-equiv="Content-Language" content="ru" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>
</head>
<body>
    <div class='main'>       
            <div class='top'>Установщик Floxim</div>
            <div class='status_bar'>
                <div class='pair'>
                    <span id='item0'>Проверка хостинга</span><br />
                    <span id='status0'>можно приступать</span>
                </div>
                <div id='arrow0'>&rarr;</div>
                <div class='pair'>
                    <span id='item1' >Проверка базы данных</span><br />
                    <span id='status1' >еще не производилась</span>
                </div>
                <div id='arrow1'>&rarr;</div>
                <div class='pair'>
                    <span id='item2' >Установка Floxim</span><br />
                    <span id='status2'>сначала все проверим</span>
                </div>
                <div style='clear:both;'></div>
            </div>
            <div id='content'>
HEREDOC;
}

function fx_html_end() {
    $html = "</div>
    </div>
<script type='text/javascript'>
    $(document).ready(function() {
      $('#main_form').submit(function() {
        var ajax = $('#ajax').val();
        if (ajax) {
            var q = $('#main_form').serialize();
            var request = $.ajax({
              type: 'POST',
              data: q,
              dataType: 'html',
              cache: false,
              success: function(data) {
                    $('#content').html(data);
                }
            });
            return false;
        }
      });
      
	$('#show_block1').click(function(){        
		if ($('#show_block1').is('.nonselected_tab')) {
			$('#show_block1').removeClass('nonselected_tab'); 
			$('#show_block1').addClass('selected_tab'); 

			$('#show_block2').removeClass('selected_tab'); 
			$('#show_block2').addClass('nonselected_tab');

			$('#block1').removeClass('hidden');    
			$('#block2').addClass('hidden');

			$('#code').val('');
			$('#code_valid').html('');
			$('#regnum').val('');
			$('#regnum_valid').html('');
		}
		
		return false;
	});
            
	$('#show_block2').click(function() {        
		if ($('#show_block2').is('.nonselected_tab')) {
			$('#show_block2').removeClass('nonselected_tab'); 
			$('#show_block2').addClass('selected_tab'); 

			$('#show_block1').removeClass('selected_tab'); 
			$('#show_block1').addClass('nonselected_tab'); 

			$('#block2').removeClass('hidden');
			$('#block1').addClass('hidden');

			for(var i = 1; i < 6; i++) {
			$('#db' + i).attr('checked', null);
			}
		}
		
		return false;
	});
        
	$('#pwd_gen').click( function() {
		var sign = ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 
		'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm', 'Q', 'W',
		'E', 'R', 'T', 'Y', 'U', 'I', 'O', 'P', 'A', 'S', 'D', 'F', 'G', 'H', 
		'J', 'K', 'L', 'Z', 'X', 'C', 'V', 'B', 'N', 'M', '1', '2', '3', '4', 
		'5', '6', '7', '8', '9', '0'];            
		var result = '';
		var end = sign.length - 1;
		for(var i = 0; i < 10; i++) {
		result += sign[Math.floor(Math.random()*end)];
		}
		$('#pwd_field').val(result);
		return false;
	});
                
        $('#pwd_show').change( function() {
          fx_show_hide_pwd('#pwd_field', this.checked);
        }); 
        
        $('#email_field').focusout(function(){
          fx_check_email($('#email_field').val(), '#email_field');
        });
    });
    
    function fx_show_hide_pwd(id, checked) {
        checked ? fx_show_pwd(id) : fx_hide_pwd(id);
    }
            
    function fx_show_pwd(id) {
        var id_clear = id+'_clear';
        $(id_clear).removeClass('hidden'); 
        $(id).addClass('hidden');
        $(id_clear).val($(id).val());
    }
            
    function fx_hide_pwd(id) {
      var id_clear = id+'_clear';
      $(id).removeClass('hidden'); 
      $(id_clear).addClass('hidden');
      $(id).val($(id_clear).val());
    }
    
    function fx_check_email(value, id) {
        var email = value;
        if(email != '') {
          if (fx_is_valid_email_address(email) == false) {
            $('#email_valid').html('<span style=\'color: red;\'>не похоже на настоящий email</span>');
          } 
          else {
            $('#email_valid').html('');
          }
        } 
        else {
          $('#email_valid').html('');
        }
    }
    
    function fx_is_valid_email_address(emailAddress) {
        var pattern=/[0-9a-z_]+@[0-9a-z_]+\.[a-z]{2,5}/i;
        return pattern.test(emailAddress);
    }
</script>   
</body>         
</html>";
    echo $html;
}

function fx_html_status_bar($array1 = array(), $array2 = array(), $array3 = array(), $array4 = array()) {
    return "<script type='text/javascript'>
    " . (!empty($array1) && $array1[0] ? "$('#item0, #status0').css({" . $array1[0] . "});" : "") . "
    " . (!empty($array1) && $array1[1] ? "$('#status0').html('" . $array1[1] . "');" : "") . "
    " . (!empty($array4) && $array4[0] ? "$('#arrow0').css({'color': '#444', 'opacity': 1});" : "") . "
    " . (!empty($array2) && $array2[0] ? "$('#item1, #status1').css({" . $array2[0] . "});" : "") . "
    " . (!empty($array2) && $array2[1] ? "$('#status1').html('" . $array2[1] . "');" : "") . "
    " . (!empty($array4) && $array4[1] ? "$('#arrow1').css({'color': '#444', 'opacity': 1});" : "") . "
    " . (!empty($array3) && $array3[0] ? "$('#item2, #status2').css({" . $array3[0] . "});" : "") . "
    " . (!empty($array3) && $array3[1] ? "$('#status2').html('" . $array3[1] . "');" : "") . "
    </script>";
}

function fx_html_form($action, $button, $opt = '') {
    global $ajax;
    $html = "<form method='post' action='" . $_SERVER['SCRIPT_NAME'] . "' name='main_form' id='main_form' >";
    $html .= "<input type='hidden' name='action' id='action' value='" . $action . "' />";
    $params = array("host", "user", "dbname", "pass", "pwd", "email", "template", "color_var", "demodistr", "regnum", "code");
    foreach ($params as $param) {
        $html .= "<input type='hidden' name='" . $param . "' value='" . fx_post_get($param) . "' />";
    }
    $html .= "<input type='hidden' name='ajax' id='ajax' value='" . $ajax . "' />";
    $html .= $opt . "<input type='submit' name='submit_button' id='submit_button' value='" . $button . "' /></form>";

    return $html;
}

function fx_query($query) {
    global $LinkID;
    $res = mysql_query($query, $LinkID);
    if (mysql_error()) {
        print mysql_error() . "\n<br /><strong>Query:</strong> <pre>$query</pre>\n";
        return false;
    } else {
        return $res;
    }
}

function fx_post_get($param) {
    if (empty($_GET) && empty($_POST))
        return false;
    if ($param) {
        return array_key_exists($param, $_GET) ? $_GET[$param] : (array_key_exists($param, $_POST) ? $_POST[$param] : "");
    }
    else
        return array_merge($_POST, $_GET);
}

function fx_connect_db() {
    global $LinkID;
    $host = fx_post_get('host');
    $user = fx_post_get('user');
    $pass = fx_post_get('pass');
    $dbname = fx_post_get('dbname');

    $LinkID = @mysql_connect($host, $user, $pass);
    if (!$LinkID)
        return false;

    if (!mysql_select_db($dbname, $LinkID)) {
        fx_write_log('Не удалось произвести подключение к указанной БД');
        return false;
    }
    mysql_query("SET NAMES 'utf8'");
    mysql_query("ALTER DATABASE `" . $dbname . "` DEFAULT CHARACTER SET 'utf8'");

    return $LinkID;
}

function fx_check_user_grants() {
    $sql_create = "CREATE TABLE test_table (ID int(11) NOT NULL, FIO varchar(128) NOT NULL)";
    $sql_drop = "DROP TABLE `test_table`";
    $error = false;
    if (!(fx_query($sql_create) && fx_query($sql_drop)))
        $error = true;
    if ($error)
        return false;
    else
        return true;
}

function fx_html_settings() {
    return fx_html_form(3, 'Установить Floxim', fx_html_config());
}

function fx_html_config() {
    $html = "<div class='pwd_create'>
                    Введите пароль пользователя <strong>admin</strong>, для входа в систему администрирования системы:<br />
                    <input type='password' name='pwd' id='pwd_field' />
                    <a href='#' id='pwd_gen' class='selected_tab'>сгенерировать пароль</a>
                    <div>
						<input type='checkbox' name='show_pwd' value='1' id='show_pwd' onclick='document.getElementById(\"pwd_field\").type = (this.checked ? \"text\" : \"password\");' />
                        <label for='show_pwd'>Показать пароль</label>
                    </div>
                </div>
                <div class='email_form'>
                    E-mail администратора:<br />
                    <input class='email' type='text' name='email' id='email_field' />
                    <span id='email_valid' class='valid'></span>
                </div>";

    return $html;
}

function fx_standardize_path_to_file($path) {
    return rtrim(fx_standardize_path_to_folder($path), '/');
}

function fx_standardize_path_to_folder($path) {
    $path = str_replace('\\', '/', $path);
    while (!(strpos($path, '//') === false)) {
        $path = str_replace('//', '/', $path);
    }
    return rtrim($path, '/') . '/';
}

function fx_install_db($dir = 'sql/', $level = 0) {
    
    if (!$level) fx_connect_db();
    
    $content = false;
    $v4 = false;
    $all = false;
    
    $install_type = 0;
    
    $system_arr = array();
    $module_arr = array();
    
    // clear system
    $system_arr[0] = array('core');

	for ($i = 0; $i <= $install_type; $i++):
		foreach ($system_arr[$i] as $row):    
			$file_name = $row . '.sql';
			$file = fx_standardize_path_to_file( dirname(__FILE__) . '/' . $dir . $file_name );
			if ( file_exists($file) ) {
				if ( !fx_exec_sql($file) ) {
					fx_write_log('Возможно система уже установлена: ' . $file);
					return false;
				}
			}
		endforeach;
    endfor;
    
	if (!$level) {
		fx_update_db();
		fx_write_log('Распаковка базы данных прошла успешно.');
	}

    return true;
}

function fx_update_db() {
	// update data
    $sql = array(
		"UPDATE `fx_site` SET `domain` = '" . mysql_real_escape_string($_SERVER['HTTP_HOST']) . "' WHERE `id` = 15",
		"UPDATE `fx_site` SET `domain` = '" . mysql_real_escape_string('alt.'.$_SERVER['HTTP_HOST']) . "' WHERE `id` = 1",
		"UPDATE `fx_content_user` SET `password` = '" . md5($_SESSION['pwd']) . "', `email` = '" . mysql_real_escape_string( fx_post_get('email') ) . "' WHERE `login` = 'admin'",
		"UPDATE `fx_settings` SET `value` = '" . mysql_real_escape_string( fx_post_get('email') ) . "' WHERE `key` = 'spam_from_email'"
	);
	
    foreach ($sql as $query) {
        fx_query($query);
    }
}

function fx_get_cattables_for_lower_case() {
    return array_map('strtolower', fx_get_cattables());
}

function fx_get_cattables() {
    $cattables = array(
		'fx_auth_external',
		'fx_auth_user_relation',
		'fx_classificator',
		'fx_classificator_cities',
		'fx_classificator_country',
		'fx_classificator_region',
		'fx_component',
		'fx_content',
		'fx_content_blogpost',
		'fx_content_gallery',
		'fx_content_page',
		'fx_content_photo',
		'fx_content_section',
		'fx_content_tag',
		'fx_content_tagpost',
		'fx_content_text',
		'fx_content_travel_route',
		'fx_content_user',
		'fx_controller',
		'fx_crontask',
		'fx_ctpl',
		'fx_datatype',
		'fx_dictionary',
		'fx_field',
		'fx_filetable',
		'fx_group',
		'fx_history',
		'fx_history_item',
		'fx_infoblock',
		'fx_infoblock_visual',
		'fx_layout',
		'fx_mail_template',
		'fx_menu',
		'fx_module',
		'fx_multiselect',
		'fx_patch',
		'fx_permission',
		'fx_redirect',
		'fx_session',
		'fx_settings',
		'fx_site',
		'fx_template',
		'fx_user_group',
		'fx_widget'
	);

    return $cattables;
}

function fx_get_users_tables() {
    $tables = array();
    $showt = mysql_query("SHOW TABLES");
    while ($row = mysql_fetch_array($showt)) {
        $tables[] = $row[0];
    }

    return $tables;
}

function fx_is_lower_case_table_names() {
    $res = fx_query("SHOW VARIABLES LIKE 'lower_case_table_names'");
    $var = mysql_fetch_row($res);
    return ($var[1] == 1 ? TRUE : FALSE);
}

function fx_repeat_tables() {
    $tables = array();
    $user_tables = fx_get_users_tables();
    if (fx_is_lower_case_table_names()) {
        $tables = array_intersect(fx_get_cattables_for_lower_case(), $user_tables);
    }
    else {
        $tables = array_intersect(fx_get_cattables() , $user_tables);
    }
    
    return $tables;
}

function fx_mail_to_user($path) {
    $mail = fx_post_get('email');
    $pwd = fx_post_get('pwd');

    $subject = 'Floxim успешно установлен';
    $message = 'Поздравляю!

Вы успешно установили Floxim на сайт http://' . $path . '.
    
Ваши авторизационные данные для доступа на сайт: 
- адрес: http://' . $path . '
- логин: admin 
' . ($pwd ? '- пароль: ' . $pwd : '- вход в систему без пароля') . '
    
Спасибо, что выбрали нашу систему!

С уважением, 
Скрипт Установки Floxim';

    $headers = "Content-type: text/plain; charset=UTF-8 \r\n";
    $headers .= "From: info@{$_SERVER['SERVER_NAME']}\r\n";
    @mail($mail, $subject, $message, $headers);
}

function fx_write_log($message, $time = true) {
    return null;
    $message_time = "";
    if ($time) {
        $message_time = date("H:i:s d.m.Y") . ' ';
        if (substr($message, -1) != '.')
            $message = $message . '.';
    }
    $result_str = PHP_EOL . $message_time . $message;

    file_put_contents("log.txt", $result_str, FILE_APPEND);
}

function fx_func_enabled($function) {
    $function = strtolower(trim($function));
    if ($function == '')
        return false;
    $dis_functions = array();
    $dis_functions = explode(",", @ini_get("disable_functions"));
    if (!empty($dis_functions))
        $dis_functions = array_map('trim', array_map('strtolower', $dis_functions));
    if (function_exists($function) && is_callable($function) && !in_array($function, $dis_functions))
        return true;
    else
        return false;
}

function fx_exec_sql($file) {
    $fp = fopen($file, "r");
    if (!$fp) {
        fx_write_log('Не удалось открыть sql-файл: ' . $file);
        return false;
    }
    $i = 0;
    while (!feof($fp)) {
        $statement = chop(fgets($fp, 65536));
        if (strlen($statement)) {
            while (substr($statement, strlen($statement) - 1, 1) <> ";" && substr($statement, 0, 1) <> "#" && substr($statement, 0, 2) <> "--")
                $statement .= chop(fgets($fp, 65536));
            if (substr($statement, 0, 1) <> "#" && substr($statement, 0, 2) <> "--") {
                if (!fx_query($statement)) {
                    fx_write_log('Неудачное выполнение запроса в файле ' . $file . ' MySQL ошибка: ' . mysql_error());
                }
            }
        }
    }

    fclose($fp);
    return true;
}

function fx_get_config($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME) {
    ob_start();
    ?>
define("FX_JQUERY_PATH", '/floxim/lib/js/jquery-1.9.1.js');
$db_config = array(
    'default' =>  array(
        'DB_DSN' => 'mysql:dbname=<?=$MYSQL_DB_NAME?>;host=<?=$MYSQL_HOST?>',
        'DB_USER' => '<?=$MYSQL_USER?>',
        'DB_PASSWORD' => '<?=$MYSQL_PASSWORD?>'
    )
);

return $db_config['default'];
<?php
    $cfg_file = ob_get_clean();
	
	$cfg_file = '<?php'.PHP_EOL.PHP_EOL.$cfg_file;
	$cfg_file = $cfg_file.'?>';
	
    return $cfg_file;
}

function fx_write_config($distr_dir, $custom_dir) {
    
    $config_path = $distr_dir . 'config.php';
    
    $cfg_file = fx_get_config( fx_post_get('host'), fx_post_get('user'), fx_post_get('pass'), fx_post_get('dbname') );
    
    if ( is_writable($distr_dir) ) {
      file_put_contents($config_path, $cfg_file);
    } else {
		fx_write_log('Конфигурационный файл config.php недоступен для записи. Установите нужные права на этот файл и повторите установку заново.');
	}
}

function fx_change_htaccess($distr_dir, $custom_dir) {
    // nothing else changed
    if (!$custom_dir) return false;
    
    // path to the .htaccess
    $htaccess_path = $distr_dir . '.htaccess';
    // file not exist
    if ( !is_file($htaccess_path) ) {
      return false;
    }
    
    // file content
    $htaccess = file($htaccess_path);
    
    // walk
    foreach ($htaccess as $num => $str) {
      if (strpos($str, 'ErrorDocument 404') === 0) {
        $htaccess[$num] = "ErrorDocument 404 ".rtrim($custom_dir, '/')."/floxim/require/e404.php\n\r";
      }
      if (strpos($str, 'RewriteRule ^(.+)$') === 0) {
        $htaccess[$num] = "RewriteRule ^(.+)$ ".rtrim($custom_dir, '/')."/floxim/require/e404.php?REQUEST_URI=$1 [L,QSA]\n\r";
      }
    }
    
    // update file content
    if ( is_writable($htaccess_path) ) {
      file_put_contents( $htaccess_path, join('', $htaccess) );
    }
}

?>