<?php
namespace MotionDots\Method\System\Response;

use MotionDots\Response\AbstractResponse;

class SystemMethodResponse extends AbstractResponse {
  public string $name;
  /**
   * @var SystemMethodParamResponse[]|null
   */
  public ?array $params;

  /**
   * @var SystemMethodParamResponse[]|SystemMethodParamResponse
   */
  public $response;

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @param string $name
   * @return SystemMethodResponse
   */
  public function setName(string $name): SystemMethodResponse {
    $this->name = $name;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getParams(): ?array {
    return $this->params;
  }

  /**
   * @param array|null $params
   * @return SystemMethodResponse
   */
  public function setParams(?array $params): self {
    $this->params = $params;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getResponse(): ?array {
    return $this->response;
  }

  /**
   * @param  SystemMethodParamResponse[]|SystemMethodParamResponse $response
   * @return SystemMethodResponse
   */
  public function setResponse($response): self {
    $this->response = $response;
    return $this;
  }

}
