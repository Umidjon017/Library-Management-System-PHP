<?php

$host = 'localhost';
$dbName = 'draft-lms_db';
$username = 'root';
$password = '';

$dsn = "mysql:host=$host;dbname=$dbName;";

$connect = new PDO($dsn, $username, $password);

session_start();
?>