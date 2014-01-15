<?php
class MySQL {
    var $handler;
    var $host = "localhost";
    var $username = "root";
    var $password = "root";
    var $database = "";

    public function __construct() {
       $this->handler = mysql_connect($this->host, $this->username, $this->password);
       $this->database = $_SERVER['database_name'];
       if (!$this->handler) {
           //die('Could not connect: ' . mysql_error());
           die('{"success": false, "message": "' . $this->getErrorMsg() . '"}');
       }
       mysql_select_db($this->database, $this->handler);
       mysql_query("set names utf8");
    }

    public function __destruct() {
        mysql_close($this->handler);
    }

    public function rs($fields, $table, $params) {
       $start = $params["start"];
       $limit = $params["limit"];
       $sort  = $params["sort"];
       $dir   = $params["dir"];
       $ordercond = is_null($sort) || is_null($dir) ? NULL : "order by {$sort} {$dir}";
       $limitcond = is_null($limit) || is_null($start) ? NULL : "limit {$start}, {$limit}";

       // get conditions
       unset($params["start"]);
       unset($params["limit"]);
       unset($params["sort"]);
       unset($params["dir"]);
       unset($params["_dc"]);
       unset($params["page"]);
       unset($params["group"]);
       foreach ($params as $key => $value) {
           $value = mysql_real_escape_string($value);
           $cond[] = "$key = '{$value}'";
       }
       $cond = implode("AND ", $cond);
       $cond = is_null($cond) ? NULL : " WHERE " . $cond;


       // 取得总数
       $sql = "SELECT count(id) FROM " . $table . $cond;
       $result = mysql_query($sql);
       if (!$result) {
           //die('Invalid query: ' . $sql . " - " . mysql_error());
           die('{"success": false, "message": "' . $this->getErrorMsg() . '"}');
       }
       $row = mysql_fetch_row($result);
       $ret["total"] = $row[0];
       $ret["data"] = array();

       if ($ret["total"] > 0 && $start < $ret["total"]) {
           $sql = "select {$fields} from {$table} {$cond} {$ordercond} {$limitcond}";

           $result = mysql_query($sql);
           if (!$result) {
               //die('Invalid query: ' . $sql . " - " . mysql_error());
               die('{"success": false, "message": "' . $this->getErrorMsg() . '"}');
           }
           $fields_num = mysql_num_fields($result);
           while ($row = mysql_fetch_row($result)) {
               $record = array();
               for ($i = 0; $i < $fields_num; $i++) {
                   $record[mysql_field_name($result, $i)] = $row[$i];
               }
               array_push($ret["data"], $record);
           }
       }
       return $ret;
    }

    public function insert($table, $fields) {
        unset($fields["id"]);
        $values = array_map('mysql_real_escape_string', array_values($fields));
        $sql = sprintf('INSERT INTO %s (%s) VALUES ("%s")', $table, implode(',',array_keys($fields)), implode('","',$values));
        $result = mysql_query($sql);
        if (!$result) {
            die('{"success": false, "message": "' . $this->getErrorMsg() . '"}');
        }
        return mysql_insert_id();
    }

    public function update($table, $id, $fields) {
        foreach ($fields as $key => $value) {
            $value = mysql_real_escape_string($value);
            $updates[] = "$key = '{$value}'";
        }
        $implode = implode(", ", $updates);
        $sql = "UPDATE $table SET $implode WHERE id = '$id'";
        $result = mysql_query($sql);
        if (!$result) {
            die('{"success": false, "message": "' . $this->getErrorMsg() . '"}');
        }
    }

    public function destroy($table, $id) {
        $sql = "DELETE FROM $table WHERE id = $id";
        $result = mysql_query($sql);
        if (!$result) {
            die('{"success": false, "message": "' . $this->getErrorMsg() . '"}');
        }
    }

    public function getErrorMsg() {
        $errno = mysql_errno();
        switch ($errno) {
        case 1062:
            return "该记录已存在！";
            break;
        default:
            return mysql_errno()." ".mysql_error();
        }
    }
}
?>
