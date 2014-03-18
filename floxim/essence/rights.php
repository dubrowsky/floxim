<?php
class fx_rights extends fx_essence {
    /**
     * Типы пользователей:
     * наследовать + все, зарегистрированные, уполномоченные
     */
    static public function get_user_types ( $with_inherit = false) {
        $res = array('all', 'reg', 'auth');
        if ( $with_inherit ) array_unshift ($res, 'inherit');
        return $res;
    }
    
    /**
     * Типы прав:
     * просмотр, добавление, изменение, включение, удаление
     * @return type 
     */
    static public function get_rights_types () {
        return array('read', 'add', 'edit', 'checked', 'delete');
    }
    
    static public function get_label ( $item ) {
        $const_name = "FX_ADMIN_RIGHTS_".strtoupper($item);
        return defined($const_name) ? constant($const_name) : $item;
    }
}