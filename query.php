<?php
require('init.php');

// Get Request
$request = new Request(array('restful' => true, 'url_prefix' => 'data/'));

if (is_null($request->controller)) {
    http_response_code(404);
    exit;
}

// Get Controller
$controller_file = 'app/controllers/' . $request->controller . '.php';
$model_file = 'app/models/' . $request->controller . '.php';

if (file_exists($model_file)) {
    require_once($model_file);
}
if (file_exists($controller_file)) {
    require_once($controller_file);
    $controller_name = ucfirst($request->controller.'Controller');
    $controller = new $controller_name;
} else {
    $controller = new ApplicationController();
}

// Dispatch request
echo $controller->dispatch($request);
?>
