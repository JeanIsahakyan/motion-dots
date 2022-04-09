<?php
namespace MotionDots\Exception;

/**
 * Class ErrorException
 *
 * @package MotionDots\Exception
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class ErrorException extends \Exception {
  public const SCHEMA_METHOD_EXISTS    = -1;
  public const PARAM_UNSUPPORTED       = -2;
  public const PARAM_UNKNOWN_RESOLVER  = -2;
  public const PARAM_REFLECTION_ERROR  = -2;
  public const PARAM_IS_REQUIRED       = -3;
  public const CONTEXT_UNDEFINED_FIELD = -4;
  public const METHOD_ACTION_UNDEFINED = -4;
  public const METHOD_UNDEFINED        = -5;
  public const PARAM_INCORRECT         = -6;
  public const INTERNAL_ERROR          = -7;

  /**
   * BasicException constructor.
   *
   * @param int $error_code
   *
   * @param string|null $additional_message
   */
  public function __construct(int $error_code = null, ?string $additional_message = null) {
    parent::__construct($additional_message, $error_code);
  }
}
