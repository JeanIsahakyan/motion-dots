<?php

namespace MotionDots\Type;

use MotionDots\Process\Context;

interface TypeInterface {
  /**
   * TypeInterface constructor.
   *
   * @param $field
   */
  public function __construct(?string $field, ?string $param_name, Context &$context);

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
