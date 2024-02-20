<?php

namespace MotionDots\Schema\Typescript\Nodes;

/**
 * Interface TypeNodeInterface
 *
 * @package MotionDots\Schema\Typescript
 *
 * @author  Ermak Aleksandr a@yermak.info
 */
interface TypeNodeInterface {

  public function __construct(array $schema);

  public function toString(): string;

  public function innerToString(): string;

  public function getTypeName(): string;

}