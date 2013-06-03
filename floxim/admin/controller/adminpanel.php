<?php
class fx_controller_admin_adminpanel extends fx_controller_admin {

    public function main($input) {
        $fx_core = fx_core::get_object();

        echo '<html><title>Floxim</title><body></body></html>';

        $fx_core->page->add_js_file('/floxim/lib/js/jquery-1.7.1.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery-ui-1.8.13.custom.min.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.ba-hashchange.min.js');
        $fx_core->page->add_js_file('/floxim/lib/js/ajaxfileupload.js'); // ajax upload files
        $fx_core->page->add_js_file('/floxim/admin/js/lib.js');
        $fx_core->page->add_js_file('/floxim/admin/js/adminpanel.js');
        $fx_core->page->add_js_file('/floxim/admin/js/form.js');
        $fx_core->page->add_js_file('/floxim/admin/js/dialog.js');
        $fx_core->page->add_js_file('/floxim/admin/js/fields.js');
        $fx_core->page->add_js_file('/floxim/admin/js/edit-in-place.js');
        $fx_core->page->add_js_file('floxim/lib/editors/elrte/elrte.full.js');
        $fx_core->page->add_js_file('/floxim/lib/editors/elrte/i18n/elrte.ru.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.form.js');
        $fx_core->page->add_js_file('/floxim/lib/js/jquery.jstree.js');

        $fx_core->page->add_css_file('/floxim/lib/css/elrte/elrte.min.css');
        $fx_core->page->add_css_file('/floxim/admin/skins/default/jquery-ui/main.css');
        $fx_core->page->add_css_file('/floxim/admin/skins/default/css/main.css');

        $fx_core->page->set_after_body(fx_controller_admin_adminpanel::panel_html());
    }

    public function index($input) {
        $fx_core = fx_core::get_object();
    }

    static public function panel_html() {
        return '
            <div id="fx_admin_panel" class="fx_overlay">
                <div id="fx_admin_panel_logo"></div>
                <div id="fx_admin_main_menu"></div>
                <div id="fx_admin_page_modes"></div>
                <div id="fx_admin_more_menu"></div>
                <div id="fx_admin_additional_menu"></div>
                <div id="fx_admin_clear"></div>
            </div>
            <div id="fx_admin_control" class="fx_overlay">
                <div id="fx_admin_buttons"></div>
                <div id="fx_admin_additionl_text"></div>
                <div id="fx_admin_statustext"></div>
            </div>
            <div id="fx_dialog" class="fx_overlay"></div>
            <div id="fx_dialog_file" class="fx_overlay"></div>';
    }


    public static function get_main_menu() {
        $main_menu = array();
        $main_menu[] = array('name' => 'Управление', 'items' => array(
                'site' => array('name' => 'Сайт', 'href' => '/#page.view'),
                'user' => array('name' => 'Пользователи', 'href' => '/floxim/#admin.user.all'),
                'tools' => array('name' => 'Инструменты', 'href' => '/floxim/#admin.redirect.all'),
                'administrate' => array('name' => 'Администрирование', 'href' => '/floxim/#admin.administrate.site.all'),
                'settings' => array('name' => 'Настройки', 'href' => '/floxim/#admin.settings.system')
                ));
        $main_menu[] = array('name' => 'Разработка', 'items' => array(
                'layout' => array('name' => 'Макеты', 'href' => '/floxim/#admin.layout.all'), /// template -> layout
                'component' => array('name' => 'Компоненты', 'href' => '/floxim/#admin.component.group'),
                'widget' => array('name' => 'Виджеты', 'href' => '/floxim/#admin.widget.group'),
                'devtools' => array('name' => 'Инструменты разработчика', 'href' => '/floxim/#admin.devtools.sql')
                ));

        return $main_menu;
    }

    public static function get_more_menu() {
        $more_menu = array();
        $more_menu[] = array(
            'name' => 'Дизайн страницы',
            'button' => array(
                'essence' => 'infoblock',
                'action' => 'layout_settings',
                'page_id' => fx::env('page')
            )
        );
        //$more_menu[] = array('name' => 'Настройки дизайна', 'button' => 'design_settings');
        //$more_menu[] = array('name' => 'Настройки прав', 'button' => 'page_rights');
        $more_menu[] = array(
        	'name' => 'Сменить макет сайта',
        	'button' => array(
        		'essence' => 'template',
        		'action' => 'set_preview',
        		'panel_mode' => true
        	)
		);
        return $more_menu;
    }

    public static function get_buttons() {
        $result = array(
            'source' => array(
                'add' => array('title' => FX_ADMINPANEL_SITE_BUTTON_ADD, 'type' => 'text'),
                'edit' => array('title' => FX_ADMINPANEL_SITE_BUTTON_EDIT),
                'on' => array('title' => FX_ADMINPANEL_SITE_BUTTON_ON),
                'off' => array('title' => FX_ADMINPANEL_SITE_BUTTON_OFF),
                'settings' => array('title' => FX_ADMINPANEL_SITE_BUTTON_SETTINGS),
                'delete' => array('title' => FX_ADMINPANEL_SITE_BUTTON_DELETE),
                'select_block' => array('title' => 'выделить блок'),
                'rights' => array('title' => 'Права'),
                'upload' => array('title' => 'Закачать файл'),
                'download' => array('title' => 'Cкачать файл'),
                'map' => array('title' => 'Карта сайта'),
                'export' => array('title' => 'Экспорт'),
                'store' => array('title' => 'Скачать с FloximStore'),
                'import' => array('title' => 'Импорт'),
                'change_password' => array('title' => 'Сменить пароль'),
                'undo' => array('title' => FX_ADMINPANEL_SITE_BUTTON_UNDO),
                'redo' => array('title' => FX_ADMINPANEL_SITE_BUTTON_REDO),
                'more' => array('title' => FX_ADMINPANEL_SITE_BUTTON_REDO)
            ),
            'map' => array(
                'page' => array(
                    'edit' => array('add', 'divider', 'edit', 'on', 'off', 'delete','divider', 'select_block' , 'settings'),
                    'design' => array('add','divider','on', 'off', 'settings', 'delete', 'select_block')
                )
            )
        );

        return $result;
    }

    public function get_menuitems($input) {
        return call_user_func(array($this, '_items_'.$input['menu']));
    }

    public function get_files() {
        $fx_core = fx_core::get_object();

        $md5s = array();
        $max_x = 200;
        $max_y = 200;

        $files = $fx_core->db->get_results("select * from {{filetable}}");
        $values = array();
        if ($files) {
            foreach ($files as $v) {
                $path = fx::config()->HTTP_FILES_PATH.$v['path'];
                if (!file_exists(fx::config()->FILES_FOLDER.$v['path'])) continue;
                if (!is_file(fx::config()->FILES_FOLDER.$v['path'])) continue;
                if (!($size = getimagesize(fx::config()->FILES_FOLDER.$v['path'])))
                        continue;

                $md5 = md5(file_get_contents(fx::config()->FILES_FOLDER.$v['path']));
                if (in_array($md5, $md5s)) continue;

                $md5s[] = $md5;

                $x = $size[0];
                $y = $size[1];
                $ratio = min($max_x / $x, $max_y / $y);

                $new_x = intval($x * $ratio);
                $new_y = intval($y * $ratio);

                $values[$v['id']] = "<img width='".$new_x."px' height='".$new_y."px' src='".$path."' />";
            }
            $values = array_unique($values);
        }

        return $values;
    }

}
?>