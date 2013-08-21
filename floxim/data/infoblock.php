<?php

class fx_data_infoblock extends fx_data {

    public function __construct() {
        parent::__construct();
        $this->classname = 'fx_infoblock';
        $this->serialized = array('params', 'scope');
    }
    
    public function get_for_page($page_id) {
        $page = fx::data('content_page', $page_id);
        if (!$page) {
            return;
        }
        $ids = $page->get_parent_ids();
        $ids []= $page['id'];
        $ids []= 0; // корень
        $infoblocks = $this->
            where('page_id', $ids)->
            where('site_id', $page['site_id'])->
            where('checked', 1)->
            all();
        
        foreach ($infoblocks as $ib) {
            // если page_id=0 - тупо все страницы, игнорируем фильтр scope.pages
            if ($ib['page_id'] != 0) {
                // scope - "только эта страница"
                if (fx::dig($ib, 'scope.pages') == 'this' && $ib['page_id'] != $page_id) {
                    $infoblocks->remove($ib);
                    continue;
                }
                // scope - "этот уровень", а мы смотрим родителя
                if (fx::dig($ib, 'scope.pages') == 'children' && $ib['page_id'] == $page_id) {
                    $infoblocks->remove($ib);
                    continue;
                }
            }
            // проверяем на соответствие фильтра по типу страницы
            $scope_page_type = fx::dig($ib, 'scope.page_type');
            if ( $scope_page_type && $scope_page_type != $page['type'] ) {
                $infoblocks->remove($ib);
                continue;
            }
        }
        $inherited_infoblocks = $infoblocks->find(
                'id', 
                $infoblocks->find('parent_infoblock_id', 0, '!=')->get_values('parent_infoblock_id'),
                fx_collection::FILTER_IN
        );
        foreach ($inherited_infoblocks as $inherited) {
            $infoblocks->remove($inherited);
        }
        return $infoblocks;
    }

    protected function get_class_name($data = array()) {
        $classname = $this->classname;
        if ($data['type']) {
            $classname .= '_'.$data['type'];
            if ($data['subtype']) {
                $classname .= '_'.$data['subtype'];
            }
        }

        return $classname;
    }

    public function get_content_infoblocks($content_type = null) {
        if ($content_type) {
            $this->where('controller', 'component_'.$content_type);
        }
        $this->where('action', 'listing');
        return $this->all();
        $params = array();//'is_listing' => '1');
        if ($content_type) {
            $params ['controller'] = 'component_'.$content_type;
        }
        return $this->get_all($params);
    }

    public function next_priority($keyword) {
        return fx::db()->get_var("SELECT MAX(`priority`)+1 FROM `{{".$this->table."}}` WHERE `keyword` = '".fx::db()->escape($keyword)."' ");
    }

}