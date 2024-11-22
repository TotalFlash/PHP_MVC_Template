<?php
$dns = "mysql:dbname=test;host=localhost;charset=utf8mb4;";
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
];

$db = null;
try{
  $db = new PDO($dns, 'root', '', $options);
}
catch(PDOException $e){
  print $e->getMessage();
  die("Database connection failed");
}