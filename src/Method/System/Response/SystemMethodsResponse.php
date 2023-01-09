<?php
namespace MotionDots\Method\System\Response;

use MotionDots\Response\AbstractResponse;

class SystemMethodsResponse extends AbstractResponse {
  public ?string $name;
  /**
   * @var SystemMethodResponse[]|null
   */
  public ?array $methods;

  /**
   * @return string|null
   */
  public function getName(): ?string {
    return $this->name;
  }

  /**
   * @param string|null $name
   * @return SystemMethodsResponse
   */
  public function setName(?string $name): SystemMethodsResponse {
    $this->name = $name;
    return $this;
  }

  /**
   * @return array|null
   */
  public function getMethods(): ?array {
    return $this->methods;
  }

  /**
   * @param SystemMethodResponse[]|null $methods
   * @return SystemMethodsResponse
   */
  public function setMethods(?array $methods): SystemMethodsResponse {
    $this->methods = $methods;
    return $this;
  }

}
