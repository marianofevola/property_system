<?php

namespace Api\Repositories;

use Api\Exceptions\PropertyNotAvailableException;
use Api\Models\Property;
use Phalcon\Di\Injectable;

/**
 * Class PropertyRepository
 */
class PropertyRepository extends Injectable
{
  /**
   * @param int $perPage
   * @param int $pageNo
   * @return array
   */
  public function find($pageNo, $perPage)
  {
    $result = Property::find(
      [
        "offset" => $pageNo * $perPage,
        "order" => "id",
        "limit" => $perPage
      ]
    );

    return $result->toArray();
  }

  /**
   * @param $id
   * @return \Phalcon\Mvc\ModelInterface|null
   */
  public function get($id)
  {
    return Property::findFirst($id);
  }

  /**
   * @param $name
   * @return \Phalcon\Mvc\ModelInterface|null
   */
  public function getByName($name)
  {
    return Property::findFirst([
      "conditions" => "name = :name:",
      "bind" => [
        "name" => $name
      ]
    ]);
  }

  /**
   * @param Property $property
   * @return Property
   */
  public function add(Property $property)
  {
    if ($property->create())
    {
      $property->refresh(); // also return "created" datetime that is set by mysql
    }
    return $property;
  }

  /**
   * @param Property $property
   * @return array
   * @throws PropertyNotAvailableException
   */
  public function update(Property $property)
  {
    if ($property->id)
    {
      $existingProperty = $this
        ->get($property->id);
    }
    else
    {
      // get the property by name
      $existingProperty = $this
        ->getByName($property->name);
    }

    if (!$existingProperty)
    {
      throw new PropertyNotAvailableException();
    }

    $isChanged = false;
    if ($existingProperty->name != $property->name)
    {
      $existingProperty->name = $property->name;
      $isChanged = true;
    }

    if ($existingProperty->type != $property->type)
    {
      $existingProperty->type = $property->type;
      $isChanged = true;
    }

    if ($existingProperty->price != $property->price)
    {
      $existingProperty->price = $property->price;
      $isChanged = true;
    }

    $existingProperty->update();

    return [$isChanged, $existingProperty];
  }
}
