<?php
class fx_controller_admin_adminpanel extends fx_controller_admin {


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
                <div id="fx_admin_fields"></div>
                <div id="fx_admin_additionl_text"></div>
                <div id="fx_admin_statustext"></div>
            </div>
            <div id="fx_dialog" class="fx_overlay"></div>
            <div id="fx_dialog_file" class="fx_overlay"></div>';
    }


    public static function get_main_menu() {
        $main_menu = array();
        $main_menu[] = array('name' => fx::lang('Управление','system'), 'items' => array(
                'site' => array('name' => fx::lang('Сайт','system'), 'href' => '/#page.view'),
                'user' => array('name' => fx::lang('Пользователи','system'), 'href' => '/floxim/#admin.user.all'),
                'tools' => array('name' => fx::lang('Инструменты','system'), 'href' => '/floxim/#admin.redirect.all'),
                'administrate' => array('name' => fx::lang('Администрирование','system'), 'href' => '/floxim/#admin.administrate.site.all'),
                'settings' => array('name' => fx::lang('Настройки','system'), 'href' => '/floxim/#admin.settings.system')
                ));
        $main_menu[] = array('name' => fx::lang('Разработка','system'), 'items' => array(
                'layout' => array('name' => fx::lang('Макеты','system'), 'href' => '/floxim/#admin.layout.all'), /// template -> layout
                'component' => array('name' => fx::lang('Компоненты','system'), 'href' => '/floxim/#admin.component.group'),
                'widget' => array('name' => fx::lang('Виджеты','system'), 'href' => '/floxim/#admin.widget.group')
                ));

        return $main_menu;
    }

    public static function get_more_menu() {
        $more_menu = array();
        $more_menu[] = array(
            'name' => fx::lang('Дизайн страницы','system'),
            'button' => array(
                'essence' => 'infoblock',
                'action' => 'layout_settings',
                'page_id' => fx::env('page')
            )
        );
        //$more_menu[] = array('name' => 'Настройки дизайна', 'button' => 'design_settings');
        //$more_menu[] = array('name' => 'Настройки прав', 'button' => 'page_rights');
        $more_menu[] = array(
        	'name' => fx::lang('Сменить макет сайта','system'),
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
                'select_block' => array('title' => fx::lang('выделить блок','system')),
                'rights' => array('title' => fx::lang('Права','system')),
                'upload' => array('title' => fx::lang('Закачать файл','system')),
                'download' => array('title' => fx::lang('Cкачать файл','system')),
                'map' => array('title' => fx::lang('Карта сайта','system')),
                'export' => array('title' => fx::lang('Экспорт','system')),
                'store' => array('title' => fx::lang('Скачать с FloximStore','system')),
                'import' => array('title' => fx::lang('Импорт','system')),
                'change_password' => array('title' => fx::lang('Сменить пароль','system')),
                'undo' => array('title' => FX_ADMINPANEL_SITE_BUTTON_UNDO),
                'redo' => array('title' => FX_ADMINPANEL_SITE_BUTTON_REDO),
                'more' => array('title' => FX_ADMINPANEL_SITE_BUTTON_REDO)
            ),
            'map' => array(
                'page' => explode(
                    ",",
                    'add,divider,edit,on,off,delete,divider,select_block,settings'
                )
                /*array(
                    'edit' => array('add', 'divider', 'edit', 'on', 'off', 'delete','divider', 'select_block' , 'settings'),
                    'design' => array('add','divider','on', 'off', 'settings', 'delete', 'select_block')
                )*/
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