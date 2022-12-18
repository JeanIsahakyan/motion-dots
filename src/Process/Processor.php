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
  protected Context $context;
  protected Schema $schema;
  protected string $separator;

  /**
   * Processor constructor.
   *
   * @param Schema $schema
   * @param string $separator
   */
  public function __construct(Schema $schema, string $separator = '.', bool $disable_system_methods = false) {
    $this->schema  = &$schema;
    if (!$disable_system_methods) {
      $this->schema->addMethod(new System($this->schema, $separator));
    }
    $this->context   = new Context();
    $this->separator = $separator;
  }

  /**
   * @param string $method
   *
   * @throws ErrorException
   *
   * @return array
   */
  private function unpackMethodAndAction(string $method) {
    $result = explode($this->separator, $method);
    if (count($result) < 2) {
      throw new ErrorException(ErrorException::METHOD_ACTION_UNDEFINED, "Action can't be null");
    }
    return $result;
  }

  /**
   * @return Context
   */
  public function getContext(): Context {
    return $this->context;
  }

  /**
   * @throws ErrorException
   */
  private function tryInvokeProcess(string $method, array &$params = []) {
    [$method_name, $action_name] = $this->unpackMethodAndAction($method);
    return $this->schema->tryInvokeProcess($this->context, $method_name, $action_name, $params);
  }

  /**
   * @throws ErrorException
   */
  public function invokeProcess(string $method, array &$params = []): array {
    return ResponseBuilder::build($this->tryInvokeProcess($method, $params));
  }

  /**
   * @param string $method
   * @param array $params
   * @return array
   */
  public function execute(string $method, array &$params = []): array {
    try {
      return $this->invokeProcess($method, $params);
    } catch (\Exception $exception) {
      return [
        'error' => [
          'error_code'    => $exception->getCode(),
          'error_message' => $exception->getMessage(),
        ],
        'request_params'  => $params,
      ];
    }
  }

}
