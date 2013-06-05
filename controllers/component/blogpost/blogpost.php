<?php
class fx_controller_component_blogpost extends fx_controller_component {
    public function do_listing() {
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('tags');
        });
        if ( isset($_GET['p_year']) || isset($_GET['p_month']) ) {
            $this->listen('build_query', function (fx_data $query) {
                // TODO: переделать логику после подключения конструктора Get-запросов
                if ( isset($_GET['p_year']) ) {
                    $interval['begin'] = $_GET['p_year'] . '-01-01 00:00:00';
                    $interval['end'] = $_GET['p_year'] . '-12-31 00:00:00';
                } elseif ( isset($_GET['p_month']) ) {
                    $month = strlen($_GET['p_month']) == 1 ? '0'.$_GET['p_month'] : $_GET['p_month'];
                    $interval['begin'] = date('Y') . '-' . $month . '-01 00:00:00';
                    $days_in_current_month = date('t',strtotime('20.'.$month.'.'.date('Y')));
                    $interval['end'] = date('Y') . '-' . $month . '-' .$days_in_current_month . ' 00:00:00';
                }
                // вытаскиваем только те посты, которые подходят под наш запрос
                $query->where('publish_date',$interval['begin'],'>=');
                $query->where('publish_date',$interval['end'],'<=');
            });
        }
        return parent::do_listing();
    }

    public function do_calendar() {
        $month_names = array(
            'январь','февраль','март','апрель','май','июнь','июль','август','сентябрь','октябрь','ноябрь','декабрь',
        );
        
        $f = $this->_get_finder();
        $months = fx::data('content_blogpost')->
            select('DATE_FORMAT(`publish_date`, "%c") as month')->
            select('DATE_FORMAT(`publish_date`, "%Y") as year')->
            select('COUNT(DISTINCT({{content}}.id)) as cnt')->
            order('year', 'DESC')->order('month')->
            group('month')->group('year')->
            get_data();
        
        $blog_page = fx::data(
                'content_page', 
                fx::data('infoblock', $this->param('infoblock_id')->get('page_id'))
        );
        echo fen_debug($blog_page);
        die();
        $years = array();
        foreach ($months as $m) {
            if (!isset($years[$m['year']])) {
                $months[$m['year']] = array();
            }
            $months[$m['year']][] = $m + array(
                'url' => ''
            );
        }
        // TODO: сделать составитель урла с учетом нескольких передаваемых GET параметров из разных мест
        $curent_year = isset($_GET['p_year']) ? $_GET['p_year'] : date('Y');
        $f = $this->_get_finder();
        $content_type = $this->get_content_type();
        $items = $f->all();
        $months = array();
        $years = array();

        $cur_month = date('n');
        for ( $i = 1; $i <=12; $i++ ) if ( $i <= (int)$cur_month ) $months[$i] = 0;
        foreach ( $items as $item ) {
            $item_month = date('n',strtotime($item['publish_date']));
            $item_year = date('Y',strtotime($item['publish_date']));
            $years[] = $item_year;
            if ( $item_year == $curent_year )  $months[$item_month]++;
        }
        $years = array_unique($years);
        $c = count($months);
        for ( $i=0; $i <= $c; $i++)
            if( $months[$i] == 0 ) unset( $months[$i] );

        $items = array();
        $items['years'] = array();
        foreach ( $years as $year ) {
            $items['years'][] = array(
                'name' => $year,
                'url' => "?p_year=".$year
            );
        }
        $items['months'] = array();
        foreach ( $months as $month => $post_count ) {
            $items['months'][] = array(
                'name' => $month_names[(int)$month-1],
                'post_counter' => $post_count,
                'url' => '?p_month='.$month
            );
        }
        return $items;
    }
}
?>