<?php

class fx_route {

    protected $path;
    protected $e404 = false;

    public function __construct($path) {
        $this->path = $path;
    }

    public function attempt_to_redirect() {
        $fx_core = fx_core::get_object();

        $url = str_replace(array("http://", "https://", "www."), "", fx::config()->HTTP_HOST);
        $url .= $fx_core->REQUEST_URI;

        $where = " checked = 1 AND  ? LIKE REPLACE(REPLACE(old_url ,'_','\\\_'),'*','%') ";
        $res = fx::data('redirect')->get($where, array($url));

        if (!$res) {
            return;
        }

        $old_url = str_replace('*', '([[:alnum:]]+)', $res['old_url'], $count);
        if ($count) {
            $new_url = preg_replace('@'.$old_url.'@i', $res['new_url'], $url);
        } else {
            $new_url = $res['new_url'];
        }

        header("Location: http://".$new_url, true, $res['header']);
        echo "<meta http-equiv='refresh' content='0;url=http://".$new_url."'>";
        exit;
    }

    public function resolve() {
        $fx_core = fx_core::get_object();

        $site = $fx_core->env->get_site();
        $cat_id = $site->get_id();
        $title_sub_id = $site->get_title_sub_id();

        // определение страницы
        $match = false;
        $page = 0;
        preg_match("/\/".preg_quote(fx::config()->PAGE_TPL, '/')."([0-9]+)\//iu", $this->path, $match);
        if ($match) {
            $page = $match[1];
            $this->path = str_replace(ltrim($match[0], '/'), '', $this->path);
        }

        $req_file = strrchr($this->path, '/');

        //$action = 'index';
        // титульная страница
        if ($this->path == "/" || $this->path == "") {
            $sub_env = fx::data('subdivision')->get_by_id($title_sub_id);
        } else {
            $uri = rtrim(substr($this->path, 0, strrpos($this->path, '/')), '/').'/';
            $sub_env = fx::data('subdivision')->get_by_uri($uri, $cat_id);
        }

        // 404 page not found
        if (!$sub_env) {
            $this->e404 = true;
            $sub_env = $site->get_404_sub();
        }

        $infoblocks = fx::data('infoblock')->get_all('subdivision_id', $sub_env->get_id());

        $ibs_env = array();


        if ($req_file != '/') {
            $req_file = substr($req_file, 1);
            $fext = '';
            if (strpos($req_file, '.')) {
                $req_file_parts = explode(".", $req_file);
                $fname = $req_file_parts[0];
                $fext = strtolower($req_file_parts[count($req_file_parts) - 1]);
            }

            preg_match("/^((add|search|index)_)?((edit|delete|drop)_)?([a-z0-9-]+)(_([0-9]+))?$/i", $fname, $match);

            do {
                if (!$match) {
                    $this->e404 = true;
                    break;
                }

                $action = $match[2];
                $content_action = $match[4];
                $keyword = $match[5];
                $content_id = $match[7];

                if ($action && $content_id) {
                    $this->e404 = true;
                    break;
                }

                // действие с объектом по keyword
                if (!$action && !$content_id) {
                    $mes_result = $this->find_content_by_keyword($keyword, $infoblocks);
                    if (!$mes_result) {
                        $this->e404 = true;
                        break;
                    }
                    $content_id = $mes_result['content']['id'];
                    $ibs_env = array($mes_result['infoblock']);
                    $action = $content_action ? $content_action : 'full';
                } else if ($action || $content_id) {
                    $current_infoblock = false;
                    foreach ($infoblocks as $infoblock) {
                        if ($infoblock['url'] == $keyword) {
                            $current_infoblock = $infoblock;
                        }
                    }

                    if (!$current_infoblock) {
                        $this->e404 = true;
                        break;
                    }
                    $ibs_env = array($current_infoblock);
                    if ($content_id) {
                        $content = $this->get_content($content_id, $current_infoblock);
                        if ( !$content ) {
                            $this->e404 = true;
                            break;
                        }
                        $this->attempt_to_redirect_content($content, $uri);
                        $action = $content_action ? $content_action : 'full';
                    }
                }
            } while (false);
        } else {
            $ibs_env = $infoblocks;
        }

        if ($this->e404) {
            $sub_env = $site->get_404_sub();
        }

        if ($content_id) {
            $fx_core->page->add_data_js('content_id', $content_id);
        }
        if ($action) $fx_core->page->add_data_js('action', $action);

        $result = array('sub_env' => $sub_env, 'ibs_env' => $ibs_env, 'content_id' => $content_id, 'action' => $action);
        if ($page) $result['page'] = $page;

        return $result;
    }

    protected function get_content ( $id, $infoblock ) {
        $fx_core = fx_core::get_object();
        $result = false;
        if ($infoblock['type'] == 'content' && $infoblock['essence_id']) {
        	if ($infoblock['essence_id'] == 1) { // USER
        		$result = fx::data('user')->get('id', $id);
        	} else {
				$result = fx::data('content')->set_component($infoblock['essence_id'])->get('id', $id);
			}
        }
        return $result;
    }

    protected function find_content_by_keyword($keyword, $infoblocks) {
        $fx_core = fx_core::get_object();
        $result = false;
        foreach ($infoblocks as $infoblock) {
            if ($infoblock['type'] == 'content' && $infoblock['essence_id']) {
                $content = $fx_core->content->get($infoblock['essence_id'], 'keyword', $keyword);
                if ($content) {
                    $result['content'] = $content;
                    $result['infoblock'] = $infoblock;
                    break;
                }
            }
        }
        return $result;
    }

    protected function attempt_to_redirect_content ( $content, $uri ) {
        if (!fx::config()->REDIRECT_FULL_MESSAGE || !$content['keyword']) {
            return false;
        }
        $new_url = $uri.$content['keyword'].'.html';

        header("Location: ".$new_url, true, 301);
        echo "<meta http-equiv='refresh' content='0;url=".$new_url."'>";
        exit;
    }


}

?>
