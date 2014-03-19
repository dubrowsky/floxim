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
        $ids []= 0; // root
        $infoblocks = $this->
            where('page_id', $ids)->
            where('site_id', $page['site_id'])->
            where('checked', 1)->
            all();
        foreach ($infoblocks as $ib) {
            // if page_id=0 blunt - all pages, ignored by the filter scope.pages
            if ($ib['page_id'] != 0) {
                // scope - "this page only"
                if (fx::dig($ib, 'scope.pages') == 'this' && $ib['page_id'] != $page_id) {
                    $infoblocks->remove($ib);
                    continue;
                }
                // scope - "this level, and we look parent
                if (fx::dig($ib, 'scope.pages') == 'children' && $ib['page_id'] == $page_id) {
                    $infoblocks->remove($ib);
                    continue;
                }
            }
            // check for compliance with the filter type page
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
        if (isset($data['type']) && $data['type']) {
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
        $this->where('action', 'list_infoblock');
        return $this->all();
    }

    public function next_priority($keyword) {
        return fx::db()->get_var("SELECT MAX(`priority`)+1 FROM `{{".$this->table."}}` WHERE `keyword` = '".fx::db()->escape($keyword)."' ");
    }

}