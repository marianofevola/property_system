<?php

namespace Api\Controllers;

use Api\Exceptions\PropertyNotAvailableException;
use Api\Models\Property;
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
      ->find($pageNo, $perPage);

    $response = new ApiHttpResponse();
    $response->buildContent($properties);
    $this
      ->response
      ->setStatusCode($response->getCode(), $response->getMessage())
      ->setJsonContent($response->getBody())
      ->send();
  }

  /**
   * @param $id
   */
  public function deleteAction($id)
  {
    $property = $this
      ->propertyRepository
      ->get($id);

    $response = new ApiHttpResponse();

    if (!$property || !$property->toArray())
    {
      $response->buildBadRequest();
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();

      return;
    }

    if ($property->delete())
    {
      $response->buildNoContent();
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();

      return;
    }

    $response->buildInternalError();
    $this
      ->response
      ->setStatusCode($response->getCode(), $response->getMessage())
      ->setJsonContent($response->getBody())
      ->send();
  }

  public function addAction()
  {

    $isPost = $this->request->isPost();

    $data = [
      "name" => $isPost
        ? $this->request->getPost("name", "trim")
        : $this->request->getPut("name", "trim"),
      "type" => $isPost
        ? $this->request->getPost("type", "trim")
        : $this->request->getPut("type", "trim"),
      "price" => $isPost
        ? $this->request->getPost("price", "int")
        : $this->request->getPut("price", "int")
    ];

    $property = new Property($data);
    $response = new ApiHttpResponse();

    if ($isPost)
    {
      try
      {
        $result = $this
          ->propertyRepository
          ->add($property);
      }
      catch (\PDOException $ex)
      {
        if ($ex->getCode() === "23000") // duplicate code
        {
          $this->update($property);

          return;
        }
        $response->buildInternalError();
        $this
          ->response
          ->setStatusCode($response->getCode(), $response->getMessage())
          ->setJsonContent($response->getBody())
          ->send();

        return;
      }
      catch (\Exception $ex)
      {
        $response->buildInternalError();
        $this
          ->response
          ->setStatusCode($response->getCode(), $response->getMessage())
          ->setJsonContent($response->getBody())
          ->send();

        return;
      }
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

      if ($result->id)
      {
        $response->buildContent($result->toArray());
        $this
          ->response
          ->setStatusCode($response->getCode(), $response->getMessage())
          ->setJsonContent($response->getBody())
          ->send();
        return;
      }
      $response->buildInternalError();
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();
    }

    // must be patch
    $this->update($property);
  }

  /**
   * @param Property $property
   */
  private function update(Property $property)
  {
    $response = new ApiHttpResponse();
// docker exec property_system-php /bin/sh -c "cd /var/www/property_system/src/cli; ENV=DEV php cli.php import"
    try
    {
      list($isChanged, $updatedProperty) = $this
        ->propertyRepository
        ->update($property);
    }
    catch (PropertyNotAvailableException $ex)
    {
      $response->buildBadRequest();
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();

      return;
    }
    catch (\Exception $ex)
    {
      $response->buildInternalError();
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();

      return;
    }
    $errorMessages = $updatedProperty->getMessages();

    if ($isChanged && !$errorMessages)
    {
      $response->buildContent($updatedProperty->toArray());
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();

      return;
    }
    elseif ($errorMessages)
    {
      $response->buildErrors($errorMessages);
      $this
        ->response
        ->setStatusCode($response->getCode(), $response->getMessage())
        ->setJsonContent($response->getBody())
        ->send();

      return;
    }
    // no changes
    $response->buildNoContent();
    $this
      ->response
      ->setStatusCode($response->getCode(), $response->getMessage())
      ->setJsonContent($response->getBody())
      ->send();
  }
}
