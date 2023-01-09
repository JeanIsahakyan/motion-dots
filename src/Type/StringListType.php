<?php
namespace MotionDots\Type;

/**
 * Class StringListType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class StringListType extends AbstractType {

  public function parse(): array {
    return array_map(function($list_id) {
      return trim($list_id);
    }, explode(',', $this->field));
  }
}
