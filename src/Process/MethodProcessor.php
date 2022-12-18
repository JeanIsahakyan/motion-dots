<?php

namespace MotionDots\Process;

use MotionDots\Exception\ErrorException;
use MotionDots\Method\AbstractMethod;
use MotionDots\Type\BuiltinType;

/**
 * Class MethodProcessor
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class MethodProcessor {

  /**
   * @var AbstractMethod
   */
  protected $method;

  /**
   * @var string
   */
  protected $action;

  /**
   * @var \ReflectionMethod
   */
  protected $reflection;

  protected ParamParser $params;

  /**
   * MethodProcessor constructor.
   *
   * @param AbstractMethod $method
   * @param string $action
   * @param array $params
   *
   * @throws ErrorException
   */
  public function __construct(AbstractMethod &$method, string &$action, array &$params = []) {
    $this->method            = &$method;
    $this->action            = &$action;
    $this->initReflection();
    $this->params            = new ParamParser($this->reflection, $this->method->context, $params);
  }

  /**
   * @throws ErrorException
   *
   * @return self
   */
  private function initReflection() {
    try {
      $this->reflection = new \ReflectionMethod($this->method, $this->action);
    } catch (\ReflectionException $exception) {
      throw new ErrorException(ErrorException::PARAM_REFLECTION_ERROR, $exception->getMessage());
    }
    return $this;
  }

  /**
   * @param AbstractMethod $method
   * @param string $action
   * @param array $params
   *
   * @return MethodProcessor
   *
   * @throws ErrorException
   */
  public static function create(AbstractMethod &$method, string &$action, array $params = []): self {
    return new self($method, $action, $params);
  }

  /**
   * @return mixed
   */
  public function invoke() {
    return $this->method->{$this->action}(...array_values($this->params->getParams()));
  }
}
