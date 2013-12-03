<?php
class fx_controller_component_publication extends fx_controller_component {
    
    protected function _get_publication_page() {
        $infoblock_id=$this->get_param('source_infoblock_id');
        $infoblock = fx::data('infoblock', $infoblock_id);
        return fx::data(
            'content_page', 
            $infoblock->get('page_id')
        );
    }
    public function settings_calendar() {
        $ib_values=fx::data('infoblock')->
                    where('site_id', fx::env('site')->get('id'))->
                    get_content_infoblocks($this->get_content_type())
                    ->get_values('name', 'id');
        $fields ['source_infoblock_id']= array(
            'type' => 'select',
            'values' => $ib_values,
            'name' => 'source_infoblock_id',
            'label' => 
                fx::lang('Infoblock for the field', 'controller_component')
        );
        return $fields;
    }
    public function do_calendar() {
        $months = $this->_get_finder()->
            select('DATE_FORMAT(`publish_date`, "%m") as month')->
            select('DATE_FORMAT(`publish_date`, "%Y") as year')->
            select('COUNT(DISTINCT({{content}}.id)) as `count`')->
            where('site_id', fx::env('site')->get('id'))->
            order('publish_date', 'DESC')->
            group('month')->group('year')->
            get_data();
        $base_url = $this->_get_publication_page()->get('url');
        
        $years = new fx_collection();
        $c_full_month = isset($_GET['month']) ? $_GET['month'] : null;
        $c_year = $c_full_month ? preg_replace("~\d+\.~", '', $c_full_month) : date('Y');
        foreach ($months as $m) {
            if (!isset($years[$m['year']])) {
                $years[$m['year']] = array(
                    'year' => $m['year'],
                    'months' => new fx_collection(),
                    'active' => $c_year == $m['year']
                );
            }
            
            $full_month = $m['month'].'.'.$m['year'];
            $m['active'] = $full_month == $c_full_month;
            $m['url'] = $base_url .'?month='.$full_month;
            $years[$m['year']]['months'][] = $m;
        }
        return array('items' => $years);
    }
    
}
?>