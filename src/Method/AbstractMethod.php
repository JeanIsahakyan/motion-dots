<?php

namespace MotionDots\Method;

use MotionDots\Response\ResponseInterface;

/**
 * Class AbstractMethod
 *
 * @package MotionDots\Method
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractMethod implements MethodInterface {

    /**
     * @param $name
     * @param $arguments
     * @return ResponseInterface
     */
    public function __call($name, $arguments): ResponseInterface {
        return $this->{$name}(...$arguments);
    }

    /**
     * @return string
     */
    public final function __toString(): string {
        return lcfirst(get_class($this));
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    public final function __actionExists(string $action): bool {
        return in_array($action, get_class_methods($this), true);
    }
}
