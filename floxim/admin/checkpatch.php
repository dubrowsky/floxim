<?php

class fx_admin_checkpatch extends fx_admin_floximsite {

    /**
     * Возвращает true, если обращение к серверу обновлений было успешно
     */
    static public function check( $force = false ) {
        $self = new self();
        return $self->run($force);
    }

    public function run($force) {
        $fx_core = fx_core::get_object();
        $last_check = $fx_core->get_settings('last_check');

        if ($force || (time() > $last_check + $this->get_period()) ) {
            return $this->check_patch();
        }

        return false;

    }

    public function get_file() {
        $post = $this->get_base_post();
        $post['action'] = 'get_file';
        return $this->send($post);
    }

    protected function get_period() {
        $period = fx::config()->CHECK_PATCH_PERIOD;
        if (!$period) $period = 7;

        return $period * 86400;
    }

    protected function check_patch() {
        $fx_core = fx_core::get_object();

        $post = $this->get_base_post();
        $post['action'] = 'get_next_patch';
        $res = $this->send($post);
        dev_log($post);
        $res = json_decode($res, 1);
        if (is_array($res)) {
            $this->update($res);
        }
        else {
            return false;
        }

        $fx_core->set_settings('last_check', time());
        return true;
    }

    protected function update($response) {
        $fx_core = fx_core::get_object();

        if (isset($response['next_patch'])) {
            $fx_core->set_settings('next_patch', $response['next_patch']);
        }
        $fx_core->set_settings('last_response', serialize($response));
    }

    protected function get_base_post() {
        $post = parent::get_base_post();
        $post['essence'] = 'module_patch';
        return $post;
    }

}

?>
