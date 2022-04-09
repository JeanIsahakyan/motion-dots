<?php

namespace MotionDots\Response;

/**
 * Class AbstractResponse
 *
 * @package MotionDots\Response
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractResponse implements ResponseInterface {

  public final static function create() {
    return new static();
  }

  /**
   * @return array
   */
  public final function build(): array {
    $fields = (array)$this;
    $result = [];
    foreach ($fields as $field => $value) {
      if ($value === null) {
        unset($fields[$field]);
        continue;
      }
      $result[$field] = $value;
    }
    if ($result) {
      $result[ResponseInterface::TYPE_ID_FIELD] = md5(static::class);
    }
    return $result;
  }
}
