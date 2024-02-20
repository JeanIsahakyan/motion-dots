<?php

namespace MotionDots\Schema\Typescript\Repositories;

use MotionDots\Schema\Typescript\Nodes\MethodNode;

/**
 * class MethodsRepository
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class MethodsRepository {

  /** @var MethodNode[][] space_name => method[] */
  private static array $spaces_methods = [];

  /** @param MethodNode[] $methods */
  public static function add(string $space, array $methods): void {
    self::$spaces_methods[$space] = $methods;
  }

  /** @return MethodNode[][] space_name => method[] */
  public static function getSpacesMethods(): array {
    return static::$spaces_methods;
  }

  public static function getExports(): string {
    $spaces = array_keys(self::$spaces_methods);
    $exports = array_map(fn($space) => "export * from './{$space}';\n", $spaces);
    return implode($exports);
  }

}
