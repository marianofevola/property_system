<?php

namespace Api\Shared;

class ApiHttpException
{
  const KEY_CODE   = "code";
  const KEY_MESSAGE = "message";

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
      ApiHttpException::KEY_CODE    => 500,
      ApiHttpException::KEY_MESSAGE => 'Some error occurred on the server.'
    ];

    $this->code = 500;
    $this->message = "Internal Server Error";

    return $this;
  }

  public function build404()
  {
    $this->body = [
      ApiHttpException::KEY_CODE    => 404,
      ApiHttpException::KEY_MESSAGE => 'URI not found or error in request.'
    ];

    $this->code = 404;
    $this->message = "Not Found";

    return $this;
  }
}
