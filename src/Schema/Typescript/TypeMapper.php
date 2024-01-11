<?php

namespace MotionDots\Schema\Typescript;

class TypeMapper {

  public static function map(string $type): string {
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
