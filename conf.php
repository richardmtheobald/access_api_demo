<?php

date_default_timezone_set('America/Chicago');

spl_autoload_register(function ($class) {
	require_once($_SERVER['DOCUMENT_ROOT'] . '/rtdemo/classes/' . $class . '.class.php');
});

//database object information
defined("DB_USER") ? null: define("DB_USER","root");
defined("DB_PASSWORD") ? null : define("DB_PASSWORD","mysql");
defined("DB_HOST") ? null : define("DB_HOST","127.0.0.1");
defined("DB_NAME") ? null : define("DB_NAME","rtDemo");

$dbCon = new rtDbCon(DB_HOST,DB_NAME,DB_USER,DB_PASSWORD);

?>