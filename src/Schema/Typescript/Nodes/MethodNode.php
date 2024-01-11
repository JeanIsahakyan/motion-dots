<?php

namespace MotionDots\Schema\Typescript\Nodes;

class MethodNode implements NodeInterface {

  private string $name;
  /** @var ParamNode[] */
  private array $params;
  private string $separator;
  private ResponseNode $response;

  /** @param mixed[] $schema */
  public function __construct(array $schema, string $separator) {
    $this->separator = $separator;

    $this->name = $schema['name'];

    $params = [];
    $params_schema = (array)$schema['params'];
    foreach ($params_schema as $param_schema) {
      $params[] = new ParamNode($param_schema);
    }
    $this->params = $params;

    $this->response = new ResponseNode($schema['response']);
  }

  /** @return ResponseNode[] */
  public function getResponseObjects(): array {
    return $this->response->getResponseObjects();
  }

  public function toString(): string {
    $name = $this->nameToString();
    $method_name = $this->name;
    $params = $this->paramsToString();
    $response = $this->responseToString();

    return <<<EOT
      export const {$name}Method = '{$method_name}';
      export type {$name}Params = {{$params}};
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
      $params = $params . '  ' . $param->toString() . "\n";
    }
    return $params;
  }

  private function responseToString(): string {
    return $this->response->getTypeName();
  }

}
