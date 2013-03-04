<?php
error_reporting(E_ALL & ~(E_NOTICE | E_STRICT));

$installer = new fx_install();
$installer->process($_REQUEST);

class fx_install {

    protected $lang = array();

    public function process($input) {
        $this->load_lang($input['language'] ? $input['language'] : 'ru');

        $ajax_request = isset($input['ajax_request']) ? $input['ajax_request'] : false;

        if ($ajax_request) {
            $step = $input['step'];
            if ($step) {
                $result['status'] = 'ok';

                // проверка текущего шага
                $check_func = 'check_step_' . $step;
                if (method_exists($this, $check_func)) {
                    $result = call_user_func(array($this, $check_func), $input);
                }

                // предыдущий шаг прошел удачно, переходим к следующему
                if ($result['status'] == 'ok') {
                    $next_step = $this->get_next_step($step);
                    $result['step'] = $next_step;
                    $result['content'] = call_user_func(array($this, 'show_step_' . $next_step), $input);
                }
            } else if ($input['action']) {
                $action = $input['action'];
                $result = call_user_func(array($this, 'make_action_' . $action), $input);
            } else {
                $result['status'] = 'error';
            }

            echo json_encode($result);
        } else {
            header("Content-Type: text/html; charset=utf-8");
            $this->show_base_form();
        }
    }

    protected function get_next_step($current_step) {
        $steps = array('language', 'hosting', 'info', 'install', 'configure', 'design', 'install_site', 'final');
        $flip = array_flip($steps);
        return $steps[$flip[$current_step] + 1];
    }

    protected function show_step_language() {
        $html = "<h1>" . $this->lang['lang_select'] . "</h1>";
        $html .= "<select name='language'><option value='ru'>Русский</option><option value='en'>English</option></select>";

        return $html;
    }

    protected function show_step_hosting() {
        $errors = $this->check_hosting();
        if (!$errors) {
            $html = "<script type='text/javascript'> next(); </script>";
        } else {
            $html = join('<br/>', $errors);
        }

        return $html;
    }

    protected function show_step_info() {
        $domain = str_replace(array('http:', '/'), '', $_SERVER['HTTP_HOST']);

        $html = "<div class='col1'>";
        $html .= "<h1>" . $this->lang['profile'] . "</h1>";
        $html .= $this->form_input('sitename', $this->lang['sitename'], $domain);
        $html .= $this->form_input('email', $this->lang['email'], 'admin@' . $domain);
        $html .= $this->form_input('login', $this->lang['login'], 'admin');
        $html .= $this->form_password('password', $this->lang['password']);
        $html .= $this->form_password('password2', $this->lang['repassword']);
        $html .= "</div>";

        $html .= "<div class='col2'>";
        $html .= "<h1>" . $this->lang['db'] . "</h1>";
        $html .= $this->form_input('db[host]', $this->lang['dbhost'], 'localhost');
        $html .= $this->form_input('db[user]', $this->lang['dbuser'], 'root');
        $html .= $this->form_input('db[password]', $this->lang['dbpassword'], '');
        $html .= $this->form_input('db[name]', $this->lang['dbname'], 'floxim');
        $html .= $this->form_input('db[prefix]', $this->lang['dbprefix'], 'fx');
        $html .= "</div>";

        $html .="<div class='cl'></div>";

        return $html;
    }

    protected function show_step_install() {
        $html = '
    <h1>Установка</h1>
    <label id="remark">' . $this->lang['preparation'] . '</label>
    <div id="progressbar" class="ui-progressbar ui-widget ui-widget-content ui-corner-all"></div>
    <script type="text/javascript">
        var lang = ' . json_encode($this->lang['js']) . ';
        install (' . $this->is_distrib_exist() . ');
    </script>';
        return $html;
    }

    protected function show_step_configure() {
        if (defined('SITE_STORE_ID') ) {
            $html = '<input type="hidden" name="site" value="'.SITE_STORE_ID.'" />';
            $html .= "<script type='text/javascript'> next(); </script>";
        }
        else {
            $this->load_system();

            $store = new fx_admin_store();
            $result = $store->get_items('site');

            $html = "<h1>" . $this->lang['select_site'] . "</h1>";
            $html .= "<label><input type='radio' name='site' value='-1' class='radio' checked='checked'>" . $this->lang['default_site'] . "</label><br/>";

            $html .= "<h2>" . $this->lang['site_store'] . "</h2>";
            foreach ($result['items'] as $item) {
                $html .= "<label><input type='radio' name='site' value='" . $item['store_id'] . "' class='radio'>" . $item['name'] . "</label><br/>";
            }
        }

        $html .= "<script type='text/javascript'>$('#next').show();</script>";
        return $html;
    }

    protected function show_step_design() {
        if ( defined('DESIGN_STORE_ID') ) {
            $html = '<input type="hidden" name="design" value="'.DESIGN_STORE_ID.'" />';
            $html .= "<script type='text/javascript'> next(); </script>";
        }
        else {
            $checked = ' checked="checked" ';
            $this->load_system();

            $html = "<h1>" . $this->lang['select_design'] . "</h1>";

            $templates = fx_core::get_object()->template->get_all('type', 'parent');
            foreach ($templates as $item) {
                $html .= "<label><input type='radio' name='design' value='" . $item['id'] . "' class='radio' $checked>" . $item['name'] . "</label><br/>";
                $checked = '';
            }

            $store = new fx_admin_store();
            $result = $store->get_items('design');

            $html .= "<h2>" . $this->lang['design_store'] . "</h2>";
            foreach ($result['items'] as $item) {
                $html .= "<label><input type='radio' name='design' value='" . $item['store_id'] . "' class='radio'>" . $item['name'] . "</label><br/>";
                $checked = '';
            }
        }
        return $html;
    }

    protected function show_step_install_site() {
        $html = '
    <h1>Установка сайта</h1>
    <label id="remark_site">' . $this->lang['preparation'] . '</label>
    <div id="progressbar_site" class="ui-progressbar ui-widget ui-widget-content ui-corner-all"></div>
    <script type="text/javascript">
        install_site();
    </script>';
        return $html;
    }

    protected function show_step_final() {
        $html = '<h1>' . $this->lang['final'] . '</h1>' . $this->lang['final_text'];
        return $html;
    }

    protected function is_distrib_exist() {
        return file_exists("../vars.inc.php");
    }

    protected function load_system() {
        $dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

        $essence = 'page';
        $action = 'blank';
        require_once $dir . "/vars.inc.php";
        require_once $dir . "/floxim/index.php";
    }

    protected function show_base_form() {
        if ($this->is_distrib_exist()) {
            $install_js = 'resources/install.js';
            $install_css = 'resources/install.css';
            $logo = 'resources/logo.png';
        } else {
            $install_js = 'http://floxim.org/floxim/modules/distrib/resources/install.js';
            $install_css = 'http://floxim.org/floxim/modules/distrib/resources/install.css';
            $logo = 'http://floxim.org/floxim/modules/distrib/resources/logo.png';
        }
        ?>
        <html>
            <head>
                <title>Floxim CMS</title>
                <meta http-equiv='content-type' content='text/html; charset=utf-8' />
                <link rel='stylesheet' type="text/css" href="<?= $install_css ?>" />
                <script type="text/javascript" src="<?= $install_js ?>"></script>
            </head>
            <body>
                <div id="header">
                    <img src="<?= $logo ?>" alt="Floxim"/>
                </div>
                <div id="error_text"></div>
                <div id="content">
                    <div class="step">
                        <?= $this->show_step_language() ?>
                    </div>
                </div>
                <div id="control">
                    <button id="next"></button>
                </div>
            </body>
        </html>
        <?php
    }

    protected function form_input($name, $label, $value = '') {
        return "<label>" . $label . "</label><br/><input name='" . $name . "' value='" . $value . "' />";
    }

    protected function form_password($name, $label) {
        return "<label>" . $label . "</label><br/><input type='password' name='" . $name . "' />";
    }

    protected function check_step_info($input) {
        $result = array('status' => 'ok');

        if (!$input['sitename']) {
            $result['status'] = 'error';
            $result['fields'][] = 'sitename';
            $result['warn_text'][] = $this->lang['error_sitename'];
        }
        if (!$input['email']) {
            $result['status'] = 'error';
            $result['fields'][] = 'email';
            $result['warn_text'][] = $this->lang['error_email'];
        }
        if (!$input['login']) {
            $result['status'] = 'error';
            $result['fields'][] = 'login';
            $result['warn_text'][] = $this->lang['error_login'];
        }
        if (!$input['password']) {
            $result['status'] = 'error';
            $result['fields'][] = 'password';
            $result['warn_text'][] = $this->lang['error_pass'];
        }
        if ($input['password'] && $input['password'] != $input['password2']) {
            $result['status'] = 'error';
            $result['fields'][] = 'password2';
            $result['warn_text'][] = $this->lang['error_pass2'];
        }


        $dsn = 'mysql:dbname=' . $input['db']['name'] . ';host=' . $input['db']['host'];
        try {
            $dbh = new PDO($dsn, $input['db']['user'], $input['db']['password']);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $result['status'] = 'error';
            $result['warn_text'][] = $this->lang['error_db'];
        }

        return $result;
    }

    protected function make_action_installsql($input) {
        $dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
        $file = $dir . "/install/" . $input['file'];

        $dsn = 'mysql:dbname=' . $input['db']['name'] . ';host=' . $input['db']['host'];
        try {
            $dbh = new PDO($dsn, $input['db']['user'], $input['db']['password']);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "error";
        }

        $sql = file_get_contents($file);
        $sql = str_replace('%%FX_PREFIX%%', 'fx_', $sql);

        $dbh->exec($sql);
    }

    protected function make_action_download($input) {
        $content = file_get_contents("http://floxim.org/?essence=module_distrib&action=get_tar_file");
        file_put_contents('__floxim__tar.php', $content);

        $content = file_get_contents("http://floxim.org/?essence=module_distrib&action=get_install_distrib");
        file_put_contents('__floxim__distrib.tgz', $content);
    }

    protected function make_action_unpacking($input) {
        require_once "__floxim__tar.php";
        tgz_extract("__floxim__distrib.tgz", ".");

        unlink('__floxim__tar.php');
        unlink('__floxim__distrib.tgz');
    }

    protected function make_action_configedit($input) {
        $dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
        $config_path = $dir . '/vars.inc.php';
        $new_file = '';
        $fp = fopen($config_path, 'r');
        while ($str = fgets($fp)) {
            if (strstr($str, 'DB_DSN') !== false) {
                $dsn = "mysql:dbname=" . $input['db']['name'] . ";host=" . $input['db']['host'];
                $new_file .= "\t" . 'private $DB_DSN = "' . $dsn . '";' . PHP_EOL;
            } else if (strstr($str, 'DB_USER') !== false) {
                $new_file .= "\t" . 'private $DB_USER = "' . $input['db']['user'] . '";' . PHP_EOL;
            } else if (strstr($str, 'DB_PASSWORD') !== false) {
                $new_file .= "\t" . 'private $DB_PASSWORD = "' . $input['db']['password'] . '";' . PHP_EOL;
            } else if (strstr($str, 'DB_PREFIX') !== false) {
                $new_file .= "\t" . 'private $DB_PREFIX = "' . $input['db']['prefix'] . '";' . PHP_EOL;
            } else {
                $new_file .= $str;
            }
        }
        file_put_contents($config_path, $new_file);

        $this->load_system();

        $user = fx_core::get_object()->user->get_by_id(1);
        $user['login'] = trim($input['login']);
        $user['email'] = trim($input['email']);
        $user['password'] = trim($input['password']);
        $user->save();
        
        $user->authorize();
    }

    protected function make_action_import_default($input) {
        $dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

        $this->load_system();

        $import = new fx_import();
        $dir .= '/install/default/';

        if (is_dir($dir)) {
            if (($dh = opendir($dir))) {
                while (($file = readdir($dh)) !== false) {
                    if (is_file($dir . $file) && substr($file, strlen($file) - 3) == 'tgz') {
                        $import->import_by_file($dir . $file);
                    }
                }
                closedir($dh);
            }
        }
    }

    protected function make_action_install_design($input) {
        $this->load_system();
        $design = $input['design'];
        if (strpos($design, 'design.') !== false) {
            $store = new fx_admin_store();
            $file = $store->get_file($design);

            $import = new fx_import();
            $import_result = $import->import_by_content($file);
            $result['template_id'] = $import_result[0]['id'];
        } else {
            $result['template_id'] = intval($design);
        }

        return $result;
    }

    protected function make_action_install_site($input) {
        $this->load_system();
        $site = $input['site'];
        if (strpos($site, 'site.') !== false) {
            $store = new fx_admin_store();
            $file = $store->get_file($site);
        } else {
            $dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
            $path = $dir . '/install/configure/default.tgz';
            $file = file_get_contents($path);
        }

        $import = new fx_import(array('template_id' => $input['template_id']));
        $import->import_by_content($file);

        return $result;
    }

    protected function check_hosting() {
        $errors = array();

        $php_version = phpversion();
        if (version_compare($php_version, "5.1.0") == '-1') {
            $errors[] = sprintf($this->lang['error_php'], $php_version);
        }

        $arr_ini = array('allow_url_fopen' => 1,
            'safe_mode' => 0,
            'short_open_tag' => 1,
            'zend.ze1_compatibility_mode' => 0);
        foreach ($arr_ini as $key => $value) {
            if (intval(ini_get($key)) != $value) {
                if (strtolower(ini_get($key)) != ($value ? 'on' : 'off')) {
                    if ($value) {
                        $errors[] = sprintf($this->lang['error_php_param_on'], $key);
                    } else {
                        $errors[] = sprintf($this->lang['error_php_param_off'], $key);
                    }
                }
            }
        }

        // проверка наличия обязательных расширений
        $arr_ext = array('session', 'mysql', 'curl', 'dom', 'gd', 'iconv', 'json', 'mbstring', 'libxml', 'SimpleXML');
        foreach ($arr_ext as $ext) {
            if (!extension_loaded($ext)) {
                $errors[] = sprintf($this->lang['error_php_ext'], $ext);
            }
        }

        // проверка прав
        if ($this->is_distrib_exist()) {
            $dir = $dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/';
            if (!is_writable($dir . 'vars.inc.php')) {
                $errors[] = sprintf($this->lang['error_php_right'], 'vars.inc.php');
            }
            $dirs = array('floxim_files', 'floxim_components', 'floxim_templates', 'floxim_widgets');
            foreach ($dirs as $v) {
                if (!is_writable($dir . $v)) {
                    $errors[] = sprintf($this->lang['error_php_right'], $v);
                }
            }
        } else {
            $dir = $dir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/';
            if (!is_writable($dir . $v)) {
                $errors[] = sprintf($this->lang['error_php_right'], $dir);
            }
        }

        return $errors;
    }

    protected function load_lang($language = 'en') {
        $lang = array();

        $lang['ru']['lang_select'] = 'Выбор языка';
        $lang['ru']['profile'] = 'Учетная запись';
        $lang['ru']['sitename'] = 'Название сайта';
        $lang['ru']['email'] = 'E-mail';
        $lang['ru']['login'] = 'Логин';
        $lang['ru']['password'] = 'Пароль';
        $lang['ru']['repassword'] = 'Повторите пароль';
        $lang['ru']['db'] = 'База данных';
        $lang['ru']['dbhost'] = 'Хост';
        $lang['ru']['dbuser'] = 'Пользователь';
        $lang['ru']['dbpassword'] = 'Пароль к БД';
        $lang['ru']['dbname'] = 'Название БД';
        $lang['ru']['dbprefix'] = 'Префикс таблиц';
        $lang['ru']['preparation'] = 'Подготовка';
        $lang['ru']['select_site'] = 'Выбор конфигурации';
        $lang['ru']['default_site'] = 'Конфигурация по умолчанию';
        $lang['ru']['site_store'] = 'Конфигурации с FloximStore';
        $lang['ru']['select_design'] = 'Выберите макет';
        $lang['ru']['design_store'] = 'Макеты с FloximStore';
        $lang['ru']['final'] = 'Установка успешно завершена';
        $lang['ru']['final_text'] = '<a href="/">Перейти на сайт</a>';
        $lang['ru']['error_sitename'] = 'Заполните поле с названием сайта';
        $lang['ru']['error_email'] = 'Заполните поле с email';
        $lang['ru']['error_login'] = 'Заполните поле с логином';
        $lang['ru']['error_pass'] = 'Заполните поле с паролем';
        $lang['ru']['error_pass2'] = 'Пароли не совпадают';
        $lang['ru']['error_db'] = 'Не удалось подключиться к базе данных';
        $lang['ru']['error_php'] = 'Версия PHP должна быть не ниже 5.1.0 (текущая версия - %s )';
        $lang['ru']['error_php_param_on'] = 'Необходимо включить параметр "%s"';
        $lang['ru']['error_php_param_off'] = 'Необходимо отключить параметр "%s"';
        $lang['ru']['error_php_ext'] = 'Не хватает расширения "%s"';
        $lang['ru']['error_php_right'] = 'Не хватает прав на запись в директорию/файл "%s"';
        $lang['ru']['js']['download'] = 'Скачиваю дистрибутив...';
        $lang['ru']['js']['unpacking'] = 'Распаковываю дистрибутив...';
        $lang['ru']['js']['dbinstall'] = 'Распаковка БД...';
        $lang['ru']['js']['configedit'] = 'Правка конфигурационного файла...';
        $lang['ru']['js']['importdefault'] = 'Установка стандартных расширений...';
        $lang['ru']['js']['importdesign'] = 'Импорт макета дизайна...';
        $lang['ru']['js']['importsite'] = 'Импорт конфигурации...';

        $lang['en']['lang_select'] = 'Select language';
        $lang['en']['profile'] = 'Admin profile';
        $lang['en']['sitename'] = 'Site name';
        $lang['en']['email'] = 'E-mail';
        $lang['en']['login'] = 'Login';
        $lang['en']['password'] = 'Password';
        $lang['en']['repassword'] = 'Retype password';
        $lang['en']['db'] = 'Data base';
        $lang['en']['dbhost'] = 'Host';
        $lang['en']['dbuser'] = 'User';
        $lang['en']['dbpassword'] = 'Password';
        $lang['en']['dbname'] = 'Data base name';
        $lang['en']['dbprefix'] = 'Table prefix';
        $lang['en']['preparation'] = 'Preparation';
        $lang['en']['select_site'] = 'Select site';
        $lang['en']['default_site'] = 'Default site';
        $lang['en']['site_store'] = 'Install site from FloximStore';
        $lang['en']['select_design'] = 'Select design';
        $lang['en']['design_store'] = 'Install design from FloximStore';
        $lang['en']['final'] = 'Installation completed successfully';
        $lang['en']['final_text'] = '<a href="/">Go to site</a>';
        $lang['en']['error_sitename'] = 'en Заполните поле с названием сайта';
        $lang['en']['error_email'] = 'en Заполните поле с email';
        $lang['en']['error_login'] = 'en Заполните поле с логином';
        $lang['en']['error_pass'] = 'en Заполните поле с паролем';
        $lang['en']['error_pass2'] = 'en Пароли не совпадают';
        $lang['en']['error_db'] = 'en Не удалось подключиться к базе данных';
        $lang['en']['error_php'] = 'en Версия PHP должна быть не ниже 5.1.0 (текущая версия - %s )';
        $lang['en']['error_php_param_on'] = 'en Необходимо включить параметр "%s"';
        $lang['en']['error_php_param_off'] = 'en Необходимо отключить параметр "%s"';
        $lang['en']['error_php_ext'] = 'en Не хватает расширения "%s"';
        $lang['ru']['error_php_right'] = 'Не хватает прав на запись в директорию/файл "%s"';
        $lang['en']['js']['download'] = 'Download...';
        $lang['en']['js']['unpacking'] = 'Unpacking...';
        $lang['en']['js']['dbinstall'] = 'en Распаковка БД...';
        $lang['en']['js']['configedit'] = 'en Правка конфигурационного файла...';
        $lang['en']['js']['importdefault'] = 'en Установка стандартных расширений...';
        $lang['en']['js']['importdesign'] = 'en Импорт макета дизайна...';
        $lang['en']['js']['importsite'] = 'en Импорт конфигурации...';

        $this->lang = $lang[$language];
    }

}
?>