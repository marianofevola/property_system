<?php

namespace Api\Repositories;

use Api\Models\Agent;
use Api\Models\AgentProperty;
use Phalcon\Db\Adapter\Pdo\Mysql;
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

  public function findByIds(array $ids)
  {
    $result = Agent::find(
      [
        "conditions" => "id IN (:agentIds:)",
        "bind" => [
          "agentIds" => implode(",", array_values($ids[0]))
        ]
      ]
    );

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

  /**
   * @return array
   */
  public function findAgentsByPropertyId()
  {
    /** @var Mysql $db */
    $db = $this->getDI()->get("db");
    $query = 'SELECT property_id, GROUP_CONCAT(agent_id) as agents
FROM agent_property
GROUP BY property_id ';
    return $db->query($query)->fetchAll();
  }

}
