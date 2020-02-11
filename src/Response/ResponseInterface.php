<?php
namespace MotionDots\Response;

use MotionDots\Error\AbstractErrors;
use MotionDots\Error\ErrorsInterface;

/**
 * Interface ResponseInterface
 *
 * @package MotionDots\Response
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
interface ResponseInterface {

    /**
     * @return array
     */
    public function build(): array;

    /**
     * @return ErrorsInterface
     */
    public function getErrors(): ErrorsInterface;
}
