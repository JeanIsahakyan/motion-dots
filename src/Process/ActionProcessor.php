<?php

namespace MotionDots\Process;

use MotionDots\Exception\ErrorException;
use MotionDots\Method\AbstractMethod;
use MotionDots\Type\BuiltinType;

/**
 * Class ActionProcessor
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class ActionProcessor {

  /**
   * @var AbstractMethod
   */
  protected $method;

  /**
   * @var string
   */
  protected $action;

  /**
   * @var array
   */
  protected $params = [];

  /**
   * @var array
   */
  protected $processed_params = [];

  /**
   * @var \ReflectionMethod
   */
  protected $action_reflection;

  /**
   * ActionProcessor constructor.
   *
   * @param AbstractMethod $method
   * @param string $action
   * @param ContextContainer $context
   * @param array $params
   *
   * @throws ErrorException
   */
  public function __construct(AbstractMethod &$method, string &$action, array &$params = []) {
    $this->method            = $method;
    $this->action            = $action;
    $this->params            = $params;
    $this->initReflection()->prepareParams();
  }

  /**
   * @throws ErrorException
   *
   * @return self
   */
  private function initReflection() {
    try {
      $this->action_reflection = new \ReflectionMethod($this->method, $this->action);
    } catch (\ReflectionException $exception) {
      throw new ErrorException(ErrorException::PARAM_REFLECTION_ERROR, $exception->getMessage());
    }
    return $this;
  }

  /**
   * @param string $param
   *
   * @return bool
   */
  private function paramExists(string $param): bool {
    return array_key_exists($param, $this->params);
  }

  /**
   * @param string $param
   *
   * @return mixed
   */
  private function getParam(string $param) {
    return $this->params[$param];
  }


  /**
   * @throws ErrorException
   *
   * @return self
   */
  private function prepareParams() {
    $params = $this->action_reflection->getParameters();
    foreach ($params as $index => $param) {
      $param_name      = $param->getName();
      $param_type      = $param->getType();
      $param_type_name = $param_type->getName();
      $resolver        = null;
      if (!$this->paramExists($param_name)) {
        if (!$param_type->allowsNull()) {
          throw new ErrorException(ErrorException::PARAM_IS_REQUIRED, "Param `{$param_name}` is required");
        } else {
          $this->processed_params[$index] = null;
          continue;
        }
      }
      $param_value = $this->getParam($param_name);
      if ($param_type->isBuiltin()) {
        $resolver = (new BuiltinType([$param_type_name, $param_value]))->parse();
      } elseif (class_exists($param_type_name)) {
        $resolver = new $param_type_name($param_value);
      } else {
        throw new ErrorException(ErrorException::PARAM_UNSUPPORTED, "Unsupported param `{$param_name}` with type `{$param_type_name}`");
      }
      if ($resolver === null) {
        throw new ErrorException(ErrorException::PARAM_UNKNOWN_RESOLVER, "Unknown resolver for param `{$param_name}` with type `{$param_type_name}`");
      }
      $this->processed_params[$index] = $resolver;
    }
    return $this;
  }

  /**
   * @param AbstractMethod $method
   * @param string $action
   * @param array $params
   *
   * @return ActionProcessor
   *
   * @throws ErrorException
   */
  public static function create(AbstractMethod $method, string $action, array $params = []): self {
    return new self($method, $action, $params);
  }

  /**
   * @return mixed
   */
  public function invoke() {
    return $this->method->{$this->action}(...array_values($this->processed_params));
  }
}
