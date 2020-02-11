<?php
namespace MotionDots\Schema;

use MotionDots\Error\InternalErrors;
use MotionDots\Exception\ErrorException;
use MotionDots\Method\AbstractMethod;

/**
 * Class AbstractSchema
 *
 * @package MotionDots\Schema
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractSchema implements SchemaInterface {

    /**
     * @var AbstractMethod[]
     */
    protected $methods = [];

    /**
     * @param string $method
     *
     * @return bool
     */
    public final function methodExists(string $method): bool {
        return array_key_exists($method, $this->methods);
    }

    /**
     * @param AbstractMethod $method
     *
     * @throws ErrorException
     */
    public final function addMethod(AbstractMethod $method): void {
        $method_name = (string)$method;
        if ($this->methodExists($method_name)) {
            throw new ErrorException(InternalErrors::SCHEMA_METHOD_EXISTS, "Method `{$method_name}` already exists.");
        }
        $this->methods[$method_name] = $method;
    }

    /**
     * @param AbstractMethod[] $methods
     *
     * @throws ErrorException
     */
    public final function addMethods(array $methods): void {
        foreach ($methods as $method) {
            $this->addMethod($method);
        }
    }

    /**
     * @param string $method
     *
     * @return AbstractMethod
     */
    public final function getMethod(string $method): AbstractMethod {
        return $this->methods[$method];
    }

    /**
     * @return AbstractMethod[]
     */
    public final function getMethods(): array {
        return $this->methods;
    }
}