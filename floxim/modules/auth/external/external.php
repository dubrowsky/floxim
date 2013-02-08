<?php

class fx_auth_external {

    protected $settings = array();
    protected $type = '';
    protected $external_table;

    public function __construct() {
        $this->settings = fx_core::get_object()->get_settings('', 'auth');
        $this->external_table = fx_data::optional('auth_external');
    }

    public function proccess_response($response) {
        $external_id = $response['id'];

        $user = $this->user_exists($external_id);
        if ( !$user ) {
            $user = $this->make_user($response);
            $this->eval_addaction($user, $response);
            $this->link_user( $user, $external_id );
        } 

        $user->authorize();
    }

    protected function user_exists($external_id) {
        $fx_core = fx_core::get_object();
        
        $user = $this->external_table->get('external_id', $external_id, 'type', $this->type);
        if ($user) {
            return $fx_core->user->get_by_id($user['user_id']);
        }

        return false;
    }

    protected function make_user($info) {
        $fx_core = fx_core::get_object();

        $user = $fx_core->user->create();
        
        $groups = unserialize($this->settings[$this->type.'_group']);
        if ( is_array($groups) )  {
            $user->set_groups($groups);
        }
        $user['password'] = md5(time().md5(rand().rand()));
        $user['checked'] = 1;
        $user['site_id'] = $fx_core->env->get_site('id');
        $user['created'] = date("Y-m-d H:i:s");
        $user['type'] = $this->type;

        $fields = array();
        foreach (fx_user::fields() as $v) {
            $fields[$v->get_name()] = $v;
        }

        $map = unserialize($this->settings[$this->type.'_map']);
        if (is_array($map)) {
            foreach ($map as $map_field) {
                $value = $info[$map_field['external_field']];
                $field_name = $map_field['user_field'];

                $field = $fields[$field_name];

                if ($field->validate_value($value)) {
                    $field->set_value($value);
                    $user[$field_name] = $field->get_savestring($user);
                }
            }
        }

        $user->save();

        foreach ($fields as $v) {
            $v->post_save($user);
        }
        return $user;
    }

    protected function link_user ( $user, $external_id ) {
        $row = array();
        $row['user_id'] = $user['id'];
        $row['type'] = $this->type;
        $row['external_id'] =  $external_id;
        
        $this->external_table->create($row)->save();
    } 
    
    protected function eval_addaction ( $user,  $response ) {
        $fx_core = fx_core::get_object();
        $action = $fx_core->get_settings($this->type.'_addaction', 'auth');
        
        if ($action) eval($action.';');
    }
    
    protected function redirect ( $url = '/' ) {
        ob_end_clean();
        header("Location: ".($url ? $url : '/'));
        exit;
    }
}

?>
