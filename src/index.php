<?php
require_once 'assets/composer/vendor/autoload.php';
require_once 'config/config.php';
require_once 'init/10_database.php';

// Session start
session_save_path( 'data/' );

// Default controller and action
$controller = 'User';
$action = 'list';

if (!empty( $_GET[ 'c' ] ) && !empty( $_GET[ 'a' ] ))
{
  $controller = $_GET[ 'c' ];
  $action = $_GET[ 'a' ];
}

$controllerPath = CONTROLLER_PATH . "{$controller}Controller.php";
if (file_exists( $controllerPath ))
{
  $controllerClass = "\\controller\\{$controller}Controller";
  require_once $controllerPath;
  if (class_exists( $controllerClass ))
  {
    $controllerInstance = new $controllerClass( $controller );
    if (method_exists( $controllerInstance, $action ))
    {
      $controllerInstance->$action();
    }
    else
    {
      die( "Method not found: $action" );
    }
  }
  else
  {
    die( "Controller class not found: $controllerClass" );
  }
}
else
{
  die( "Controller class file not found: $controllerPath ");
}

