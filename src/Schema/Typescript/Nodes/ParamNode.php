<?php

namespace MotionDots\Schema\Typescript\Nodes;

use MotionDots\Schema\Typescript\TypeMapper;

class ParamNode implements NodeInterface {
  private string $type;
  private string $name;
  private bool $is_required;

  /** @param mixed[] $schema */
  public function __construct(array $schema) {
    $this->type = (string)$schema['type'];
    $this->name = (string)$schema['name'];
    $this->is_required = (bool)$schema['required'];
  }

  public function toString() : string {
    $type = TypeMapper::map($this->type);
    $is_required = $this->is_required ? '' : '?';
    return "{$this->name}{$is_required}: {$type};";
  }
}
