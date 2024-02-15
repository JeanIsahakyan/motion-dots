<?php

namespace MotionDots\Schema\Typescript\Repositories;

use MotionDots\Schema\Typescript\Nodes\ObjectNode;

/**
 * class ObjectsRepository
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class ObjectsRepository {

  /** @var ObjectNode[] */
  private static array $objects = [];

  /** @return ObjectNode[] */
  public static function getAll(): array {
    return self::$objects;
  }

  public static function add(ObjectNode $object): void {
    self::$objects[$object->getTypeName()] = $object;
  }

  /** @param ObjectNode[] $objects */
  public static function getExports(array $objects): string {
    $exports = array_map(fn($object) => "export * from './{$object->getTypeName()}';\n", $objects);
    return implode($exports);
  }

  /** @param ObjectNode[] $objects */
  public static function getImports(string $relative_path, array $objects): string {
    if (!$objects) {
      return '';
    }
    $type_names = array_map(fn($object) => $object->getTypeName(), $objects);
    $type_names = implode(', ', $type_names);
    return "import { {$type_names} } from '{$relative_path}';\n";
  }

}