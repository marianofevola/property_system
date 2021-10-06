<?php

use Phalcon\Config\Adapter\Yaml;
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console as ConsoleApp;
use Phalcon\Loader;

// Using the CLI factory default services container
$di = new CliDI();

/**
 * Register the autoloader and tell it to register the tasks directory
 */
$loader = new Loader();

$loader->registerDirs(
  [
    __DIR__ . '/tasks',
  ]
);

$loader->register();

// Load the configuration file (if any)
$configFile = getConfig();
$di->set('config', $config);

// Create a console application
$console = new ConsoleApp();
$console->setDI($di);

/**
 * Process the console arguments
 */
$arguments = [];

foreach ($argv as $k => $arg) {
  if ($k === 1) {
    $arguments['task'] = $arg;
  } elseif ($k === 2) {
    $arguments['action'] = $arg;
  } elseif ($k >= 3) {
    $arguments['params'][] = $arg;
  }
}

try {
  // Handle incoming arguments
  $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
  // Do Phalcon related stuff here
  // ..
  fwrite(STDERR, $e->getMessage() . PHP_EOL);
  exit(1);
} catch (\Throwable $throwable) {
  fwrite(STDERR, $throwable->getMessage() . PHP_EOL);
  exit(1);
} catch (\Exception $exception) {
  fwrite(STDERR, $exception->getMessage() . PHP_EOL);
  exit(1);
}

/**
 * @return Yaml
 */
function getConfig()
{
  $environment = $_SERVER["ENV"];
  if (!$environment)
  {
    die("env not set. Usage 'ENV=dev php cli/cli.php <task name>'." . PHP_EOL);
  }

  $fileName = sprintf("%s/config/%s.yml", dirname(__DIR__), $environment);

  $envConf = new Yaml($fileName);
  $default = sprintf("%s/config/defaults.yml", dirname(__DIR__));
  $default = new Yaml($default);
  $default->merge($envConf);

  return $default;
}
