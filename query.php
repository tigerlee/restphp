<?php
///////////////////////////////////////////////////////////////////////////////
/// Configuration
///
/// $mysql_hostname = ""
/// $mysql_username = ""
/// $mysql_password = ""
/// $mysql_database = ""

/// controller->table的映射
/// $table_routes = array();
///////////////////////////////////////////////////////////////////////////////

require('init.php');

// Get Request
$request = new Request(array('restful' => true, 'url_prefix' => 'data/'));

if (is_null($request->controller)) {
    http_response_code(404);
    exit;
}

// Get Controller
$controller_file = "${plugin_path}/controllers/" . $request->controller . '.php';
$model_file = "${plugin_path}/models/" . $request->controller . '.php';

if (file_exists($model_file)) {
    require_once($model_file);
}
if (file_exists($controller_file)) {
    require_once($controller_file);
    $controller_name = ucfirst($request->controller.'Controller');
    $controller = new $controller_name($request->controller);
} else {
    $controller = new ApplicationController($request->controller);
}

// Dispatch request
echo $controller->dispatch($request);
?>
