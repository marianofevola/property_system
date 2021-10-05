<?php

namespace Api\Models;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\StringLength;

class Property extends Model
{
  /** @var integer */
  public $id;

  /** @var string */
  public $name;

  /** @var string */
  public $type;

  /** @var integer */
  public $price;

  public function validation()
  {
    $validator = new Validation();

    $validator->add(
      "name",
      new StringLength(
        [
          "max" => 50,
          "min" => 2,
          "messageMaximum" => "We don't like really long names",
          "messageMinimum" => "We want more than just property initials",
          "includedMaximum" => true,
          "includedMinimum" => false,
        ]
      )
    );

    $validator
      ->add(
        "price",
        new Numericality([
          "price must be a number"
        ]));

    $validator->add(
      "price",
      new Between(
        [
          "minimum" => 20000,
          "maximum" => 999999999999999,
          "message" => "The price must be at least 20000",
        ]
      )
    );

    $validator->add(
      "type",
      new InclusionIn(
        [
          "message" => "The type must be 'flat', 'detached' or 'attached'",
          "domain" => ["flat", "detached", "attached"],
        ]
      )
    );

    return $this->validate($validator);
  }
}
