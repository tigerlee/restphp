<?php
    session_start();

    // base framework
    require(dirname(__FILE__).'/controller.php');
    require(dirname(__FILE__).'/model.php');
    require(dirname(__FILE__).'/request.php');
    require(dirname(__FILE__).'/response.php');
    require(dirname(__FILE__).'/mysql.php');

    // Fake a database connection using _SESSION
    $dbh = new MySQL();
?>

