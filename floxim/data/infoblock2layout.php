<?php
class fx_data_infoblock2layout extends fx_data {
    public function __construct() {
        parent::__construct();
        $this->classname = 'fx_infoblock2layout';
        $this->serialized = array('wrapper_visual', 'template_visual');
        $this->order = 'priority';
    }
    
    public function get_for_infoblocks($infoblocks) {
        if ($infoblocks instanceof fx_infoblock) {
            $infoblocks = array($infoblocks);
        }
        $ids = array();
        foreach ($infoblocks as $ib) {
            if (is_numeric($ib)) {
                $ids []= $ib;
            } else {
                $ids []= $ib['id'];
            }
        }
        $i2ls = $this->get_all(array('infoblock_id' => $ids));
        return $i2ls;
    }
}
?>
