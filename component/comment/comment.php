<?php
class fx_controller_component_comment extends fx_controller_component {
    public function do_listing() {
      /*if (isset($_POST["addcomment"]) && isset($_POST["user_name"]) && isset($_POST["comment_text"])) {
			
         $comments = fx::data('content_comment')->create(
            array(
               'user_name' => $_POST["user_name"], 
               'comment_text' => $_POST["comment_text"], 
               'publish_date' => date("Y-m-d H:i:s"), 
               'parent_id' => $this->_get_parent_id(),
               'infoblock_id' => $this->get_param('infoblock_id')
            )
         );
         $comments->save();    	
      }
    */
      $res= parent::do_listing();
      $this->_meta['hidden'] = false;
      return $res;
    }

    public function  get_action_settings_add() {
        $target_ibs = fx::data('infoblock')
        ->where('controller', 'component_comment')
        ->where('action', 'listing')->all();
        $field= array(
            'type' => 'select',
            'name' => 'target_infoblock_id',
            'label' => 'Target infoblock',
            'values' => array()
         
        );
        foreach ($target_ibs as $ib) {
            $field['values'] []= array($ib['id'], $ib['name']);
        }
        return array('target_infoblock_id' => $field);
    }
    
    public function do_add() {
      if (isset($_POST["addcomment"]) && isset($_POST["user_name"]) && isset($_POST["comment_text"])) {
			
         $comments = fx::data('content_comment')->create(
            array(
               'user_name' => $_POST["user_name"], 
               'comment_text' => $_POST["comment_text"], 
               'publish_date' => date("Y-m-d H:i:s"), 
               'parent_id' => $this->_get_parent_id(),
               'infoblock_id' => $this->get_param('target_infoblock_id'),
            )
         );
         dev_log($comments);
         $comments->save();    	
         fx::http()->refresh();    
      }
   }
}
?>