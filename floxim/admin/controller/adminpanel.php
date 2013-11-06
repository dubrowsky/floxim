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
                <div id="fx_admin_additional_menu"><a class="fx_logout">'.fx::lang('Sign out','system').'</a></div>
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
            'name' => fx::lang('Page design','system'),
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
                'add' => array('title' => fx::lang('add', 'system'), 'type' => 'text'),
                'edit' => array('title' => fx::lang('edit', 'system')),
                'on' => array('title' => fx::lang('on', 'system')),
                'off' => array('title' => fx::lang('off', 'system')),
                'settings' => array('title' => fx::lang('settings', 'system')),
                'delete' => array('title' => fx::lang('delete', 'system')),
                'select_block' => array('title' => fx::lang('Select parent block','system')),
                'rights' => array('title' => fx::lang('Permissions','system')),
                'upload' => array('title' => fx::lang('Upload file','system')),
                'download' => array('title' => fx::lang('Download file','system')),
                'map' => array('title' => fx::lang('Site map','system')),
                'export' => array('title' => fx::lang('Export','system')),
                'store' => array('title' => fx::lang('Download from FloximStore','system')),
                'import' => array('title' => fx::lang('Import','system')),
                'change_password' => array('title' => fx::lang('Change password','system')),
                'undo' => array('title' => fx::lang('Cancel', 'system')),
                'redo' => array('title' => fx::lang('Redo', 'system')),
                'more' => array('title' => fx::lang('More', 'system'))
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