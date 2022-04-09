<?php

namespace MotionDots\Type;

use MotionDots\Process\ContextContainer;

interface TypeInterface {
  /**
   * TypeInterface constructor.
   *
   * @param $field
   */
  public function __construct($field, $param_name, ?ContextContainer $context = null);

  /**
   * @return mixed
   */
  public function parse();

  /**
   * @return mixed
   */
  public function build();

  public function example(): string;

}
