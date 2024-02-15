<?php

namespace MotionDots\Schema\Typescript\Nodes;

use MotionDots\Schema\Typescript\Repositories\ObjectsRepository;
use MotionDots\Schema\Typescript\TypeMapper;

/**
 * Class ObjectNode
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class ObjectNode implements TypeNodeInterface {

  public const TYPE = 'object';

  private string $name;
  private bool   $is_required;
  private string $type_name;
  /** @var TypeNodeInterface[] */
  private array $properties = [];

  public function __construct(array $schema) {
    $this->name = (string)$schema['name'];
    $this->is_required = (bool)$schema['required'];
    $this->type_name = (string)$schema['type_name'];

    $properties_schema = $schema['properties'];
    $properties = [];
    foreach ($properties_schema as $property_schema) {
      $property_name = (string)$property_schema['name'];
      if (self::isExcludedProperty($property_name)) {
        continue;
      }
      $properties[] = TypeMapper::map($property_schema);
    }
    $this->properties = $properties;

    ObjectsRepository::add($this);
  }

  private static function isExcludedProperty(string $property_name): bool {
    return str_starts_with($property_name, '__');
  }

  public function toString(): string {
    $properties = "\n";
    foreach ($this->properties as $property) {
      $properties = $properties . "{$property->innerToString()}\n";
    }
    return "export type {$this->type_name} = {{$properties}};";
  }

  public function innerToString(): string {
    $type = $this->type_name;
    $is_required = $this->is_required ? '' : '?';
    return "  {$this->name}{$is_required}: {$type};";
  }

  public function getTypeName(): string {
    return $this->type_name;
  }

  /** @return ObjectNode[] */
  public function getInnerObjects(): array {
    $objects = [];
    foreach ($this->properties as $property) {
      if ($property instanceof ObjectNode) {
        $objects[$property->getTypeName()] = $property;
        $objects += $property->getInnerObjects();
      }
    }
    return $objects;
  }

  /** @return EnumNode[] */
  public function getInnerEnums(): array {
    $enums = [];
    foreach ($this->properties as $property) {
      if ($property instanceof EnumNode) {
        $enums[$property->getTypeName()] = $property;
      }
      if ($property instanceof ObjectNode) {
        $enums += $property->getInnerEnums();
      }
    }
    return $enums;
  }

}