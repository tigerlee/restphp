<?php
/**
 * @class Model
 * Baseclass for Models in this imaginary ORM
 */
class Model {
    public $type, $id, $attributes;
    static function create($type, $params) {
        $obj = new self($type, get_object_vars($params));
        $obj->save();
        return $obj;
    }
    static function find($table, $id) {
        global $dbh;
        $found = null;
        $rec = Model::all("*", $table, array("id" => $id))["data"][0];
        if ($rec['id'] == $id) {
            $found = new self($table, $rec);
        }
        return $found;
    }

    static function update($table, $id, $params) {
        global $dbh;
        $rec = self::find($table, $id);

        if ($rec == null) {
            return $rec;
        }
        $rec->attributes = array_merge($rec->attributes, get_object_vars($params));
        $dbh->update($table, $id, $rec->attributes);

        return $rec;
    }
    static function destroy($table, $id) {
        global $dbh;
        $rec = self::find($table, $id);

        if ($rec == null) {
            return $rec;
        }
        $dbh->destroy($table, $id);
        return $rec;
    }
    static function all($fields, $table, $params) {
        global $dbh;
        return $dbh->rs($fields, $table, $params);
    }

    public function __construct($type, $params) {
        $this->id = isset($params['id']) ? $params['id'] : null;
        $this->type = $type;
        $this->attributes = $params;
    }
    public function save() {
        global $dbh;
        $this->id = $this->attributes['id'] = $dbh->insert($this->type, $this->attributes);
    }
    public function to_hash() {
        return $this->attributes;
    }
}

