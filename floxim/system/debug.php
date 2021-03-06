<?php
class fx_debug {
    protected $dir = null;
    protected $id = null;
    protected $file = null;
    protected $start_time = null;
    protected $last_time = null;
    protected $count_entries = 0;
    protected $separator = "\n=============\n";
    protected $max_log_files = 30;
    protected $disabled = false;
    protected $head_files_added = false;
    
    public function __construct() {
        $this->id = md5(microtime().rand(0, 10000));
        $this->start_time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        $this->last_time = $this->start_time;
    }
    
    public function disable() {
        $this->disabled = true;
        if (!is_null($this->file)) {
            fclose($this->file);
            fx::files()->rm($this->_get_file_name());
            $this->file = null;
        }
    }


    protected function _get_dir() {
        if (is_null($this->dir)) {
            $this->dir = fx::path('log');
        }
        return $this->dir;
    }
    
    protected function _get_file_name($log_id = null) {
        if (is_null($log_id)) {
            $log_id = $this->id;
        }
        return $this->_get_dir().'/log_'.$log_id.".html";
    }
    
    protected function _get_index_file_name() {
        return $this->_get_dir().'/index.txt';
    }

    /**
     * Open log file and init index
     */
    protected function _start_log() {
        $this->file = fx::files()->open($this->_get_file_name(), 'w');
        register_shutdown_function(array($this, '_stop_log'));
    }

    public function _stop_log() {
        if (is_null($this->file)) {
            return;
        }
        fclose($this->file);
        $this->_write_index();
    }
    
    /**
     * Drop first (oldest) log file
     * used when there are too much files (more than $this->max_log_files)
     */
    protected function _drop_first() {
        $index_file = $this->_get_index_file_name();
        $ifh = fopen($index_file, "c+");
        $is_first = true;
        while (($line = fgets($ifh)) !== FALSE) { 
            if ($is_first) {
                $item = unserialize(trim($line));
                $first_file = $this->_get_file_name($item['id']);
                fx::files()->rm($first_file);
                $write_offset = ftell($ifh);
                $is_first = false;
            }
            if (isset($write_position)) {
                $read_position = ftell($ifh);
                fseek($ifh, $write_position);
                fputs($ifh, $line);
                fseek($ifh, $read_position);
            }
            $write_position = ftell($ifh) - $write_offset;
        }
        ftruncate($ifh, $write_position);
        fclose($ifh);
    }

    /**
     * Put log data into index
     */
    protected function _write_index() {
        if ($this->disabled) {
            return;
        }
        $c_count = $this->_get_count();
        if ($c_count >= $this->max_log_files) {
            $this->_drop_first();
        } else {
            $c_count++;
        }
        $fh_index = fx::files()->open($this->_get_index_file_name(), 'a');
        $log_header = array(
            'id' => $this->id,
            'start' => $this->start_time,
            'host' => $_SERVER['HTTP_HOST'],
            'url' => $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'time' => microtime(true) - $this->start_time,
            'count_entries' => $this->count_entries
        );
        fputs($fh_index, serialize($log_header)."\n");
        fclose($fh_index);
        $this->_set_count($c_count);
    }
    
    protected function _get_counter_file_name() {
        return $this->_get_dir().'/counter.txt';
    }
    protected function _get_count() {
        $counter_file = $this->_get_counter_file_name();
        if (!file_exists($counter_file)) {
            $c_count = 0;
        } else {
            $c_count = (int) file_get_contents($counter_file);
        }
        return $c_count;
    }
    
    protected function _set_count($count) {
        $count = (int) $count;
        if ($count < 0) {
            $count = 0;
        }
        file_put_contents($this->_get_counter_file_name(), $count);
    }
    
    public function drop_log($log_id) {
        $f = $this->_get_file_name($log_id);
        $index = $this->get_index();
        if (file_exists($f)) {
            fx::files()->rm($f);
            $ifh = fx::files()->open($this->_get_index_file_name(), 'w');
            foreach ($index as $item) {
                if ($item['id'] != $log_id) {
                    fputs($ifh, serialize($item)."\n");
                }
            }
            fclose($ifh);
            $this->_set_count( $this->_get_count() - 1);
        }
    }
    
    public function drop_all() {
        $log_files = glob($this->_get_dir().'/log*');
        $ifh = fx::files()->open($this->_get_index_file_name(), 'w');
        fputs($ifh, '');
        fclose($ifh);
        $this->_set_count(0);
        if (!$log_files) {
            return;
        }
        $own_file = fx::path()->to_abs($this->_get_file_name());
        foreach ($log_files as $lf) {
            if (fx::path()->to_abs($lf) != $own_file) {
                fx::files()->rm($lf);
            }
        }
    }

    public function get_index($id =  null) {
        $file = $this->_get_index_file_name();
        if (!file_exists($file)) {
            return array();
        }
        $index = trim(file_get_contents($file));
        if (strlen($index) == 0) {
            return array();
        }
        $items = explode("\n", $index);
        $res = array();
        foreach ($items as $item) {
            $item = unserialize($item);
            if (!is_null($id)){ 
                if ($item['id'] == $id) {
                    return $item;
                }
                continue;
            }
            if (is_array($item)) {
                $res[]= $item;
            }
        }
        if (!is_null($id)) {
            return false;
        }
        $res = array_reverse($res);
        return $res;
    }
    
    public function show_item($item_id) {
        ob_start();
        $file = $this->_get_dir().'/log_'.$item_id.'.html';
        $fh = fopen($file, 'r');
        $entry = '';
        while (!feof($fh)) {
            $s = fgets($fh);
            if (trim($s) == trim($this->separator)) {
                $this->_print_entry(unserialize($entry));
                $entry = '';
            }else {
                $entry .= $s;
            }
        }
        $this->_print_entry(unserialize($entry));
        fclose($fh);
        
        /*$entries = explode($this->separator, file_get_contents($file));
        foreach ($entries as $entry) {
            $this->_print_entry(unserialize($entry));
        }*/
        return ob_get_clean();
    }

    /**
     * Put args into log
     */
    public function log() {
        if (defined("FX_ALLOW_DEBUG") && !FX_ALLOW_DEBUG) {
            return;
        }
        if ($this->disabled) {
            return;
        }
        if (is_null($this->file)) {
            $this->_start_log();
        } else {
            fputs($this->file, $this->separator);
        }
        fputs(
            $this->file, 
            serialize(
                call_user_func_array(
                    array($this, '_entry'), 
                    func_get_args()
                )
            )
        );
        $this->count_entries++;
    }
    
    /**
     * Print args to the output
     */
    public function debug() {
        $e = call_user_func_array(array($this, '_entry'), func_get_args());
        $this->_print_entry($e);
        if (!$this->head_files_added) {
            fx::page()->add_css_file(fx::path('floxim', 'admin/skins/default/css/debug.less'));
            fx::page()->add_js_file(FX_JQUERY_PATH);
            fx::page()->add_js_file('/floxim/admin/js/fxj.js');
            fx::page()->add_js_file(fx::path('floxim', 'admin/js/debug.js'));
        }
    }
    
    protected function _entry() {
        $c_time = microtime(true);
        $memory = memory_get_usage(true);
        
        $backtrace = array_slice(debug_backtrace(), 4, 2);
        
        $meta = array(
            'time' => $c_time - $this->start_time, 
            'passed' => $c_time - $this->last_time,
            'memory' => $memory
        );
        $this->last_time = $c_time;
        if (isset($backtrace[0]['file'])) {
            $meta['file'] = $backtrace[0]['file'];
            $meta['line'] = $backtrace[0]['line'];
        }
        
        $caller = '';
        if (isset($backtrace[1])) {
            if (isset($backtrace[1]['class'])) {
                $caller = $backtrace[1]['class'];
                $caller .= $backtrace[1]['type'];
            }
            if (isset($backtrace[1]['function'])) {
                $caller .= $backtrace[1]['function'];
            }
        }
        $meta['caller'] = $caller;
        
        $args = func_get_args();
        $items = array();
        foreach ($args as $a) {
            $type = gettype($a);
            if ($type == 'array' || $type == 'object') {
                $a = print_r($a,1);
            }
            $items[]= array($type, $a);
        }
        return array($meta, $items);
    }
    
    protected function _print_entry($e) {
        $meta = $e[0];
        $file = isset($meta['file']) ? $meta['file'] : false;
        $line = isset($meta['line']) ? $meta['line'] : false;
        ?>
        <div class='fx_debug_entry'>
            <div class='fx_debug_title'>
                <?php echo $file; ?>
                <?php if ($line !== false) {
                    ?> at line <b><?php echo $line ?></b><?
                }
                echo sprintf(
                    ' (+%.5f, %.5f s, %s)', 
                    $meta['passed'], 
                    $meta['time'], 
                    self::convert_memory($meta['memory'])
                );
                ?>
            </div>
            <?php foreach ($e[1] as $n => $item) { 
                ob_start();
                if (in_array($item[0], array('array', 'object'))) {
                    echo $this->_print_format($item[1]);
                } else {
                    if (strstr($item[1], "\n")) {
                        echo '<pre>'.htmlspecialchars($item[1]).'</pre>';
                    } else {
                        echo '<pre class="fx_debug_one_line">'.htmlspecialchars($item[1]).'</pre>';
                    }
                }
                $printed = ob_get_clean();
                echo preg_replace_callback(
                    '~\[\[(good|bad)\s(.+?)\]\]~',
                    function($matches) {
                        return '<span class="fx_debug_'.$matches[1].'">'.$matches[2].'</span>';
                    },
                    $printed
                );
                if ($n < count($e[1]) - 1) { ?>
                    <div class="fx_debug_separator"></div>
                <?php } ?>
            <?php } ?>
        </div>
        <?php
    }
    
    public static function convert_memory($size, $round = 3) {
        $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $total = count($sizes);
        for ($i=0; $size > 1024 && $i < $total; $i++) {
            $size = $size / 1024;
        }
        $result = $size." ".$sizes[$i];
        return $result;
    }
    
    protected function _print_format($html) {
	$strings = explode("\n", htmlspecialchars($html));
        unset($html);
	$result = array();
	$collapsers = array();
	$level = 0;
	foreach ($strings as $string_num => $s) {
            if (strlen($s) > 0) {
                $init_line = $s;
                $s = trim($s);
                $is_index = preg_match("~^\s*\[.+\]~", $s);
                
                $s = preg_replace("~\sObject$~", '', $s);
                $s = preg_replace("~^\[(.+?)\]\s=&gt;\s?~", '<b class="pn">$1</b><span class="vs">&nbsp;:&nbsp;</span>', $s);
                $s = preg_replace('~>(.+?):(protected|private)</b>~', '><span class="$2">*</span> $1</b>', $s);
                if ($s == '(') {
                    $level++;
                    $c_string = '<div class="fx_debug_collapse">';
                    $collapser =& $result[count($result) - 1];
                    $collapsers[$level] = array(
                        'collapser' => &$collapser,
                        'length' => 0
                    );
                } elseif ($s == ')') {
                    $c_string = '</div>';
                    if (isset($collapsers[$level]) && $collapsers[$level]['length'] > 0) {
                        $c_collapser = $collapsers[$level]['collapser'];
                        $c_collapser = preg_replace('~^<div class="~', '<div class="fx_debug_collapser ', $c_collapser);
                        $c_collapser = preg_replace("~</div>$~", " <i class='ln'>".$collapsers[$level]['length']."</i></div>", $c_collapser); 
                        $collapsers[$level]['collapser'] = $c_collapser;
                    }
                    $level--;
                } else {
                    if (preg_match("~\*RECURSION~", $s)) {
                        $last_string =& $result[ count($result) - 1];
                        $last_string = preg_replace('~^<div class="~', '<div class="fx_debug_recursion ', $last_string);
                        $last_string = preg_replace('~</span></div>$~', ' [RECURSION]</span></div>', $last_string);
                        $c_string = '';
                    } else {
                        $c_string = '<div class="fx_debug_line"><span>'
                                    .($is_index || $string_num == 0 ? $s : $init_line)
                                    .'</span></div>';
                        if (isset($collapsers[$level]) && $is_index) {
                            $collapsers[$level]['length']++;
                        }
                    }
                }
                $result[]= $c_string;
            }
	}
	return join("", $result);
    }
}