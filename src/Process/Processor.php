<?php

namespace MotionDots\Process;

use MotionDots\Exception\ErrorException;
use MotionDots\Method\System;
use MotionDots\Schema\Schema;

/**
 * Class Processor
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan jeanisahakyan@gmail.com
 */
class Processor {

  /**
   * @var ContextContainer
   */
  protected $context;

  /**
   * @var Schema
   */
  protected $schema;

  /**
   * @var string
   */
  protected $method_action_separator;

  /**
   * Processor constructor.
   *
   * @param Schema $schema
   * @param string $method_and_action_separator
   */
  public function __construct(Schema $schema, string $method_and_action_separator = '.', bool $disable_system_methods = false) {
    $this->schema                  = $schema;
    if (!$disable_system_methods) {
      $this->schema->addMethod(new System($this->schema, $method_and_action_separator));
    }
    $this->context                 = new ContextContainer();
    $this->method_action_separator = $method_and_action_separator;
  }

  /**
   * @param string $method_and_action
   *
   * @throws ErrorException
   *
   * @return array
   */
  private function unpackMethodAndAction(string $method_and_action) {
    $result = explode($this->method_action_separator, $method_and_action);
    if (count($result) < 2) {
      throw new ErrorException(ErrorException::METHOD_ACTION_UNDEFINED, "Action can't be null");
    }
    return $result;
  }

  /**
   * @return ContextContainer
   */
  public function getContext(): ContextContainer {
    return $this->context;
  }

  /**
   * @throws ErrorException
   */
  private function tryInvokeProcess(string $method_and_action, array $params = []) {
    [$method_name, $action_name] = $this->unpackMethodAndAction($method_and_action);
    if (!$this->schema->methodExists($method_name)) {
      throw new ErrorException(ErrorException::METHOD_UNDEFINED, "Undefined method `{$method_name}`");
    }
    $method = $this->schema->getMethod($method_name);
    if (!$method->__actionExists($action_name)) {
      throw new ErrorException(ErrorException::METHOD_ACTION_UNDEFINED, "Undefined action `{$action_name}` in method `{$method_name}`");
    }
    $method->__setContext($this->context);
    return ActionProcessor::create($method, $action_name, $params)->invoke();
  }

  /**
   * @throws ErrorException
   */
  public function invokeProcess(string $method_and_action, array $params = []): array {
    return ResponseBuilder::build($this->tryInvokeProcess($method_and_action, $params));
  }

  public function execute(string $method_and_action, array $params = []): array {
    try {
      return $this->invokeProcess($method_and_action, $params);
    } catch (\Exception $exception) {
      return [
        'error' => [
          'error_code'    => $exception->getCode(),
          'error_message' => $exception->getMessage(),
        ]
      ];
    }
  }

}
