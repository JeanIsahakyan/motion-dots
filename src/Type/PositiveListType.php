<?php
namespace MotionDots\Type;

use MotionDots\Exception\ErrorException;
/**
 * Class PositiveListType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class PositiveListType extends AbstractType {
  public function parse(): array {
    $values = explode(',', $this->field);
    $result = [];
    foreach ($values as $value) {
      $value = intval(trim($value));
      if ($value <= 0) {
        continue;
      }
      $result[] = $value;
    }
    if (!$result) {
      throw new ErrorException(ErrorException::PARAM_INCORRECT, "`{$this->param_name}` must be positive_list");
    }
    return $result;
  }
}
