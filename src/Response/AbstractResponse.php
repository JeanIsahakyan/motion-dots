<?php

namespace MotionDots\Response;

use MotionDots\Type\AbstractType;

/**
 * Class AbstractResponse
 *
 * @package MotionDots\Response
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractResponse implements ResponseInterface {
    /**
     * @return array
     */
    public final function build(): array {
        return (array)$this;
    }
}
