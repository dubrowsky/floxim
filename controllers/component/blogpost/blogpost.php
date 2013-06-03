<?php
class fx_controller_component_blogpost extends fx_controller_component {
    public function do_listing() {
        $this->listen('query_ready', function (fx_data $query) {
            $query->with('tags');
        });
        return parent::do_listing();
    }

    public function search_by_date() {
        // search will be here
    }
}
?>