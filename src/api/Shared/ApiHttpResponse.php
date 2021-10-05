<?php

namespace Api\Shared;

class ApiHttpResponse
{
  const KEY_CODE   = "code";
  const KEY_MESSAGE = "message";
  const KEY_ERROR_MESSAGE = "error_message";

  /** @var integer */
  private $code;

  /** @var string */
  private $message;

  /** @var array */
  private $body;

  /**
   * @return int
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * @return array
   */
  public function getBody()
  {
    return $this->body;
  }

  /**
   * @return $this
   */
  public function buildInternalError()
  {
    $this->body = [
      ApiHttpResponse::KEY_CODE    => 500,
      ApiHttpResponse::KEY_MESSAGE => "Some error occurred on the server."
    ];

    $this->code = 500;
    $this->message = "Internal Server Error";

    return $this;
  }

  /**
   * @return $this
   */
  public function build404()
  {
    $this->body = [
      ApiHttpResponse::KEY_CODE    => 404,
      ApiHttpResponse::KEY_MESSAGE => "URI not found or error in request."
    ];

    $this->code = 404;
    $this->message = "Not Found";

    return $this;
  }

  /**
   * @param $result
   * @return $this
   */
  public function buildContent($result)
  {
    $this->body = $result;

    $this->code = 200;
    $this->message = "OK";

    return $this;
  }

  /**
   * @return $this
   */
  public function buildBadRequest()
  {
    $this->body = [
      ApiHttpResponse::KEY_CODE    => 400,
      ApiHttpResponse::KEY_MESSAGE => "Invalid request provided."
    ];
    $this->code = 400;
    $this->message = "Bad Request";

    return $this;
  }

  /**
   * @return $this
   */
  public function buildNoContent()
  {
    $this->code = 204;
    $this->message = "No Content";

    return $this;
  }

  /**
   * @param array $errors
   * @return $this
   */
  public function buildErrors(array $errors)
  {
    $message = [];
    foreach ($errors as $error)
    {
      $message[] = $error->getMessage();
    }

    $this->body = [
      ApiHttpResponse::KEY_CODE    => 400,
      ApiHttpResponse::KEY_ERROR_MESSAGE => $message
    ];
    $this->code = 400;
    $this->message = "Bad Request";

    return $this;
  }
}
