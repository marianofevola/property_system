<?php

namespace Api\Repositories;

use Api\Models\Agent;
use Api\Models\AgentProperty;
use Phalcon\Di\Injectable;

/**
 * Class AgentRepository
 */
class AgentRepository extends Injectable
{

  public function find()
  {
    $result = Agent::find();

    return $result->toArray();
  }

  /**
   * @param int $agentId
   * @param int $propertyId
   * @param string $role
   * @return AgentProperty
   */
  public function setProperty($agentId, $propertyId, $role)
  {
    $model = new AgentProperty();
    $model->agent_id = $agentId;
    $model->property_id = $propertyId;
    $model->role = $role;
    $model->create();

    return $model;
  }

}
