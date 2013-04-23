<?php

class fx_db extends PDO {

    // информация о последней ошибке
    protected $last_error;
    // последний результат
    protected $last_result;
    protected $last_result_array;
    // последний запрос
    protected $last_query;
    // тип запроса ( insert, select, etc)
    protected $query_type;
    // общее число запросов
    protected $num_queries;

    public function __construct() {
        try {
            parent::__construct(fx::config()->DB_DSN, fx::config()->DB_USER, fx::config()->DB_PASSWORD);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->prefix = fx::config()->DB_PREFIX ? fx::config()->DB_PREFIX.'_' : '';
    }

    public function escape($str) {
        $str = $this->quote($str);
        // hack!
        $str = trim($str, "'");
        return $str;
    }

    public function prepare($str) {
        return $this->escape($str);
    }

    protected static $q_time = 0;
    protected static $q_count = 0;
    
    public function query($statement) {
        self::$q_count++;
    	$start_time = microtime();

        $statement = trim($this->replace_prefix($statement));

        // определение типа запроса
        preg_match("/^([a-z]+)\s+/i", $statement, $match);
        $this->query_type = strtolower($match[1]);

        $this->last_result = parent::query($statement);
        $this->num_queries++;
        $this->last_query = $statement;

        if (!$this->last_result && fx_core::get_object()->beta) {
            $this->last_error = $this->errorInfo();
            echo "<div style='border: 2pt solid red; margin: 10px; padding:10px; font-size:13px; color:black;'><br/>\n";
            echo "Query: <b>" . $statement . "</b><br/>\n";
            echo "Error: <b>" . $this->last_error[2] . "</b><br/>\n";
            echo "</div>\n";
            dev_log($statement, debug_backtrace());
        }
        $q_time = microtime() - $start_time;
        self::$q_time += $q_time;
        return $this->last_result;
        dev_log(
                '#'.self::$q_count, 
                'q_time: '.$q_time, 
                'q_total: '.self::$q_time,
                $statement
        );
        return $this->last_result;
    }

    public function get_row($query = null) {
        $res = array();

        if (!$query) {
            $res = $this->last_result_array;
        } else {
            if (($result = $this->query($query))) {
                $res = $result->fetch(PDO::FETCH_ASSOC);
                $this->last_result_array = $result->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $res;
    }

    public function get_results($query = null, $result_type = PDO::FETCH_ASSOC) {
        $res = array();

        if (!$query) {
            $res = $this->last_result_array;
        } else {
            if (($result = $this->query($query))) {
                $res = $result->fetchAll($result_type);
                $this->last_result_array = $res; // ??? $result->fetchAll($result_type);
            }
        }

        return $res;
    }

    public function get_col ( $query = null, $col_num = 0 ) {
        $res = array();

        if (!$query) {
            $res = $this->last_result_array;
        } else {
            if (($result = $this->query($query))) {
                while ($row = $result->fetch(PDO::FETCH_NUM)) {
                    $res[] = $row[ $col_num];
                }
                $this->last_result_array = $result->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $res;
    }

    public function get_var($query = null) {
        $res = array();
        
        if (!$query) {
            $res = $this->last_result_array;
        } else {
            if (($result = $this->query($query))) {
                $res = $result->fetch(PDO::FETCH_NUM);
                $res = $res[0];

                $this->last_result_array = $result->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return $res;
    }

    public function row_count() {
        return $this->last_result ? $this->last_result->rowCount() : 0;
    }

    public function insert_id() {
        return $this->lastInsertId();
    }

    public function is_error() {
        return!(bool) $this->last_result;
    }

    public function get_last_error() {
        return $this->last_error;
    }

    public function error_info() {
        return $this->errorInfo();
    }

    public function debug() {

        $last = $this->get_results();

        echo "<blockquote>";

        if ($this->last_error) {
            echo "<font face=arial size=2 color=000099><b>Last Error --</b> [<font color=000000><b>" . $this->last_error[2] . "</b></font>]<p>";
        }

        echo "<font face=arial size=2 color=000099><b>Query</b> [" . $this->num_queries . "] <b>--</b> ";
        echo "[<font color=000000><b>$this->last_query</b></font>]</font><p>";

        echo "<font face=arial size=2 color=000099><b>Query Result..</b></font>";
        echo "<blockquote>";

        if ($last) {

            // =====================================================
            // Results top rows

            echo "<table cellpadding=5 cellspacing=1 bgcolor=555555>";
            echo "<tr bgcolor=eeeeee><td nowrap valign=bottom><font color=555599 face=arial size=2><b>(row)</b></font></td>";


            foreach (array_keys($last[0]) as $name) {
                echo "<td nowrap align=left valign=top><span style='font-family: arial; font-size: 10pt; font-weight: bold;'>" . $name . "</span></td>";
            }

            echo "</tr>";

            // ======================================================
            // print main results

            if ($this->last_result) {

                $i = 0;
                foreach ($last as $one_row) {
                    $i++;
                    echo "<tr bgcolor=ffffff><td bgcolor=eeeeee nowrap align=middle><font size=2 color=555599 face=arial>$i</font></td>";

                    foreach ($one_row as $item) {
                        echo "<td nowrap><font face=arial size=2>$item</font></td>";
                    }

                    echo "</tr>";
                }
            } // if last result
            else {
                echo "<tr bgcolor=ffffff><td colspan=" . (count($this->col_info) + 1) . "><font face=arial size=2>No Results</font></td></tr>";
            }

            echo "</table>";
        } // if col_info
        else {
            echo "<font face=arial size=2>No Results</font>";
        }

        echo "</blockquote></blockquote><hr noshade color=dddddd size=1>";
    }

    protected function replace_prefix($query) {
        if ( $this->prefix ) {
            $query = preg_replace('/{{(.*?)}}/', $this->prefix . '\1', $query);
        }
        return $query;
    }

    public function column_exists($table2check, $column) {
        $sql = "SHOW COLUMNS FROM {{".$table2check."}}";
        foreach ($this->get_col($sql) as $column_name) {
            if ($column_name == $column) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * получить sql-код последнего выполненного запроса
     * @return string
     */
    public function get_last_query() {
        return $this->last_query;
    }

}

?>
