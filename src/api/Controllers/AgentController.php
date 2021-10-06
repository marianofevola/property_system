<?php

namespace Api\Controllers;

use Api\Shared\ApiHttpResponse;
use Phalcon\Di\Injectable;

/**
 * Class AgentController
 *
 * @property \Api\Repositories\AgentRepository $agentRepository
 */
class AgentController extends Injectable
{
  public function listAction()
  {
    $properties = $this
      ->agentRepository
      ->find();

    $response = new ApiHttpResponse();
    $response->buildContent($properties);
    $this
      ->response
      ->setStatusCode($response->getCode(), $response->getMessage())
      ->setJsonContent($response->getBody())
      ->send();
  }

  public function linkPropertyAction()
  {
    $response = new ApiHttpResponse();

    $agentId = $this->request->getPost("agent_id", "int");
    $propertyId = $this->request->getPost("property_id", "int");
    $role = $this->request->getPost("role", "trim");

    $result = $this->agentRepository->setProperty($agentId, $propertyId, $role);
    $errorMessages = $result->getMessages();
    if (!empty($errorMessages))
    {
      $response->buildErrors($errorMessages);
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();

      return;
    }

    $response = $response->buildContent($result->toArray());
    $this
      ->response
      ->setStatusCode($response->getCode(), $response->getMessage())
      ->send();

  }

}
