<?php

namespace MotionDots\Process;

/**
 * Class ResponseBuilder
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan jeanisahakyan@gmail.com
 */
class ResponseBuilder {
  private static function response($response): array {
    return [
      'response' => $response,
    ];
  }

  private static function tryBuildInternal($response) {
    if (is_object($response) && method_exists($response, 'build')) {
      $response = $response->build();
      $response = self::tryBuild($response);
    }
    if (is_array($response)) {
      $response = self::tryBuild($response);
    }
    if (is_object($response)) {
      $response = self::tryBuild($response);
    }
    return $response;
  }

  public static function tryBuild($response) {
    if (is_scalar($response)) {
      return $response;
    }
    if (is_object($response)) {
      return self::tryBuildInternal($response);
    }
    $result = [];
    foreach ($response as $key => $row) {
      $result[$key] = self::tryBuildInternal($row);
    }
    return $result;
  }

  public static function build($response) {
    $response = self::tryBuild($response);
    return self::response(self::tryBuild($response));
  }
}
