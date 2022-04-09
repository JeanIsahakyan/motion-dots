<?php

namespace MotionDots\Type;

interface TypeInterface {
  /**
   * TypeInterface constructor.
   *
   * @param $field
   */
  public function __construct($field);

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
