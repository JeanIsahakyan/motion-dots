<?php

namespace MotionDots\Schema;

use MotionDots\Method\AbstractMethod;

/**
 * Interface SchemaInterface
 *
 * @package MotionDots\Schema
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
interface SchemaInterface {
    /**
     * @param string $method
     *
     * @return bool
     */
    public function methodExists(string $method): bool;

    /**
     * @param AbstractMethod $method
     */
    public function addMethod(AbstractMethod $method): void;

    /**
     * @param AbstractMethod[] $methods
     */
    public function addMethods(array $methods): void;

    /**
     * @param string $method
     *
     * @return AbstractMethod
     */
    public function getMethod(string $method): AbstractMethod;

    /**
     * @return AbstractMethod[]
     */
    public function getMethods(): array;
}