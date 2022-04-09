<?php

namespace MotionDots\Process;

use App\Graphql\Utils\Resolver\AbstractResolver;
use MotionDots\Type\AbstractType;
use MotionDots\Type\BuiltinType;

/**
 * Class ResponseBuilder
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan jeanisahakyan@gmail.com
 */
class ResponseBuilder {
  private const BUILTIN_TYPES = [
    'boolean',
    'integer',
    'double',
    'string',
    'null',
  ];
  private static function isBuiltinType($row) {
    return in_array(gettype($row), self::BUILTIN_TYPES);
  }

  private static function response($response): array {
    return [
      'response' => $response,
    ];
  }
  public static function build($response) {
    if (is_scalar($response)) {
      return self::response($response);
    }
    $result = [];
    foreach ($response as $key => $row) {
      if ($row instanceof AbstractType) {
        $row = $row->build();
        if (is_array($row)) {
          $row = self::build($row);
        } elseif (self::isBuiltinType($row)) {
          $row = (new BuiltinType($row))->build();
        }
      }
      $result[$key] = $row;
    }
    return self::response($result);
  }
}
