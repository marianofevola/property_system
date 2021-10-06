<?php

use Phalcon\Config\Adapter\Yaml;
use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Request\Exception;
use Phalcon\Http\RequestInterface;
use Phalcon\Http\Response;
use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;


$di = new FactoryDefault();
$app = new Micro();

require __DIR__ . '/../vendor/autoload.php';

$di->setShared("config", getConfig($app->request));

$di
  ->setShared("response", function ()
  {
    $response = new Response();
    $response->setContentType("application/json", "utf-8");
    $response->setHeader("Access-Control-Allow-Origin", "*");
    $response->setHeader("Access-Control-Allow-Methods", "POST, GET, OPTIONS");
    return $response;
  });

$config = $di->get("config");
$di->set(
  "db",
  function () use ($config) {
    return new PdoMysql(
      [
        "host"     => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname"   => $config->database->dbname,
      ]
    );
  }
);

$di->set(
  'propertyRepository',
  function ()
  {
    return new \Api\Repositories\PropertyRepository($this);
  }
);

$di->set(
  'agentRepository',
  function ()
  {
    return new \Api\Repositories\AgentRepository($this);
  }
);

$app->setDI($di);

$propertyCollection = new Micro\Collection();
$propertyCollection->setHandler(\Api\Controllers\PropertyController::class, true);
$propertyCollection->setPrefix("/property");
/** @see \Api\Controllers\PropertyController::listAction() */
$propertyCollection->get("/list", "listAction");
$propertyCollection->get("/list/{pageNo:[0-9]+}/{perPage:[0-9]+}", "listAction");
/** @see \Api\Controllers\PropertyController::deleteAction() */
$propertyCollection->delete("/{id:[0-9]+}", "deleteAction");
/** @see \Api\Controllers\PropertyController::addAction() */
$propertyCollection->post("/", "addAction");
$propertyCollection->put("/", "addAction");
$app->mount($propertyCollection);

$agentCollection = new Micro\Collection();
$agentCollection->setHandler(\Api\Controllers\AgentController::class, true);
$agentCollection->setPrefix("/agent");
/** @see \Api\Controllers\AgentController::listAction() */
$agentCollection->get("/list", "listAction");
/** @see \Api\Controllers\AgentController::linkPropertyAction() */
$agentCollection->post("/property", "linkPropertyAction");
/** @see \Api\Controllers\AgentController::listTopAgentsAction() */
$agentCollection->get("/top", "listTopAgentsAction");
$app->mount($agentCollection);

$app->notFound(function () use ($app)
{
  $error = (new Api\Shared\ApiHttpResponse())
    ->build404();
  $app
    ->response
    ->setStatusCode($error->getCode(), $error->getMessage())
    ->setJsonContent($error->getBody())
    ->send();
});

try
{
  $app->handle();
}
catch (Api\Shared\ApiHttpResponse $ex)
{
  $response = $app->response;
  $response->setStatusCode($ex->getCode(), $ex->getMessage());
  $response->setJsonContent($ex->getAppError());
  $response->send();
}
catch (Exception $ex)
{
  // Obfuscate error
  $error = (new Api\Shared\ApiHttpResponse())
    ->buildInternalError();

  // Sending error response
  $app
    ->response
    ->setStatusCode($error->getCode(), $error->getMessage())
    ->setJsonContent($error->getBody())
    ->send();
}

/**
 * @param RequestInterface $request
 * @return Yaml
 */
function getConfig(RequestInterface $request)
{
  $environment = $request->getServer("ENV");
  $fileName = sprintf("../config/%s.yml", $environment);
  $envConf = new Yaml($fileName);
  $default = new Yaml("../config/defaults.yml");
  $default->merge($envConf);

  return $default;
}
