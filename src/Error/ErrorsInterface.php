<?php

namespace MotionDots\Error;

/**
 * Interface ErrorsInterface
 *
 * @package MotionDots\Error
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
interface ErrorsInterface {

    /**
     *  Errors Builder
     */
    public function build(): void;

    /**
     * @return array
     */
    public function getErrorsList(): array;

    /**
     * @param int $error_code
     *
     * @return bool
     */
    public function errorExists(int $error_code): bool;
}
