<?php

/**
 * Description of router_admin
 *
 * @author Nikita Dezzpil Orlov <n.dezz.orlov@gmail.com>
 */
class fx_router_admin extends fx_router {
    
    public function route($url = null, $context = null) {

        $regexp = "/((floxim\/)+|(floxim\/index.php)+)$/";
        if (!preg_match($regexp, $url)) {
            return null;
        }

        $fx_core = fx_core::get_object();
        $fx_core->modules->load_env();
        $input = $fx_core->input->make_input();
        
        if (empty($_REQUEST)) 
        {
            // параметров запроса нет, идем стандартной 
            // для всех контроллеров дорогой
            return new fx_controller_admin($input);
        }
        
        // НИЖЕ - остатки старой админки. Руины, загромождающие
        // площадку для понятного кода. Админка в плане задумывалась как набор
        // контроллеров, которые лежат в /floxim/admin/controllers/
        
        $essence = $fx_core->input->fetch_post('essence');
        $action = $fx_core->input->fetch_post('action');
        $fx_admin = $fx_core->input->fetch_post('fx_admin');
        $posting = $fx_core->input->fetch_post('posting');
        
        if ($fx_admin) {
            $essence = 'admin_'.$essence;
        }

        if ($posting && $posting !== 'false') {
            $action .= "_save";
        }
        
        if (!$essence || $essence == 'admin') {
            // Если сущность, к которой идет post запрос
            // не указано, то просто возвращаем контроллер,
            // как и положено. Так то
            return new fx_controller_admin($input);
        }
        
        $classname = 'fx_controller_' . $essence;
        try {
            $controller = new $classname($input, $action);
        } catch (Exception $e) {
            die("Error! Essence: " . htmlspecialchars($essence));
        }

        $fx_buffer = function($str) {
            $fx_core = fx_core::get_object();
            $str = $fx_core->page->post_proccess($str);
            return $str;
        };

        // мутные контроллеры не умеют вызывать
        // post_postprocess самостоятельно
        ob_start($fx_buffer);
        $controller->process();
        $str = ob_get_clean();
        echo $str;
        die();


    }

}
?>
