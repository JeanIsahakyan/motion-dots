<?php
namespace MotionDots\Process;

use MotionDots\Exception\ErrorException;
use MotionDots\Type\BuiltinType;
use MotionDots\Type\EnumType;

/**
 * Class ParamParser
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class ParamParser {
  protected $params           = [];
  protected $processed_params = [];
  protected \ReflectionMethod $reflection;
  protected Context $context;

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

  public function __construct(\ReflectionMethod &$reflection, Context &$context, &$params) {
    $this->reflection = &$reflection;
    $this->params     = &$params;
    $this->context    = &$context;
    $this->prepare();
  }

  private function prepare() {
    foreach ($this->reflection->getParameters() as $index => $param) {
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
        $resolver = (new BuiltinType([$param_type_name, $param_value], $param_name, $this->context))->parse();
      } elseif (enum_exists($param_type_name)) {
        $resolver = (new EnumType([$param_type_name, $param_value], $param_name, $this->context))->parse();
      } elseif (class_exists($param_type_name)) {
        $resolver = new $param_type_name($param_value, $param_name, $this->context);
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

  public function getParams() {
    return $this->processed_params;
  }
}
