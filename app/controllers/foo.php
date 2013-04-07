<?php
/**
 * @class Foo
 * A simple application controller extension
 */
class FooController extends ApplicationController {
    /**
     * view
     * Retrieves rows from database.
     */
    public function view() {
        $res = new Response();
        $res->success = true;
        $res->message = "success";
        $all = Foo::all($this->params);
        $res->total = $all["total"];
        $res->data  = $all["data"];
        return $res->to_json();
    }
    /**
     * create
     */
    public function create() {
        $res = new Response();
        $rec = Foo::create($this->params);
        if ($rec) {
            $res->success = true;
            $res->message = "Created new Foo " . $rec->id;
            $res->data = $rec->to_hash();
        } else {
            $res->message = "Failed to create Foo";
        }
        return $res->to_json();
    }
    /**
     * update
     */
    public function update() {
        $res = new Response();
        $rec = Foo::update($this->id, $this->params);
        if ($rec) {
            $res->data = $rec->to_hash();
            $res->success = true;
            $res->message = 'Updated Foo ' . $this->id;
        } else {
            $res->message = "Failed to find that Foo " . $this->id;
        }
        return $res->to_json();
    }
    /**
     * destroy
     */
    public function destroy() {
        $res = new Response();
        if (Foo::destroy($this->id)) {
            $res->success = true;
            $res->message = 'Destroyed Foo ' . $this->id;
        } else {
            $res->message = "Failed to destroy Foo " . $this->id;
        }
        return $res->to_json();
    }
}

?>
