<?
define("DEV_LOG_PATH", dirname(__FILE__).'/log');

function dev_log() {
	static $fh = false;
	if (!$fh) {
		$fh = fopen(DEV_LOG_PATH.'/log_'.md5(microtime().rand(0, 10000)).".html", 'w');
		
		$log_header = array(
			'date' => time(),
			'url' => $_SERVER['REQUEST_URI'],
			'method' => $_SERVER['REQUEST_METHOD']
		);
		fputs($fh, serialize($log_header)."\n");
	}
	$res = call_user_func_array('fen_debug', func_get_args());
	fputs($fh, $res);
}

function fen_debug() {
	$call_time = Timer::Instance()->elapsed();
	static $is_first_launch = true;
	static $last_timer_value = 0;
	$call_time_diff = $call_time - $last_timer_value;
	$last_timer_value = $call_time;
	if ($is_first_launch && false) {
		// дублируем на случай, если работаем без аутпута
		// подумать, как поймать этот случай
		?>
		<link type="text/css" href="/dcms/lib/debug/debug.css" rel="stylesheet" />
		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/dcms/lib/debug/debug.js"></script>
		<?
		//moduleOutput::addToHead('/fenrir/lib/jquery/jquery.js');
		//moduleOutput::addToHead('/fenrir/_modules_cms/debug/debug.js');
		//moduleOutput::addToHead('/fenrir/_modules_cms/debug/debug.css');
		$is_first_launch = false;
	}
	
	$result = array();
	foreach (func_get_args() as $print_item) {
		if (is_object($print_item) && $print_item instanceof DOMNode) {
			$result []= fen_pretty_xml($print_item);
		} elseif ( is_object($print_item) || is_array($print_item) ) {
			$result []= fen_print($print_item);
		} else {
			$result []= $print_item;
		}
	}
	$backtrace = debug_backtrace();
	$call = $backtrace[2];
	$print_title = " ".$call['file'];
	if (isset($call['line'])) {
		$print_title .= ' at line <b>'.$call['line'].'</b>';
	}
	if (isset($backtrace[1])) {
		$p_call = $backtrace[1];
		if (isset($p_call['function'])) {
			$method_name = isset($p_call['class']) ? $p_call['class'].'::'.$p_call['function'] : $p_call['function'];
			$print_title .= ' from '.$method_name;
		}
	}
	$print_title .= sprintf(' (+%.4f, %.4f s, %s)', $call_time_diff, $call_time, convert(memory_get_usage(true)));
	$out = "<div class='hi_pre'><div class='hi_title'>".$print_title;
	$out .= "<a onclick='fen_toggle_all(this.parentNode.parentNode)'>+</a><input /></div>";
	$out .= join('<div class="hi_sep"></div>', $result)."</div>";
	return $out;
}

function fen_print($data = '', $print_title = false) {
	ob_start();
	echo htmlspecialchars(print_r($data,1));
	$html = ob_get_clean();
	$strings = explode("\n", $html);
	if (count($strings) > 50000) {
		echo "TOO HEAVY";
		return;
	}
	unset($html);
	$result = array();
	$collapsers = array();
	$level = 0;
	foreach ($strings as $string_num => $s) {
		if (strlen($s) > 0) {
			$s = trim($s);
			$s = preg_replace("~^\s+~", '', $s);
			$s = preg_replace("~\sObject$~", '', $s);
			$s = preg_replace("~^\[(.+?)\]\s=&gt;\s?~", '<b class="pn">$1</b><span class="vs">&nbsp;:&nbsp;</span>', $s);
			$s = preg_replace('~>(.+?):(protected|private)</b>~', '><span class="$2">*</span> $1</b>', $s);
			if ($s == '(') {
				$level++;
				$c_string = '<div class="hi_collapse">';
				$collapser =& $result[count($result) - 1];
				$collapsers[$level] = array(
					'collapser' => &$collapser,
					'length' => 0
				);
			} elseif ($s == ')') {
				$c_string = '</div>';
				if (isset($collapsers[$level]) && $collapsers[$level]['length'] > 0) {
					$c_collapser = $collapsers[$level]['collapser'];
					$c_collapser = preg_replace('~^<div class="~', '<div class="hi_collapser ', $c_collapser);
					$c_collapser = preg_replace('~><span>~', '><span onclick="fen_toggle_hi_node(this)">', $c_collapser);
					$c_collapser = preg_replace("~</div>$~", " <i class='ln'>".$collapsers[$level]['length']."</i></div>", $c_collapser); 
					$collapsers[$level]['collapser'] = $c_collapser;
				}
				$level--;
			} else {
				if (preg_match("~\*RECURSION~", $s)) {
					$last_string =& $result[ count($result) - 1];
					$last_string = preg_replace('~^<div class="~', '<div class="hi_recursion ', $last_string);
					$last_string = preg_replace('~</span></div>$~', ' [RECURSION]</span></div>', $last_string);
					$c_string = '';
				} else {
					$c_string = '<div class="hi_line"><span>'.$s.'</span></div>';
					if (isset($collapsers[$level])) {
						$collapsers[$level]['length']++;
					}
				}
			}
			$result[]= $c_string;
		}
	}
	if (!empty($print_title)) {
		$print_title = "&laquo;".$print_title."&raquo; ";
	}
	return join("", $result);
}

class Timer { 
      var $start; // start time in usec 
      var $stop; // stop time in usec 
      private $marks = array();
		private $total = 0;
		private static $last_id = 0;
		private $id;
		
		protected static $instance = false;
		
		public static function Instance() {
			if (self::$instance == false) {
				self::$instance = new Timer();
				self::$instance->start();
			}
			return self::$instance;
		}
		
		public static function passed() {
			$T = self::Instance();
			return $T->elapsed();
		}
		
		function __construct() {
			$this->addMark('start');
			$this->id = self::$last_id++;
		}
		
		function addMark($name = false) {
			$t = gettimeofday();
         $mark = $t['sec'] * 1000000.0 + $t['usec'];
			if (count($this->marks) > 0) {
				$last_mark = $this->marks[count($this->marks) - 1];
				$elapsed = ($mark - $last_mark['time']) / 1000000.0;
				$this->marks[count($this->marks) - 1]['elapsed'] = $elapsed;
				$this->marks[count($this->marks) - 1]['memory'] = memory_get_usage(false);
				$this->total += $elapsed;
			}
			$this->marks []= array('time' => $mark, 'name' => $name);
		}
		
      function start() { 
         $t = gettimeofday(); 
         $this->start = $t['sec'] * 1000000.0 + $t['usec']; 
      }
   
      function stop() { 
         $t = gettimeofday(); 
         return $t['sec'] * 1000000.0 + $t['usec']; 
      } 
   
      function elapsed() {
        return ($this->stop() - $this->start) / 1000000.0; 
      }
		
		public function getTotal() {
			return $this->total;
		}
		
		public function printMarks() {
			$this->addMark('MARKS OUTPUT');
			//return "<pre>".htmlspecialchars(print_r($this->marks,1))."</pre>";
			ob_start();
			$passed = 0;
			?><table style='width:auto;' cellpadding='2' border='1' class="list">
			<tr class="h"><td><b>Event</b></td><td><b>Time spent</b></td><td><b>Total time</b></td><td><b>Memory</b></td></tr>
			<?
			$c = 0;
			foreach ($this->marks as $mark) {
				$c++;
				if (isset($mark['elapsed']) && !empty($mark['name'])) {
					$passed += $mark['elapsed'];
					$mark_name = $mark['name'];
					if (strlen($mark_name) > 80) {
						$mark_start = substr($mark_name, 0, 80);
						$tale_id = "tale_".$this->id."_".$c;
						ob_start();
						?>
						<?=substr($mark_name, 0, 80)?><a id="c_<?=$tale_id?>" onClick="$n('<?=$tale_id?>').style.display = ''; this.style.display = 'none';">...</a><span onClick="$n('c_<?=$tale_id?>').style.display = ''; this.style.display = 'none';" id="<?=$tale_id?>" style="display:none;"><?=substr($mark_name, 80)?></span>
						<?
						$mark_name = ob_get_clean();
					}
					echo sprintf("<tr><td class='time_mark_name'>%s</td><td>%.4f</td><td>%.4f</td><td>%s</td></tr>", $mark_name, $mark['elapsed'], $passed, fenFile::formatSize($mark['memory'],2));
				}
			}
			$marks = ob_get_clean();
			return $marks."</table>";
		}
    };
    
    function convert($size, $round = 3) {
	$dsize = $size;
	$sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	$total = count($sizes);
	for ($i=0; $size > 1024 && $i < $total; $i++) {
		$size = $size / 1024;
	}
	//$result = round($size,$round)." ".$sizes[$i];
	$result = $size." ".$sizes[$i];
	return $result;
}
?>