<?php
/*
$ctr_params = array('limit' => 2);
echo serialize($ctr_params)."<hr />";
$tpl_params = array('q2' => 'второй, особенный!');
echo serialize($tpl_params)."<hr />";
$wrap_params = array('color' => '#090');
echo serialize($wrap_params);
die();
*/

class fx_controller_test extends fx_controller {
    public function show() {
        return array('test_data' => 'Hello world!');
    }
    
    public function side() {
        $qs = array('what?','where?','wtf?','who?','omg???','whats next?');
        shuffle($qs);
        if (! ($limit = $this->param('limit')) ) {
            $limit = 3;
        }
        $qs = array_slice($qs, 0, $limit);
        return $qs;
    }
}
?>