<?php
namespace MotionDots\Type;

/**
 * Class BuiltinType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class BuiltinType extends AbstractType implements TypeInterface {

  public const EXAMPLES = [
    'string'   => 'String text',
    'int'      => '1',
    'integer'  => '1',
    'bool'     => 'true',
    'boolean'  => 'true',
    'float'    => '1.0',
  ];

  public function example(): string  {
    [$type] = $this->field;
    return self::EXAMPLES[$type];
  }

  /**
   * @return mixed
   */
  public function parse() {
    [$type, $value] = $this->field;
    if ($type === 'boolean' || $type === 'bool') {
      if ($value === 'false' || $value === '0') {
        $value = false;
      } elseif ($type === 'true' || $value === '1') {
        $value = true;
      }
    }
    settype($value, $type);
    return $value;
  }

  public function build() {
    return $this->parse();
  }

}
