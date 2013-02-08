<?php

class ctpl_newsblog_select extends ctpl_newsblog_main {

    public function record() {
        extract($this->get_vars());
         ?>
<div>
    <h3><?= $f_caption ?></h3>
    <div><?= $f_announce ?></div>
</div>
        <?php 
    }

}
?>