<?php

class widget_youtube extends fx_tpl_widget {

    public function record() {
        extract($this->get_vars());
        ?>
        <iframe width='<?=$f_width?>' height='<?=$f_height?>' src='http://www.youtube.com/embed/<?=$this->m['code']?>' frameborder='0' allowFullScreen></iframe>
        
                <?
    }

    public function settings() {
        extract($this->get_vars()); 
        if (preg_match('/.*?youtube.com\/watch\?v=(.+?)(&|$).*/i', $f_url, $matches)) {
            $this->m['code'] = $matches[1];
        } else {
            $this->m['code'] = $f_url;
        }
    }


}
?>

