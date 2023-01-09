<?php
namespace MotionDots\Type;

use MotionDots\Exception\ErrorException;

/**
 * Class EnumType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class EnumType extends AbstractType implements TypeInterface {

  /**
   * @return mixed
   */
  public function parse(): \BackedEnum {
    /**
     * @var \UnitEnum $type;
     */
    [$type, $value] = $this->field;
    $value = $type::tryFrom($value);
    if (!$value) {
      $params = array_map(fn(\BackedEnum $enum) => $enum->value, $type::cases());
      $params = implode(', ', $params);
      throw new ErrorException(ErrorException::PARAM_INCORRECT, "Enum `$this->param_name` is incorrect, supported: `{$params}`");
    }
    return $value;
  }

  public function build() {
    return $this->parse();
  }

}
