<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\View;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH );

// Register an autoloader
$loader = new Loader();
$loader->registerDirs(
  [
    APP_PATH . '/controllers/',
    APP_PATH . '/models/',
  ]
)->register();

// Create a DI
$di = new FactoryDefault();

// Setting up the view component
$di['view'] = function () {
  $view = new View();
  $view->setViewsDir(APP_PATH . '/Views/');
  return $view;
};

// Handle the request
try {
  $application = new Application($di);
  echo $application->handle()->getContent();
} catch (Exception $e) {
  echo "Exception: ", $e->getMessage();
}
