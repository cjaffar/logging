<?php session_start();

$app = require_once( dirname(__DIR__) . '/bootstrap/app.php' );

$app->run();