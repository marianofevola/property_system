<?php

namespace Api\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn;

class AgentProperty extends Model
{
  /** @var integer */
  public $agent_id;

  /** @var string */
  public $property_id;

  /** @var string */
  public $role;

  public function initialize()
  {
    $this->setSource('agent_property');
  }

  public function validation()
  {
    $validator = new Validation();

    $validator->add(
      "role",
      new InclusionIn(
        [
          "message" => "The role must be 'viewing' or 'selling'",
          "domain" => ["viewing", "selling"],
        ]
      )
    );

    return $this->validate($validator);
  }
}
