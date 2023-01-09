<?php
namespace MotionDots\Type;
/**
 * Class IntListType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class IntListType extends AbstractType {
  public function parse(): array {
    return array_map(fn($list_id) => intval(trim($list_id)), explode(',', $this->field));
  }
}
