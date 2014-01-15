<?php
    session_start();

    // base framework
    require(dirname(__FILE__).'/lib/controller.php');
    require(dirname(__FILE__).'/lib/model.php');
    require(dirname(__FILE__).'/lib/request.php');
    require(dirname(__FILE__).'/lib/response.php');
    require(dirname(__FILE__).'/lib/mysql.php');

    // Fake a database connection using _SESSION
    $dbh = new MySQL();
?>

