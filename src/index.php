<?php
require_once 'assets/composer/vendor/autoload.php';
ini_set('log_errors', 'on');
ini_set('display_errors', 0);
header('Access-Control-Allow-Origin: *');
require_once 'config/config.php';
require_once 'init/10_database.php';

// Session start
session_save_path('data/');

// Default controller and action
$controller = 'User';
$action = 'list';

$controllerPath = CONTROLLER_PATH . "{$controller}Controller.php";
if(file_exists($controllerPath)) {
  $controllerClass = "\\controller\\{$controller}Controller";
  require_once $controllerPath;
  if(class_exists($controllerClass)) {
    $controllerInstance = new $controllerClass($controller);
    if(method_exists($controllerInstance, $action)) {
      $controllerInstance->$action();
    } else {
      die('Method not found');
    }
  } else {
    die('Class not found');
  }
} else {
  die('Class file not found');
}

