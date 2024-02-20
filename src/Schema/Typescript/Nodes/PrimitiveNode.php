<?php

namespace MotionDots\Schema\Typescript\Nodes;

use MotionDots\Schema\Typescript\TypeMapper;

/**
 * Class PrimitiveNode
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class PrimitiveNode implements TypeNodeInterface {

  private string $name;
  private string $type;
  private bool   $is_required;

  public function __construct(array $schema) {
    $this->name = (string)$schema['name'];
    $this->type = (string)$schema['type'];
    $this->is_required = (bool)$schema['required'];
  }

  public function toString(): string {
    return TypeMapper::mapName($this->type);
  }

  public function innerToString(): string {
    $type = TypeMapper::mapName($this->type);
    $is_required = $this->is_required ? '' : '?';
    return "  {$this->name}{$is_required}: {$type};";
  }

  public function getTypeName(): string {
    return TypeMapper::mapName($this->type);
  }

}