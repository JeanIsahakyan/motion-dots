<?php
namespace API\Types;

use MotionDots\Exception\ErrorException;
use MotionDots\Type\AbstractType;

class CodeType extends AbstractType {

  public function parse(): string {
    $field = (int)$this->field;
    if (!$field) {
      throw new ErrorException(ErrorException::PARAM_INCORRECT, 'Incorrect code format');
    }
    if (strlen($field) !== 6) {
      throw new ErrorException(ErrorException::PARAM_INCORRECT, 'You must specify 6 digits for the code');
    }
    return (string)$field;
  }
}
