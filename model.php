<?php
/**
 * @class Model
 * Baseclass for Models in this imaginary ORM
 */
class Model {
    public $table_, $id, $attributes;

    public function __construct($table_, $params) {
        $this->id = isset($params['id']) ? $params['id'] : null;
        $this->table_ = $table_;
        $this->attributes = $params;
    }

    static function create($table_, $params) {
        $obj = new self($table_, get_object_vars($params));
        $obj->save();
        return $obj;
    }
    static function find($table_, $id) {
        global $dbh;
        $found = null;
        $data = Model::all("*", $table_, array("id" => $id));
        $rec = $data["data"][0];
        if ($rec['id'] == $id) {
            $found = new self($table_, $rec);
        }
        return $found;
    }

    static function update($table_, $id, $params) {
        global $dbh;
        $rec = self::find($table_, $id);

        if ($rec == null) {
            return $rec;
        }
        $rec->attributes = array_merge($rec->attributes, get_object_vars($params));
        $dbh->update($table_, $id, $rec->attributes);

        return $rec;
    }
    static function destroy($table_, $id) {
        global $dbh;
        $rec = self::find($table_, $id);

        if ($rec == null) {
            return $rec;
        }
        $dbh->destroy($table_, $id);
        return $rec;
    }
    static function all($fields, $table_, $params) {
        global $dbh;
        return $dbh->rs($fields, $table_, $params);
    }

    public function save() {
        global $dbh;
        $this->id = $this->attributes['id'] = $dbh->insert($this->table_, $this->attributes);
    }
    public function to_hash() {
        return $this->attributes;
    }
}

?>

