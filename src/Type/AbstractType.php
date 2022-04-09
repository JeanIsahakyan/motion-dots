<?php
namespace MotionDots\Type;

/**
 * Class AbstractType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractType implements TypeInterface {

  public const NAME        = 'No name';
  public const DESCRIPTION = 'No description';

  /**
   * @var mixed
   */
  protected $field = null;

  /**
   * AbstractType constructor.
   *
   * @param mixed $field
   */
  public function __construct($field = null) {
    $this->field = $field;
  }


  public function example(): string {
    return 'No example';
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
