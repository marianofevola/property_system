<?php

namespace Api\Controllers;

use Api\Shared\ApiHttpResponse;
use Phalcon\Di\Injectable;

/**
 * Class PropertyController
 *
 * @property \Api\Repositories\PropertyRepository $propertyRepository
 */
class PropertyController extends Injectable
{

  /**
   * Default values allow call with no parameters "/property/list"
   * to return the first 100 properties
   * @param int $pageNo
   * @param int $perPage
   */
  public function listAction($pageNo = 0, $perPage = 100)
  {
    $properties = $this
      ->propertyRepository
      ->getProperties($pageNo, $perPage);

    $response = new ApiHttpResponse();
    $response->buildContent($properties);
    $this
      ->response
      ->setStatusCode($response->getCode(), $response->getMessage())
      ->setJsonContent($response->getBody())
      ->send();
  }
}
