<?php

namespace MotionDots\Schema\Typescript\Nodes;

use MotionDots\Schema\Typescript\TypeMapper;

/**
 * Class ResponseNode
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author Ermak Aleksandr a@yermak.info
 */
class ResponseNode implements NodeInterface {

    private string $name;
    private string $type;
    private string $type_name;
    private bool $is_required;
    /** @var self[] */
    private array $properties = [];
    /** @var string[] enum_name => enum_value */
    private array $enum = [];

    /** @param mixed[] $schema */
    public function __construct(array $schema) {
      $this->name = (string)$schema['name'];
      $this->type = (string)$schema['type'];
      $this->is_required = (bool)$schema['required'];
      $this->type_name = (string)$schema['type_name'];
      
      if ($this->type === 'object') {
        $properties_schema = $schema['properties'];
        $properties = [];
        foreach ($properties_schema as $property_schema) {
          $property_name = (string)$property_schema['name'];
          if (self::isExcludedProperty($property_name)) {
            continue;
          }
          $properties[] = new self($property_schema);
        }
        $this->properties = $properties;
      }

      if ($this->type === 'enum') {
        $enum_schema = $schema['enum'];
        $enum_names_schema = $schema['enum_names'];

        $enum = [];
        for ($i = 0; $i < count($enum_names_schema); $i++) {
          $enum_name = $enum_names_schema[$i];
          $enum_value = $enum_schema[$i];
          $enum[$enum_name] = $enum_value;
        }
        $this->enum = $enum;
      }
    }

    private static function isExcludedProperty(string $property_name): bool {
      return str_starts_with($property_name, '__');
    }

    /** @return self[] */
    public function getResponseObjects(): array {
      if ($this->type !== 'object') {
        return [];
      }

      $response_objects = [$this->type_name => $this];
      foreach ($this->properties as $property) {
        if (in_array($property->type, ['object', 'enum'])) {
          $response_objects[$property->type_name] = $property;
          $response_objects += $property->getResponseObjects();
        }
      }

      return $response_objects;
    }

    public function toString(): string {
      if (in_array($this->type, ['object', 'enum'])) {
        return $this->type_name;
      }
      return TypeMapper::map($this->type);
    }

    public function complexToString(): string {
      return match ($this->type) {
        'enum'   => $this->enumToString(),
        'object' => $this->objectToString(),
        default  => '',
      };
    }

    public function objectToString(): string {
      $properties = "\n";
      foreach ($this->properties as $property) {
        $property = match ($property->type) {
          'enum'   => $property->innerEnumToString(),
          'object' => $property->innerObjectToString(),
          default  => $property->innerPrimitiveToString(),
        };
        $properties = $properties . $property . "\n";
      }

      return "export type {$this->type_name} = {{$properties}};";
    }

  public function enumToString(): string {
    $enum = "\n";
    foreach ($this->enum as $enum_name => $enum_value) {
      if (is_int($enum_value)) {
        $enum = $enum . "  {$enum_name} = {$enum_value},\n";
      }
      if (is_string($enum_value)) {
        $enum = $enum . "  {$enum_name} = '{$enum_value}',\n";
      }
    }

    return "export enum {$this->type_name} {{$enum}}";
  }

  public function innerEnumToString(): string {
    $type = $this->type_name;
    $is_required = $this->is_required ? '' : '?';
    return "  {$this->name}{$is_required}: {$type};";
  }

    private function innerObjectToString(): string {
      $type = $this->type_name;
      $is_required = $this->is_required ? '' : '?';
      return "  {$this->name}{$is_required}: {$type};";
    }

    private function innerPrimitiveToString(): string {
      $type = TypeMapper::map($this->type);
      $is_required = $this->is_required ? '' : '?';
      return "  {$this->name}{$is_required}: {$type};";
    }

}