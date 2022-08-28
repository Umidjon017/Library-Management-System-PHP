<?php
// database_connection.php

$host = "localhost";
$dbName = "php_lms_db";
$userName = "root";
$password = "";

$dsn = "mysql:host=$host;dbname=$dbName;";

$connect = new PDO($dsn, $userName, $password);

session_start();

?>