<?php
class fx_controller_admin_adminpanel extends fx_controller_admin {
    
    public function index() {
        
    }

    static public function panel_html() {
        return '
            <div id="fx_admin_panel" class="fx_overlay">
                <div id="fx_admin_panel_logo"><div class="fx_preloader"></div></div>
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
        return $more_menu;
    }

    public static function get_buttons() {
        $result = array(
            'source' => array(
                'add' => array('title' => fx::lang('add', 'system'), 'type' => 'text'),
                'edit' => array('title' => fx::lang('edit', 'system')),
                'on' => array('title' => fx::lang('on', 'system')),
                'off' => array('title' => fx::lang('off', 'system')),
                'settings' => array('title' => fx::lang('settings', 'system')),
                'delete' => array('title' => fx::lang('delete', 'system')),
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
            )
        );
        return $result;
    }
}
?>