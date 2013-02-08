<?php

class fx_config {
    private $config = array(
            'SUB_FOLDER' => '',

            'DB_DSN' => '',
            'DB_USER' => '',
            'DB_PASSWORD' => '',
            'DB_PREFIX' => 'fx',
            'DB_CHARSET' => 'utf8',
            'DB_ENCRYPT' => 'MD5',

            'CHARSET' => 'utf-8',

            'PAGE_TPL' => 'page',
            'SEARCH_KEY' => 'fxsrch',

            'CHECK_PATCH_PERIOD' => 7,
            'REDIRECT_FULL_MESSAGE' => 1,

            'AUTHORIZE_BY' => 'email',
            'AUTHTIME' => 86400,
            'AUTHTYPE' => 'manual',
            'FILECHMOD' => 0644,
            'DIRCHMOD' => 0755,
            'PHP_TYPE' => 'module',

            'HTTP_ROOT_PATH' => '/floxim/',
            'HTTP_FILES_PATH' => '/floxim_files/',
            'HTTP_DUMP_PATH' => '/floxim_dump/',
            'HTTP_TEMPLATE_PATH' => '/floxim_templates/',
            'HTTP_COMPONENT_PATH' => '/floxim_components/',
            'HTTP_WIDGET_PATH' => '/floxim_widgets/',

            'SESSION_KEY' => '_fx_cms_',

            'HTTP_MODULE_PATH' => '',
            'HTTP_ACTION_LINK' => '',

            'DOCUMENT_ROOT' => '',
            'HTTP_HOST' => '',
            'FLOXIM_FOLDER' => '',
            'ADMIN_PATH' => '',
            'ADMIN_TEMPLATE' => '',
            'SYSTEM_FOLDER' => '',
            'ROOT_FOLDER' => '',
            'FILES_FOLDER' => '',
            'DUMP_FOLDER' => '',
            'INCLUDE_FOLDER' => '',
            'TMP_FOLDER' => '',
            'MODULE_FOLDER' => '',
            'ADMIN_FOLDER' => '',
            'TEMPLATE_FOLDER' => '',
            'COMPONENT_FOLDER' => '',
            'WIDGET_FOLDER' => '',
            'NC_JQUERY_PATH' => ''
    );

    public function __construct() {
        error_reporting(E_ALL & ~(E_NOTICE | E_STRICT));
        @date_default_timezone_set(@date_default_timezone_get());

        @ini_set("session.auto_start", "0");
        @ini_set("session.use_trans_sid", "0");
        @ini_set("session.use_cookies", "1");
        @ini_set("session.use_only_cookies", "1");
        @ini_set("url_rewriter.tags", "");
        @ini_set("session.gc_probability", "1");
        @ini_set("session.gc_maxlifetime", "1800");
        @ini_set("session.hash_bits_per_character", "5");
        @ini_set("mbstring.internal_encoding", "UTF-8");
        @ini_set("session.name", ini_get("session.hash_bits_per_character") >= 5 ? "sid" : "ced");
    }

    public function load(array $config = array()) {
        $this->config = array_merge($this->config, $config);

        $this->config['DOCUMENT_ROOT'] = rtrim(getenv("DOCUMENT_ROOT"), "/\\");
        $this->config['HTTP_HOST'] = getenv("HTTP_HOST");
        $this->config['FLOXIM_FOLDER'] = $this->config['DOCUMENT_ROOT'] . $this->config['SUB_FOLDER'];

        $this->config['HTTP_MODULE_PATH'] = $this->config['HTTP_ROOT_PATH'] . 'modules/';
        $this->config['HTTP_ACTION_LINK'] = $this->config['HTTP_ROOT_PATH'] . 'index.php';

        $this->config['ADMIN_PATH'] = $this->config['SUB_FOLDER'].$this->config['HTTP_ROOT_PATH'].'admin/';
        $this->config['ADMIN_TEMPLATE'] = $this->config['ADMIN_PATH'].'skins/default/';

        $this->config['ROOT_FOLDER'] = $this->config['FLOXIM_FOLDER'].$this->config['HTTP_ROOT_PATH'];
        $this->config['SYSTEM_FOLDER'] = $this->config['ROOT_FOLDER'].'system/';
        $this->config['FILES_FOLDER'] = $this->config['FLOXIM_FOLDER'].$this->config['HTTP_FILES_PATH'];
        $this->config['DUMP_FOLDER'] = $this->config['FLOXIM_FOLDER'].$this->config['HTTP_DUMP_PATH'];
        $this->config['TEMPLATE_FOLDER'] = $this->config['FLOXIM_FOLDER'].$this->config['HTTP_TEMPLATE_PATH'];
        $this->config['COMPONENT_FOLDER'] = $this->config['FLOXIM_FOLDER'].$this->config['HTTP_COMPONENT_PATH'];
        $this->config['WIDGET_FOLDER'] = $this->config['FLOXIM_FOLDER'].$this->config['HTTP_WIDGET_PATH'];
        $this->config['INCLUDE_FOLDER'] = $this->config['ROOT_FOLDER'].'lib/';
        $this->config['TMP_FOLDER'] = $this->config['ROOT_FOLDER'].'tmp/';
        $this->config['MODULE_FOLDER'] = $this->config['FLOXIM_FOLDER'].$this->config['HTTP_MODULE_PATH'];
        $this->config['ADMIN_FOLDER'] = $this->config['ROOT_FOLDER'].'admin/';
        $this->config['NC_JQUERY_PATH'] = $this->config['ROOT_FOLDER'].'lib/js/jquery-1.6.min.js';
        $this->config['COMPILED_TEMPLATES_FOLDER'] = $this->config['FILES_FOLDER'].'compiled_templates';

        return $this;
    }

    public function to_array() {
        return $this->config;
    }

    public function __get($name) {
        if (isset($this->config[$name])) {
            return $this->config[$name];
        } else {
            $trace = debug_backtrace();
            trigger_error('Undefined class property '.__CLASS__.'->'.$name.
                    ' in '.$trace[0]['file'].
                    ' on line '.$trace[0]['line'], E_USER_NOTICE);
            return null;
        }
    }

    public function __isset($name) {
        return isset($this->config[$name]);
    }
}