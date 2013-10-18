<?
class fx_controller_component_tagpost extends fx_controller_component {
    public function do_listing() {
        $this->listen('query_ready', function(fx_data $query) { 
            $query->with('tag');
        });
        return parent::do_listing();
    }
}
?>