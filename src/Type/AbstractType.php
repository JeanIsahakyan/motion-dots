<?php
namespace MotionDots\Type;

use MotionDots\Process\Context;

/**
 * Class AbstractType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractType implements TypeInterface {

  /**
   * @var mixed
   */
  protected $field      = null;
  protected $param_name = null;
  protected $context    = null;

  /**
   * AbstractType constructor.
   *
   * @param mixed $field
   */
  public function __construct($field, ?string $param_name, Context &$context) {
    $this->field      = $field;
    $this->param_name = $param_name;
    $this->context    = &$context;
  }

  /**
   * @return mixed
   */
  public function parse() {
    return $this->field;
  }

  /**
   * @return mixed
   */
  public function build() {
    return $this->field;
  }
}
