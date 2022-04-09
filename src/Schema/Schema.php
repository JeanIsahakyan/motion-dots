<?php
namespace MotionDots\Schema;

use MotionDots\Exception\ErrorException;
use MotionDots\Method\AbstractMethod;

/**
 * Class Schema
 *
 * @package MotionDots\Schema
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class Schema {

  /**
   * @var AbstractMethod[]
   */
  protected $methods = [];

  public static function create(): self {
    return new self();
  }

  /**
   * @param string $method
   *
   * @return bool
   */
  public function methodExists(string $method): bool {
    return array_key_exists($method, $this->methods);
  }

  /**
   * @param AbstractMethod $method
   *
   * @throws ErrorException
   */
  public function addMethod(AbstractMethod $method): self {
    $method_name = (string)$method;
    if ($this->methodExists($method_name)) {
      throw new ErrorException(ErrorException::SCHEMA_METHOD_EXISTS, "Methods for `{$method_name}` already exists.");
    }
    $this->methods[$method_name] = $method;
    return $this;
  }

  /**
   * @param AbstractMethod[] $methods
   *
   * @throws ErrorException
   */
  public function addMethods(array $methods): self {
    foreach ($methods as $method) {
      $this->addMethod($method);
    }
    return $this;
  }

  /**
   * @param string $method
   *
   * @return AbstractMethod
   */
  public function getMethod(string $method): AbstractMethod {
    return $this->methods[$method];
  }

  /**
   * @return AbstractMethod[]
   */
  public function getMethods(): array {
    return $this->methods;
  }
}
