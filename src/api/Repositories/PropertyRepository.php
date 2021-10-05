<?php

namespace Api\Repositories;

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
  public function getProperties($pageNo, $perPage)
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
}
