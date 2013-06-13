<?php

class fx_admin_submenu {

    protected $menu = array();
    protected $error = false;
    protected $type = 'list';
    protected $backlink;
    protected $active = '', $subactive = '';
    protected $active_main_menu = '';
    protected $menu_id = '';
    protected $old_menu_id = '';
    protected $not_update = false;

    public function __construct($old = '') {
        $this->old_menu_id = $old;
    }

    public function set_menu($type) {

        $this->menu_id = $type;
        if ($this->menu_id == $this->old_menu_id) {
            $this->not_update = true;
        }

        preg_match("/^([a-z]+)(-([a-z0-9]+))?$/i", $type, $match);
        if ($match[1] == 'component' && !$match[3]) {
            $this->init_develop();
            $this->active = 'component';
            $this->active_main_menu = 'develop';
        }
        if ($match[1] == 'componentgroup') {
            $this->init_develop();
            $this->active = 'component';
            $this->subactive = 'componentgroup-'.$match[3];
            $this->active_main_menu = 'develop';
        }
        if ($match[1] == 'component' && is_numeric($match[3])) {
            $this->init_menu_component($match[3]);
            $this->active_main_menu = 'develop';
        }

        if ($match[1] == 'template' && !$match[3]) {
            $this->init_develop();
            $this->active = 'template';
            $this->active_main_menu = 'develop';
        }

        if($match[1] == 'layout') {
            $this->init_manage();
        }

        if ($match[1] == 'template' && is_numeric($match[3])) {
            $this->init_menu_template($match[3]);
            $this->active_main_menu = 'develop';
        }

        if ($match[1] == 'widget' && !$match[3]) {
            $this->init_develop();
            $this->active = 'widget';
            $this->active_main_menu = 'develop';
        }
        if ($match[1] == 'widgetgroup') {
            $this->init_develop();
            $this->active = 'widget';
            $this->subactive = 'widgetgroup-'.$match[3];
            $this->active_main_menu = 'develop';
        }
        if ($match[1] == 'widget' && is_numeric($match[3])) {
            $this->init_menu_widget($match[3]);
            $this->active_main_menu = 'develop';
        }


        if ($match[1] == 'site' && !$match[3]) {
            $this->init_manage();
            $this->active = 'site';
            $this->active_main_menu = 'manage';
        }

        if ($match[1] == 'administrate') {
            $this->init_manage();
            $this->active = 'administrate';
            $this->active_main_menu = 'manage';
        }

        if ($match[1] == 'tools') {
            $this->init_manage();
            $this->active = 'tools';
            $this->active_main_menu = 'manage';
        }

        if ($match[1] == 'site' && $match[3]) {
            $this->init_menu_site($match[3]);
            $this->active_main_menu = 'manage';
        }

        if ($match[1] == 'classificator' && $match[3]) {
            $this->init_menu_classificator($match[3]);
            $this->active_main_menu = 'manage';
        }

        if ($match[1] == 'user' && !$match[3]) {
            $this->init_manage();
            $this->active = 'user';
            $this->active_main_menu = 'manage';
        }

        if ($match[1] == 'user' && $match[3]) {
            $this->init_menu_user($match[3]);
            $this->active_main_menu = 'manage';
        }

        if ($match[1] == 'settings') {
            $this->init_manage();
            $this->active = 'settings';
            $this->active_main_menu = 'manage';
        }

        return $this;
    }

    public function set_subactive($item) {
        $this->subactive = $item;
        return $this;
    }

    protected function init_develop() {
        $this->menu[] = $node_component = $this->add_node('component', fx_lang('Компоненты'), 'component.group');
        $this->menu[] = $node_template = $this->add_node('layout', 'Макеты', 'layout.all'); // template ->layout
        $this->menu[] = $node_widget = $this->add_node('widget', fx_lang('Виджеты'), 'widget.group');

        foreach (fx::data('component')->get_all_groups() as $group) {
            $hash = md5($group);
            $this->menu[] = $this->add_node('componentgroup-'.$hash, $group, 'component.group('.$hash.')', $node_component);
        }
        foreach (fx::data('widget')->get_all_groups() as $group) {
            $hash = md5($group);
            $this->menu[] = $this->add_node('widgetgroup-'.$hash, $group, 'widget.group('.$hash.')', $node_widget);
        }
    }

    protected function init_manage() {
        $fx_core = fx_core::get_object();
        //$modules = $fx_core->modules->get_data();
        
        $this->menu[] = $node_site = $this->add_node('site', fx_lang('Список сайтов'), 'site.all');

        $this->menu[] = $node_administrate = $this->add_node('administrate', fx_lang('Администрирование'), 'administrate.module');
        $this->menu[] = $this->add_node('module', fx_lang('Модули'), 'administrate.module', $node_administrate);
        
        $this->menu[] = $node_tools = $this->add_node('tools', fx_lang('Инструменты'), 'redirect.all');
        
        $this->menu[] = $node_users = $this->add_node('user', fx_lang('Пользователи'), 'user.all');
        $this->menu[] = $this->add_node('user', 'Список пользователей', 'user.all', $node_users);
        $this->menu[] = $this->add_node('group', 'Группы', 'group.all', $node_users);

        $this->menu[] = $node_settings = $this->add_node('settings', fx_lang('Настройки'), 'settings.system');
    }

    protected function init_menu_component($id) {
        $this->type = 'full';
        $component = fx::data('component')->get_by_id($id);
        if (!$component) {
            $this->error = fx_lang('Компонент не найден');
        } else {
            $this->title = $component['name'];
            $this->backlink = 'component.group';

            $cid = $component['id'];
            
            // выводим основные разделы
            $submenu_items = fx_controller_admin_component::get_component_submenu($component);
            foreach ($submenu_items as $item) {
            	$this->menu[] = $this->add_node($item['code'], $item['title'], $item['url']);
            }
            
            /*
            // добавляем шаблоны
            $ctpls = fx::data('ctpl')->get_by_component($cid);
            foreach ($ctpls as $v) {
                $this->menu[] = $this->add_node('ctpl-'.$v['id'], $v['name'], 'ctpl.edit('.$v['id'].')', 'ctpl');
            }
             * 
             */
            
            // и поля
            foreach ($component->fields() as $v) {
                $this->menu[] = $this->add_node('field-'.$v['id'], $v['name'], 'component.edit('.$cid.',edit_field,'.$v['id'].')', 'fields');
            }
        }
    }

    protected function init_menu_template($id) {
        $this->type = 'full';
        // $template = fx::data('template')->get_by_id($id);
        $layout = fx::data('layout', $id);
        if (!$layout) {
            $this->error = fx_lang('Макет не найден');
        } else {
            $this->title = $layout['name'];
            $this->backlink = 'template.all';

            /*
            foreach ($layout->get_layouts() as $l) {
                $this->menu[] = $this->add_node('layout-'.$l['id'], $l['name'], 'layout.edit('.$l['id'].')', 'layouts');
            }
            */
            
            $items = fx_controller_admin_template::get_template_submenu($layout);
            foreach ($items as $item) {
            	$this->menu []= $this->add_node($item['code'], $item['title'], $item['url']);
            }
        }
    }

    protected function init_menu_widget($id) {
        $this->type = 'full';
        $widget = fx::data('widget')->get_by_id($id);
        if (!$widget) {
            $this->error = fx_lang('Виджет не найден');
        } else {
            $this->title = $widget['name'];
            $this->backlink = 'widget.group';
            
            $items = fx_controller_admin_component::get_component_submenu($widget);
            foreach ($items as $item) {
            	$this->menu []= $this->add_node($item['code'], $item['title'], $item['url']);
            }
            
            $cid = $widget['id'];

            foreach ($widget->fields() as $v) {
                $this->menu[] = $this->add_node('field-'.$v['id'], $v['name'], 'widget.edit('.$cid.',edit_field,'.$v['id'].')', 'fields');
            }
        }
    }

    protected function init_menu_site($id) {
        $this->type = 'full';
        $site = fx::data('site')->get_by_id($id);
        if (!$site) {
            $this->error = fx_lang('Сайт не найден');
        } else {
            $this->title = $site['name'];
            $this->backlink = 'site.all';

            $this->menu[] = $this->add_node('sitemap-'.$site['id'], fx_lang('Карта сайта'), 'site.map('.$site['id'].')');
            $this->menu[] = $this->add_node('sitesettings-'.$site['id'], fx_lang('Настройки'), 'site.settings('.$site['id'].')');
            $this->menu[] = $this->add_node('sitedesign-'.$site['id'], fx_lang('Дизайн'), 'site.design('.$site['id'].')');
        }
    }

    protected function init_menu_classificator($id) {
        $this->type = 'full';
        $classificator = fx::data('classificator')->get_by_id($id);
        if (!$classificator) {
            $this->error = fx_lang('Список не найден');
        } else {
            $this->title = $classificator['name'];
            $this->backlink = 'classificator.all';
        }
    }

    protected function init_menu_user($id) {
        $this->type = 'full';
        $user = fx::data('user')->get_by_id($id);
        if (!$user) {
            $this->error = fx_lang('Пользователь не найден');
        } else {
            $this->title = $user['name'];
            $this->backlink = 'user.all';
            $this->menu[] = $this->add_node('profile', fx_lang('Профиль'), 'user.full('.$user['id'].')');
        }
    }

    public function to_array() {
        $res = array();

        if ($this->not_update) {
            $res['not_update'] = true;
        }

        if ($this->menu) {
            $res['items'] = $this->menu;
        }
        if ($this->error) {
            $res['error'] = $this->error;
        }
        if ($this->title) {
            $res['title'] = $this->title;
        }
        if ($this->backlink) {
            $res['backlink'] = $this->backlink;
        }
        $res['active'] = $this->active;
        $res['subactive'] = $this->subactive;
        $res['type'] = $this->type;
        $res['menu_id'] = $this->menu_id;


        return $res;
    }

    public function add_node($id, $name, $href = '', $parent = null) {
        $node = array('id' => $id, 'name' => $name, 'href' => $href);
        if ($parent) {
            if (is_array($parent)) $parent = $parent['id'];
            $node['parent'] = $parent;
        }
        return $node;
    }

    public function get_active_main_menu() {
        return $this->active_main_menu;
    }

}
