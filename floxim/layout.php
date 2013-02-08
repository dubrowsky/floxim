<?php

interface fx_layout {

    public function place_infoblock($keyword, $params);

    public function place_infoblock_simple($keyword, $params);

    public function place_menu($keyword, $params, $template = array());
}

?>
