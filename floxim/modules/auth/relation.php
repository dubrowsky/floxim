<?php

defined("FLOXIM") || die("Unable to load file.");

class fx_auth_user_relation extends fx_data {

    public $TYPE_FRIEND = 1;
    public $TYPE_BANNED = 2;

    protected $table = "auth_user_relation";
    /**
     * Вернуть все отношения пользователя
     *
     * @param  int user_id - индетификатор пользователя. По умолчанию - текущий
     * @param  int type - тип (друзья, враги), по умолчанию - все типы
     * @return array  [related_id] => type
     */
    public function get_all_relation($user_id = 0, $type=0) {

        $fx_core = fx_core::get_object();
        $current_user = $fx_core->env->get_user();

        $user_id = intval($user_id);
        $type = intval($type);

        // пользователь по умолчанию
        if (!$user_id)
            $user_id = $current_user['id'];

        $res = array();

        if (!$type)
            $relation = $this->get_all('user_id', $user_id);
        else
            $relation = $this->get_all('user_id', $user_id, 'type', $type);

        if ($relation) {
            foreach ($relation as $v) {
                $relation_arr = $v->get();
                $res[$relation_arr['related_id']] = $relation_arr['type'];
            }
        }

        return $res;
    }

    /**
     * Вернуть друзей пользователя
     *
     * @param int user_id, по умолчанию - текущий  пользователь
     * @return array [related_id] => $this->TYPE_FRIEND
     */
    public function get_all_friend($user_id = 0) {
        return $this->get_all_relation($user_id, $this->TYPE_FRIEND);
    }

    /**
     * Вернуть врагов пользователя
     *
     * @param int user_id, по умолчанию - текущий  пользователь
     * @return array [related_id] => $this->TYPE_FRIEND
     */
    public function get_all_banned($user_id = 0) {
        return $this->get_all_relation($user_id, $this->TYPE_BANNED);
    }

    /**
     * Узнать отношения между пользователями
     *
     * @param int $related_id
     * @param int $user_id, по умолчанию текущтий юзер
     * @return int
     */
    public function get_relation($related_id, $user_id=0) {

        $fx_core = fx_core::get_object();
        $current_user = $fx_core->env->get_user();

        $related_id = intval($related_id);
        $user_id = intval($user_id);

        if (!$user_id)
            $user_id = $current_user['id'];

        $relation = $this->get('user_id', $user_id, 'related_id', $related_id);

        if ($relation) {
            return $relation['type'];
        }
        else {
            return 0;
        }
    }

    /**
     * Проверить, является ли пользователь related_id другом user_id
     *
     * @param int $related_id
     * @param int $user_id, по умолчанию текущтий юзер
     * @return bool
     */
    public function is_friend ( $related_id, $user_id = 0) {
        return $this->get_relation($related_id, $user_id) == $this->TYPE_FRIEND;
    }

     /**
     * Проверить, является ли пользователь related_id врагом user_id
     *
     * @param int $related_id
     * @param int $user_id, по умолчанию текущтий юзер
     * @return bool
     */
    public function is_banned ( $related_id, $user_id = 0) {
        return $this->get_relation($related_id, $user_id) == $this->TYPE_BANNED;
    }

    /**
     * Пользовтели взаимные друзья\ враги?
     *
     * @param int $related_id
     * @param int $user_id, по умолчанию текущтий юзер
     * @return int тип (друг/враг/никто)
     */
    public function is_mutual($related_id, $user_id = 0) {
        $fx_core = fx_core::get_object();
        $current_user = $fx_core->env->get_user();

        $related_id = intval($related_id);
        $user_id = intval($user_id);

        if (!$user_id)
            $user_id = $current_user['id'];

        if (!$related_id || !$user_id) {
            return false;
        }

        $relation1 = $this->get_relation($related_id, $user_id);
        $relation2 = $this->get_relation($user_id, $related_id);

        if ($relation1 && ($relation1 == $relation2)) {
            return $relation1;
        }
        else {
            return 0;
        }
    }

    /**
     * Добавить отношения между пользователями
     *
     * @param int тип ($this->TYPE_FRIEND или $this->TYPE_FRIEND)
     * @param int related_id - пользователь, который становится кем-то
     * @param int user_id - "владелец" отношения. (по умолчанию - текущий пользователь)
     * @return bool
     */
    public function add_relation($type, $related_id, $user_id = 0) {

        $fx_core = fx_core::get_object();
        $current_user = $fx_core->env->get_user();

        $type = intval($type);
        $related_id = intval($related_id);
        $user_id = intval($user_id);

        if (!$user_id)
            $user_id = $current_user['id'];

        if (!$type || !$related_id || !$user_id)
            return false;

        $relation = $this->create();
        $relation['related_id'] = $related_id;
        $relation['user_id'] = $user_id;
        $relation['type'] = $type;
        $relation->save();

        return true;
    }

    /**
     * Добавить друга
     *
     * @param int related_id - пользователь, который становится другом
     * @param int user_id - "владелец" дружбы. (по умолчанию - текущий пользователь)
     * @return bool
     */
    public function add_friend ( $related_id, $user_id = 0 ) {
        return $this->add_relation($this->TYPE_FRIEND, $related_id, $user_id);
    }

     /**
     * Добавить врага
     *
     * @param int related_id - пользователь, который становится врагом
     * @param int user_id - "владелец" вражды. (по умолчанию - текущий пользователь)
     * @return bool
     */
    public function add_banned( $related_id, $user_id = 0 ) {
        return $this->add_relation($this->TYPE_BANNED, $related_id, $user_id);
    }

    /**
     * Удалить отношения между пользователями
     *
     * @param int related_id - пользователь, который становится кем-то
     * @param int user_id - "владелец" отношения. (по умолчанию - текущий пользователь)
     * @return bool
     */
    public function delete_relation($related_id, $user_id = 0) {

        $fx_core = fx_core::get_object();
        $current_user = $fx_core->env->get_user();

        $related_id = intval($related_id);
        $user_id = intval($user_id);

        if (!$user_id)
            $user_id = $current_user['id'];

        $relation = $this->get('user_id', $user_id, 'related_id', $related_id);

        if (!$relation)
            return false;

        $relation->delete();

        return true;
    }

        /**
     * Удалить все отношения пользователя
     *
     * @param int user_id - "владелец" отношения. (по умолчанию - текущий пользователь)
     * @return bool
     */
    public function delete_all_relation($user_id = 0) {

        $fx_core = fx_core::get_object();
        $current_user = $fx_core->env->get_user();

        $user_id = intval($user_id);

        if (!$user_id)
            $user_id = $current_user['id'];

        $relations = $this->get_all('user_id', $user_id);

        if (!$relations)
            return false;

        foreach ($relations as $v) {
            $v->delete();
        }

        return true;
    }


    /**
     * Получить url для добавления в друзья пользователя
     * @param int user_id номер пользоваетеля
     * @return string url
     */
    public function add_friend_url($user_id) {
        return "http://".fx::config()->HTTP_HOST.fx::config()->SUB_FOLDER."?essence=module_auth&action=change_relation&do=add_friend&id=".  intval($user_id);
    }

    /**
     * Получить url для добавления во враги пользователя
     * @param int user_id номер пользоваетеля
     * @return string url
     */
    public function add_banned_url($user_id) {
        return "http://".fx::config()->HTTP_HOST.fx::config()->SUB_FOLDER."?essence=module_auth&action=change_relation&do=add_banned&id=".  intval($user_id);
    }

    /**
     * Получить url для удаления из друзей/врагов пользователя
     * @param int user_id номер пользоваетеля
     * @return string url
     */
    public function delete_relation_url($user_id) {
        return "http://".fx::config()->HTTP_HOST.fx::config()->SUB_FOLDER."?essence=module_auth&action=change_relation&do=delete_relation&id=".  intval($user_id);
    }

}

?>
