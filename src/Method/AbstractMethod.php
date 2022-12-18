<?php

namespace MotionDots\Method;

use MotionDots\Process\Context;
use MotionDots\Response\ResponseInterface;

/**
 * Class AbstractMethod
 *
 * @package MotionDots\Method
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractMethod implements MethodInterface {

  /**
   * @var Context
   */
  public $context;

  public function __setContext(Context &$context) {
    $this->context = &$context;
  }

  /**
   * @param $name
   * @param $arguments
   * @return ResponseInterface
   */
  public function __call($name, $arguments): ResponseInterface {
    return $this->{$name}(...$arguments);
  }

  /**
   * @return string
   */
  public final function __toString(): string {
    $exploded_name = explode('\\', get_class($this));
    return lcfirst(array_pop($exploded_name));
  }

  /**
   * @param string $action
   *
   * @return bool
   */
  public final function __actionExists(string $action): bool {
    return in_array($action, get_class_methods($this), true);
  }
}
