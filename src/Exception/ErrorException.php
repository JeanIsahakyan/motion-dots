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

    /**
     * BasicException constructor.
     *
     * @param int $error_code
     *
     * @param string|null $additional_message
     */
    public function __construct(int $error_code = null, string $additional_message = null) {
        parent::__construct($additional_message, $error_code);
    }
}
