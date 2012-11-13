<?php
class Foo extends Model {
    static $table = 'foos';

    static function create($params) {
        return parent::create(self::$table, $params);
    }
    static function update($id, $params) {
        return parent::update(self::$table, $id, $params);
    }
    static function destroy($id) {
        return parent::destroy(self::$table, $id);
    }
    static function all($params) {
       $start = isset($params["start"]) ? $params["start"] : NULL;
       $limit = isset($params["limit"]) ? $params["limit"] : NULL;
       $sort  = isset($params["sort"] ) ? $params["sort"] : NULL;
       $dir   = isset($params["dir"]  ) ? $params["dir"] : NULL;
       $ordercond = is_null($sort) || is_null($dir) ? NULL : "order by {$sort} {$dir}";
       $limitcond = is_null($limit) || is_null($start) ? NULL : "limit {$start}, {$limit}";

       // get conditions
       unset($params["start"]);
       unset($params["limit"]);
       unset($params["sort"]);
       unset($params["dir"]);
       unset($params["_dc"]);
       unset($params["page"]);
       $cond = array();
       foreach ($params as $key => $value) {
           $value = mysql_real_escape_string($value);
           $cond[] = "$key = '{$value}'";
       }
       $cond = empty($cond) ? NULL : implode("AND ", $cond);
       $cond = is_null($cond) ? NULL : " WHERE " . $cond;

       // 取得总数
       $sql = "SELECT count(id) FROM " . self::$table . $cond;
       $result = mysql_query($sql);
       if (!$result) {
           die('Invalid query: ' . $sql . " - " . mysql_error());
       }
       $row = mysql_fetch_row($result);
       $ret["total"] = $row[0];
       $ret["data"] = array();

       if ($ret["total"] > 0 && $start < $ret["total"]) {
           $sql = "select {$fields} from ".self::$table." {$cond} {$ordercond} {$limitcond}";

           $result = mysql_query($sql);
           if (!$result) {
               die('Invalid query: ' . $sql . " - " . mysql_error());
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
}
?>
