<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\Request\Exception;
use Phalcon\Http\Response;
use Phalcon\Mvc\Micro;

$di = new FactoryDefault();
$app = new Micro();

require __DIR__ . '/../vendor/autoload.php';

$di
  ->setShared("response", function (){
    $response = new Response();
    $response->setContentType("application/json", "utf-8");
    return $response;
  });

$app->setDI($di);

// Define the routes here
$app->get(
  '/v1/properties/{pageNo:[0-9]+}/{perPage:[0-9]+}',
  function ($pageNo, $perPage) {
    echo "called " . $perPage . $pageNo;
  }
);

$app->notFound(function () use ($app) {
  $error = (new Api\Shared\ApiHttpException())
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
catch (Api\Shared\ApiHttpException $ex)
{
  $response = $app->response;
  $response->setStatusCode($ex->getCode(), $ex->getMessage());
  $response->setJsonContent($ex->getAppError());
  $response->send();
}
catch (Exception $ex)
{
  // Obfuscate error
  $error = (new Api\Shared\ApiHttpException())
    ->buildInternalError();

  // Sending error response
  $app
    ->response
    ->setStatusCode($error->getCode(), $error->getMessage())
    ->setJsonContent($error->getBody())
    ->send();
}
