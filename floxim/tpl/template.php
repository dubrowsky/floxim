<?php

abstract class fx_tpl_template extends fx_tpl {

    public function write() {

    }

    public function settings() {

    }


    public function listing(fx_infoblock_content $infoblock, $range = 5, $ctrl = true, $template_component =array()) {
        extract( $this->get_vars() );

        $fx_core = fx_core::get_object();

        // шаблон по умолчанию
        $def_template['prefix'] = "<div>%prev%&nbsp;%next%</div><div>";
        $def_template['active'] = "<span><b>%page%</b></span>";
        $def_template['unactive'] = "<span><a href='%url%'>%page%</a></span>";
        $def_template['suffix'] = "</div>";
        $def_template['prev'] = "<a href='%prev_link%' class='fx_prev_link'>← назад</a>";
        $def_template['next'] = "<a href='%next_link%' class='fx_next_link'>вперед →</a>";
        $def_template['prev_empty'] = "← назад";
        $def_template['next_empty'] = "вперед →";
        $def_template['empty'] = '';
        // $browse_msg - из системных настроек макета
        if ( !$browse_msg ) $browse_msg = array();
        // итоговый шаблон
        $template = array_merge($def_template,$browse_msg, $template_component);

        // формат url со страницей ( 'page' --> 'page10' )
        $p = fx::config()->PAGE_TPL;

        $obj_nums = $infoblock->get_total_rows();
        $cur_page = $fx_core->env->get_page();
        if ( !$cur_page ) $cur_page = 1;
        $obj_on_page = $infoblock->get_max_rows();
        $base_url = $infoblock->get_url();
        // да у нас только одна страница
        if ( $obj_nums <= $obj_on_page || !$obj_on_page ) return $template['empty'];

        $end_page = ceil ( $obj_nums / $obj_on_page );

        $prev_link = $cur_page > 1 ? $base_url.( $cur_page != 2 ? $p.($cur_page-1).'/' : '') : '';
        $next_link = $end_page != $cur_page ? $base_url.$p.($cur_page+1).'/' : '';

        for ($i = -$range; $i <= $range; $i++) {
            $page = $cur_page + $i;
            if ($page <= 0 || $page > $end_page) continue;

            $t = str_replace(array('%page%', '%url%'), array($page, $base_url.( $page != 1 ? $p.$page.'/' : '')), $template[$i ? 'unactive' : 'active']);

            $result .= "\t" . $t . "\r\n";
        }

        $result = $template['prefix'] . "\r\n" . $result . $template['suffix'];

        $result = str_replace('%prev%', $prev_link ? $template['prev'] : $template['prev_empty'], $result);
        $result = str_replace('%next%', $next_link ? $template['next'] : $template['next_empty'], $result);
        $result = str_replace('%prev_link%', $prev_link, $result);
        $result = str_replace('%next_link%', $next_link, $result);

        // переход с помощью ctrl + ←,→
        if ( $ctrl ) {
            $result .= '<script type="text/javascript">
            if (typeof jQuery != "undefined") {
                (function($) {
                    var links = { 37: $("a.fx_prev_link").attr("href"),
                                  39: $("a.fx_next_link").attr("href") };
                    $(document).keydown(function(e) {
                        if (e.ctrlKey && links[e.keyCode]) { window.location = links[e.keyCode]; }
                    });
                })(jQuery);
            }
            </script>';
        }

        return $result;
    }

}

?>