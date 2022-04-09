<?php
namespace MotionDots\Response;

/**
 * Interface ResponseInterface
 *
 * @package MotionDots\Response
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
interface ResponseInterface {
  public const TYPE_ID_FIELD = '__type_id'; // reserved field

  /**
   * @return ResponseInterface
   */
  public static function create();

  /**
   * @return array
   */
  public function build(): array;
}
