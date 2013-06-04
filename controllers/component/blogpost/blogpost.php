<?php
class fx_controller_component_blogpost extends fx_controller_component {
    public function do_listing() {
        //echo fen_debug('start listing');
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('tags');
            //$query->limit(25);
        });
        $res = parent::do_listing();
        //echo fen_debug('data grabbed');
        return $res;
    }

    public function search_by_date() {
        // search will be here
    }
}
?>