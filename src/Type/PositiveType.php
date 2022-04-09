<?php
namespace MotionDots\Type;

use MotionDots\Exception\ErrorException;
/**
 * Class PositiveType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class PositiveType extends AbstractType {
  public const NAME = 'positive';
  public const DESCRIPTION = '';

  public function example(): string {
    return '1,2,3';
  }
  public function parse(): int {
    $field = (int)$this->field;
    if ($field < 0) {
      throw new ErrorException(ErrorException::PARAM_INCORRECT, "`{$this->param_name}` must be positive");
    }
    return $field;
  }
}
