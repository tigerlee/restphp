<?php

/**
 * @class Request
 */
class Request {
    public $restful, $method, $controller, $action, $id, $params;

    public function __construct($params) {
        $this->restful = (isset($params["restful"])) ? $params["restful"] : false;
        $this->url_prefix = (isset($params["url_prefix"])) ? preg_quote($params["url_prefix"], '/') : '\/';
        $this->method = $_SERVER["REQUEST_METHOD"];
        $this->parseRequest();
    }
    public function isRestful() {
        return $this->restful;
    }
    protected function parseRequest() {
        if ($this->method == 'PUT') {   // <-- Have to jump through hoops to get PUT data
            $raw  = '';
            $httpContent = fopen('php://input', 'r');
            while ($kb = fread($httpContent, 1024)) {
                $raw .= $kb;
            }
            fclose($httpContent);
            $params = array();
            parse_str($raw, $params);

            if (isset($params['data'])) {
                $this->params =  json_decode($params['data']);
            } else {
                $params = json_decode($raw);
                $this->params = $params->data;
            }
         } else if ($this->method == 'GET') {
            $this->params = $_GET;
         } else {
            // grab JSON data if there...
            $this->params = (isset($_REQUEST['data'])) ? json_decode($_REQUEST['data']) : null;

            if (isset($_REQUEST['data'])) {
                $this->params =  json_decode($_REQUEST['data']);
            } else {
                $raw  = '';
                $httpContent = fopen('php://input', 'r');
                while ($kb = fread($httpContent, 1024)) {
                    $raw .= $kb;
                }
                #syslog(LOG_ERR, ">>>>".$raw);
                $params = json_decode($raw);
                $this->params = $params->data;
            }

        }
        $REQUEST_URI = $_SERVER["REQUEST_URI"];
        if (isset($REQUEST_URI)){
            $cai = '/^\/'.$this->url_prefix.'([a-z_]+\w)\/([a-z]+\w)\/([0-9]+)\??[^\/]*$/';  // /controller/action/id
            $ca  = '/^\/'.$this->url_prefix.'([a-z_]+\w)\/([a-z]+)\??[^\/]*$/';              // /controller/action
            $ci  = '/^\/'.$this->url_prefix.'([a-z_]+\w)\/([0-9]+)\??[^\/]*$/';              // /controller/id
            $c   = '/^\/'.$this->url_prefix.'([a-z_]+\w)\??[^\/]*$/';                        // /controller
            $i   = '/^\/'.$this->url_prefix.'([0-9]+)\??[^\/]*$/';                          // /id

            $matches = array();
            if (preg_match($cai, $REQUEST_URI, $matches)) {
                $this->controller = $matches[1];
                $this->action = $matches[2];
                $this->id = $matches[3];
            } else if (preg_match($ca, $REQUEST_URI, $matches)) {
                $this->controller = $matches[1];
                $this->action = $matches[2];
            } else if (preg_match($ci, $REQUEST_URI, $matches)) {
                $this->controller = $matches[1];
                $this->id = $matches[2];
            } else if (preg_match($c, $REQUEST_URI, $matches)) {
                $this->controller = $matches[1];
            } else if (preg_match($i, $REQUEST_URI, $matches)) {
                $this->id = $matches[1];
            }
        }
    }
}

