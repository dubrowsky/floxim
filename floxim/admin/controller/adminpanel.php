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
                <div id="fx_admin_additional_menu"><a class="fx_logout">'.fx::alang('Sign out','system').'</a></div>
                <div id="fx_admin_clear"></div>
            </div>
            <div id="fx_admin_control" class="fx_overlay">
                <div id="fx_admin_buttons"></div>
                <div id="fx_admin_fields"></div>
                <div id="fx_admin_additionl_text"></div>
                <div id="fx_admin_statustext"></div>
                <div id="fx_admin_extra_panel">
                    <div class="fx_admin_panel_title"></div>
                    <div class="fx_admin_panel_body"></div>
                    <div class="fx_admin_panel_footer"></div>
                </div>
            </div>
            <div id="fx_dialog" class="fx_overlay"></div>
            <div id="fx_dialog_file" class="fx_overlay"></div>';
    }

    public static function get_more_menu() {
        $more_menu = array();
        $more_menu[] = array(
            'name' => fx::alang('Page design','system'),
            'button' => array(
                'essence' => 'infoblock',
                'action' => 'layout_settings',
                'page_id' => fx::env('page_id')
            )
        );
        return $more_menu;
    }

    public static function get_buttons() {
        $result = array(
            'source' => array(
                'add' => array('title' => fx::alang('add', 'system'), 'type' => 'text'),
                'edit' => array('title' => fx::alang('edit', 'system')),
                'on' => array('title' => fx::alang('on', 'system')),
                'off' => array('title' => fx::alang('off', 'system')),
                'settings' => array('title' => fx::alang('settings', 'system')),
                'delete' => array('title' => fx::alang('delete', 'system')),
                'select_block' => array('title' => fx::alang('Select parent block','system')),
                'rights' => array('title' => fx::alang('Permissions','system')),
                'upload' => array('title' => fx::alang('Upload file','system')),
                'download' => array('title' => fx::alang('Download file','system')),
                'map' => array('title' => fx::alang('Site map','system')),
                'export' => array('title' => fx::alang('Export','system')),
                'store' => array('title' => fx::alang('Download from FloximStore','system')),
                'import' => array('title' => fx::alang('Import','system')),
                'change_password' => array('title' => fx::alang('Change password','system')),
                'undo' => array('title' => fx::alang('Cancel', 'system')),
                'redo' => array('title' => fx::alang('Redo', 'system')),
                'more' => array('title' => fx::alang('More', 'system'))
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