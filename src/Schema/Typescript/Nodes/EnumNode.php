<?php

namespace MotionDots\Schema\Typescript\Nodes;

use MotionDots\Schema\Typescript\Repositories\EnumsRepository;

/**
 * Class EnumNode
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class EnumNode implements TypeNodeInterface {

  public const TYPE = 'enum';

  private string $name;
  private string $type_name;
  private bool   $is_required;
  /** @var string[] enum_name => enum_value */
  private array $values = [];

  public function __construct(array $schema) {
    $this->name = (string)$schema['name'];
    $this->type_name = (string)$schema['type_name'];
    $this->is_required = (bool)$schema['required'];

    $enum_schema = $schema['enum'];
    $enum_names_schema = $schema['enum_names'];
    $enum = [];
    for ($i = 0; $i < count($enum_names_schema); $i++) {
      $enum_name = $enum_names_schema[$i];
      $enum_value = $enum_schema[$i];
      $enum[$enum_name] = $enum_value;
    }
    $this->values = $enum;

    EnumsRepository::add($this);
  }

  public function toString(): string {
    $enum = "\n";
    foreach ($this->values as $enum_name => $enum_value) {
      if (is_int($enum_value)) {
        $enum = $enum . "  {$enum_name} = {$enum_value},\n";
      }
      if (is_string($enum_value)) {
        $enum = $enum . "  {$enum_name} = '{$enum_value}',\n";
      }
    }
    return "export enum {$this->type_name} {{$enum}}\n";
  }

  public function innerToString(): string {
    $type = $this->type_name;
    $is_required = $this->is_required ? '' : '?';
    return "  {$this->name}{$is_required}: {$type};";
  }

  public function getTypeName(): string {
    return $this->type_name;
  }

}
