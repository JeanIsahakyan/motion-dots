<?php

namespace MotionDots\Schema\Typescript\Nodes;

/**
 * Class MethodsNode
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author Ermak Aleksandr a@yermak.info
 */
class MethodsNode implements NodeInterface {

  private string $name;
  private array $methods;

  /** @param mixed[] $schema */
  public function __construct(array $schema, string $separator) {
    $this->name = $schema['name'];

    $methods = [];
    $methods_schema = (array)$schema['methods'];
    foreach ($methods_schema as $method_schema) {
      $method = new MethodNode($method_schema, $separator);
      $methods[] = $method;
    }
    $this->methods = $methods;
  }

  public function toString(): string {
    $name = ucfirst($this->nameToString());
    $methods = $this->methodsToString();
    $response_objects = $this->responseObjectsToString();
    return <<<EOT
      // This file provides api-schema for {$name} methods.
      // All code below is auto-generated.

      // Methods

      {$methods}

      // Response Objects

      {$response_objects}
      EOT;
  }

  private function nameToString(): string {
    return $this->name;
  }

  private function methodsToString(): string {
    $methods = '';
    foreach ($this->methods as $method) {
      $methods = $methods . $method->toString() . "\n\n";
    }
    $methods = substr($methods, 0, -2);
    return $methods;
  }

  private function responseObjectsToString(): string {
    $methods_response_objects = [];
    foreach ($this->methods as $method) {
      $methods_response_objects += $method->getResponseObjects();
    }

    $response_objects = '';
    foreach ($methods_response_objects as $methods_response_object) {
      $methods_response_object = "export type {$methods_response_object->getTypeName()} = {$methods_response_object->toString()}";
      $response_objects = $response_objects . $methods_response_object .  "\n\n";
    }
    $response_objects = substr($response_objects, 0, -2);

    return $response_objects;
  }

}