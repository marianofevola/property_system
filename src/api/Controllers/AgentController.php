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

  public function listTopAgentsAction()
  {
    $response = new ApiHttpResponse();
    $result = $this
      ->agentRepository
      ->findAgentsByPropertyId();

    $data = [];
    foreach ($result as $agentIds)
    {
      $data[$agentIds["property_id"]] = explode(",",$agentIds["agents"]);
    }

    // remove properties with only one agent associated
    $data = array_filter($data, function ($datum) {
      return count($datum) > 1;
    });

    $previous = [];
    $level = 1;
    $next = [];
    // todo test and improve
    foreach ($data as $propertyId => $agents)
    {
      if (empty($previous))
      {
        $previous = $agents;
        continue;
      }

      $test = array_intersect($previous, $agents);
      if (!empty($test))
      {
        $next[$level] = $test;
      }
      $level++;
    }

    if (count($next) == 1)
    {
      $response = $response->buildContent([]);
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->send();
    }

    // remove first level where agents have only 1 property in common with 1/2 agents
    unset($next[1]);
    $next = array_reverse($next);

    // get Agents
    $topAgents = $this
      ->agentRepository
      ->findByIds($next);

    $response = $response->buildContent($topAgents);
    $this
      ->response
      ->setJsonContent($response->getBody())
      ->setStatusCode($response->getCode(), $response->getMessage())
      ->send();
  }

}
