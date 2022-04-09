<?php
namespace MotionDots\Type;

use MotionDots\Exception\ErrorException;

/**
 * Class CountType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class CountType extends AbstractType {
    public function parse(): int {
        $field = (int)$this->field;
        if ($field > 1000 || $field <= 0) {
          throw new ErrorException(ErrorException::PARAM_INCORRECT, "`$this->param_name` can be between 10 and 1000");
        }
        return $field;
    }
}
