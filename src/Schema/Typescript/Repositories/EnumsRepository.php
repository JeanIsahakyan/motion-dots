<?php

namespace MotionDots\Schema\Typescript\Repositories;

use MotionDots\Schema\Typescript\Nodes\EnumNode;

/**
 * class EnumsRepository
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class EnumsRepository {

  /** @var EnumNode[] */
  private static array $enums = [];

  /** @return EnumNode[] */
  public static function getAll(): array {
    return self::$enums;
  }

  public static function add(EnumNode $enum): void {
    self::$enums[$enum->getTypeName()] = $enum;
  }

  /** @param EnumNode[] $enums */
  public static function getExports(array $enums): string {
    $exports = array_map(fn($object) => "export * from './{$object->getTypeName()}';\n", $enums);
    return implode($exports);
  }

  /** @param EnumNode[] $enums */
  public static function getImports(string $relative_path, array $enums): string {
    if (!$enums) {
      return '';
    }
    $type_names = array_map(fn($object) => $object->getTypeName(), $enums);
    $type_names = implode(', ', $type_names);
    return "import { {$type_names} } from '{$relative_path}';\n";
  }

}
