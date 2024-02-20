<?php

namespace MotionDots\Schema\Typescript;

use MotionDots\Schema\Typescript\Nodes\EnumNode;
use MotionDots\Schema\Typescript\Nodes\ObjectNode;
use MotionDots\Schema\Typescript\Nodes\PrimitiveNode;
use MotionDots\Schema\Typescript\Nodes\TypeNodeInterface;

/**
 * Class TypeMapper
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
class TypeMapper {

  public static function map(array $schema): TypeNodeInterface {
    return match ($schema['type']) {
      ObjectNode::TYPE => new ObjectNode($schema),
      EnumNode::TYPE   => new EnumNode($schema),
      default          => new PrimitiveNode($schema),
    };
  }

  public static function mapName(string $type): string {
    return match ($type) {
      'int'    => 'number',
      'float'  => 'number',
      'bool'   => 'boolean',
      'string' => 'string',
      'null'   => 'null',
      'array'  => 'any[]',
      'mixed'  => 'any',
      'object' => 'object',
      default  => 'any',
    };
  }

}
