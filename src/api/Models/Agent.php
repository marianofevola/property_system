<?php

namespace Api\Models;

use Phalcon\Mvc\Model;

class Agent extends Model
{
  /** @var integer */
  public $id;

  /** @var string */
  public $first_name;

  /** @var string */
  public $last_name;

  /** @var string */
  public $phone;

  /** @var string */
  public $email;

}
