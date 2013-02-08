<?php


class widget_yandexprobki extends fx_tpl_widget {

    public function record() {
        extract($this->get_vars());
        ?>
        <a href="http://clck.yandex.ru/redir/dtype=stred/pid=30/cid=<?= $this->m['cids'][0] ?>/*http://maps.yandex.ru/moscow_traffic" style="background-image:url(http://clck.yandex.ru/click/dtype=stred/pid=30/cid=<?= $this->m['cids'][1] ?>/*http://ya.ru)">
            <img src="http://info.maps.yandex.net/traffic/moscow/current_traffic_<?= $f_width ?>.gif" alt="Пробки на Яндекс.Картах" border="0"/>
        </a>
        <?
    }

    public function settings() {
        extract($this->get_vars());
        
        $c['moscow'] = array(533, 529);
        $c['saint-petersburg'] = array(1532, 1530);
        $c['yekaterinburg'] = array(1557, 1558);
        
        $this->m['cids'] = $c[$f_city];
    }
    
    public function add_form() {
        extract($this->get_vars());
        
        $city = $infoblock['settings']['city'] ? $infoblock['settings']['city'] : 'moscow';
        $width = $infoblock['settings']['width'] ? $infoblock['settings']['width'] : '175';

        return array(
            array('name' => 'city', 'label' => 'Город', 'type' => 'select', 'value' => $city, 'values' => array('moscow' => 'Москва', 'saint-petersburg' => 'Санкт-Петербург', 'yekaterinburg' => 'Екатеринбург')),
            array('name' => 'width', 'label' => 'Размер', 'type' => 'select', 'value' => $width,'values' => array('150' => '150x101', '120' => '120x110', '100' => '100x104', '234' => '234x90', '88' => '88x110'))
        );
    }

   

}
?>

