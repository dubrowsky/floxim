<?php
class widget_menutest extends fx_tpl_widget {

    public function record() {
        extract($this->get_vars());
         ?>
        <div>Новый виджет</div>
        <?php 
    }

    public function settings() {
        extract($this->get_vars());
		

		
    }
}
?>