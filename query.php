<?php
require('init.php');

// Get Request
$request = new Request(array('restful' => true));

if (is_null($request->controller)) {
    http_response_code(404);
    exit;
}

// Get Controller
$controller_file = 'app/controllers/' . $request->controller . '.php';
$model_file = 'app/controllers/' . $request->controller . '.php';

if (file_exists($controller_file)) {
    require($controller_file);
    $controller_name = ucfirst($request->controller);
    $controller = new $controller_name;
} else {
    $controller = new ApplicationController();
}
if (file_exists($model_file)) {
    require($model_file);
}

// Dispatch request
echo $controller->dispatch($request);
?>
