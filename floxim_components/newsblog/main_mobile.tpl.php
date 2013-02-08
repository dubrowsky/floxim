<?php

class ctpl_newsblog_main_mobile extends ctpl_newsblog_main {

    public function prefix() {
        extract($this->get_vars());
         ?>
<h2>Мобильный вид</h2>
<div>
        <?php 
    }



    public function suffix() {
        extract($this->get_vars());
         ?>
<?= ( $fx_tpl ? $fx_tpl->listing($fx_infoblock) : '') ?>
</div>
        <?php 
    }



    public function record() {
        extract($this->get_vars());
            $group = '';
            // группировка
            if ($nc_visual['group'] == 'day') {
                if ($this->m['cur_dep'] != $f_created_d) {
                    $this->m['cur_dep'] = $f_created_d;
                    $group = '<h4 class="nc_group">' . $f_created_date . '</h4>';
                }
            }
            if ($nc_visual['group'] == 'month') {
                if ($this->m['cur_dep'] != $f_created_m) {
                    $this->m['cur_dep'] = $f_created_m;
                    $group = '<h4 class="nc_group">' . $f_created_F_loc . ' ' . $f_created_Y . '</h4>';
                }
            }
            if ($nc_visual['group'] == 'year') {
                if ($this->m['cur_dep'] != $f_created_Y) {
                    $this->m['cur_dep'] = $f_created_Y;
                    $group = '<h4 class="nc_group">' . $f_created_Y . '</h4>';
                }
            }
         ?>
<div class="nc_row <?= $f_id_hash ?>" style="padding:10px; margin-bottom: 5px;">
    <?= $group ?>
    <h3 style="margin-top: 0px;"><a href="<?= $full_link ?>" class="<?= $f_caption_hash ?>"><?= $f_caption ?></a></h3>
    <?= ($f_ev ? 'Дата события: <span class="' . $f_ev_hash . '">' . $f_ev_date . '</span><br/>' : '') ?>
    <?= ( $f_city_id ? 'Город: <span class="' . $f_city_hash . '">' . $f_city . '</span><br/><br/>' : '' ) ?>
    <?= ( $f_mcity ? 'Города: <span class="' . $f_mcity_hash . '">' . join(', ', $f_mcity) . '</span><br/><br/>' : '' ) ?>
    <div class="<?= $f_announce_hash ?>"><?= $f_announce ?></div>
    <?= ( $f_foto ? '<div class="' . $f_foto_hash . '"><img src="' . $f_foto . '" width="150" height="150"/><br/></div>' : '') ?>
</div>

        <?php 
    }



    public function full() {
        extract($this->get_vars());
         ?>
        <div class="<?= $f_id_hash ?>">
            <div class="nc_row <?= $f_content_hash ?>" ><?= $f_content ?></div>
        </div>
        <?php 
    }



    public function h1() {
        extract($this->get_vars());
        return $f_caption;
    }


}
?>