<?php

namespace MotionDots\Method;

use MotionDots\Response\ResponseInterface;

/**
 * Interface MethodInterface
 *
 * @package MotionDots\Method
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
interface MethodInterface {

    /**
     * @param $name
     * @param $arguments
     * @return ResponseInterface
     */
    public function __call($name, $arguments): ResponseInterface;
}
