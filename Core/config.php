<?php
session_start();
set_time_limit(60);
error_reporting(0);
$_SESSION['lastRef'] = [];

define('RQ_DEF', '/phpexcel/');
define('TIME_NOW', date('Y-m-d H:i:s'));

define('CHARSET', 'utf-8');
/**
* Constants for database
*/
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'mcd');

