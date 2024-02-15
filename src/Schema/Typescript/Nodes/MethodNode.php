<?php

namespace MotionDots\Schema\Typescript\Nodes;

use MotionDots\Schema\Typescript\TypeMapper;

/**
 * Class MethodNode
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class MethodNode {

  private string $name;
  /** @var TypeNodeInterface[] */
  private array             $params;
  private string            $separator;
  private TypeNodeInterface $response;

  public function __construct(array $schema, string $separator) {
    $this->separator = $separator;

    $this->name = $schema['name'];

    $params = [];
    $params_schema = (array)$schema['params'];
    foreach ($params_schema as $param_schema) {
      $params[] = TypeMapper::map($param_schema);
    }
    $this->params = $params;

    $this->response = TypeMapper::map($schema['response']);
  }

  public function toString(): string {
    $name = $this->nameToString();
    $method_name = $this->name;
    $params = $this->paramsToString();
    $response = $this->response->getTypeName();

    return <<<EOT
      export const {$name}Method = '{$method_name}';
      export interface {$name}Params {{$params}}
      export type {$name}Response = {$response};
      EOT;
  }

  private function nameToString(): string {
    $name_separated = str_replace($this->separator, ' ', $this->name);
    $name_separated = ucwords($name_separated);
    return str_replace(' ', '', $name_separated);
  }

  private function paramsToString(): string {
    if (!$this->params) {
      return '';
    }

    $params = "\n";
    foreach ($this->params as $param) {
      $params = $params . $param->innerToString() . "\n";
    }
    return $params;
  }

  /** @return ObjectNode[] */
  public function getObjects(): array {
    $objects = [];
    $response = $this->response;
    if ($response instanceof ObjectNode) {
      $objects[$response->getTypeName()] = $response;
      $objects += $response->getInnerObjects();
    }
    return $objects;
  }

  /** @return EnumNode[] */
  public function getEnums(): array {
    $enums = [];
    $response = $this->response;
    if ($response instanceof EnumNode) {
      $enums[$response->getTypeName()] = $response;
    }
    if ($response instanceof ObjectNode) {
      $enums += $response->getInnerEnums();
    }
    return $enums;
  }

}
