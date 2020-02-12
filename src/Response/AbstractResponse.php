<?php

namespace MotionDots\Response;

use MotionDots\Error\ErrorsInterface;

/**
 * Class AbstractResponse
 *
 * @package MotionDots\Response
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractResponse implements ResponseInterface {

    /**
     * @var ErrorsInterface
     */
    private $__internal_method_errors = null;

    /**
     * @param ErrorsInterface $errors
     */
    public final function setErrors(ErrorsInterface $errors) {
        $this->__internal_method_errors = $errors;
    }

    /**
     * @return array
     */
    public final function build(): array {
        return (array)$this;
    }

    /**
     * @return ErrorsInterface
     */
    public final function getErrors(): ErrorsInterface {
        return $this->__internal_method_errors;
    }
}
