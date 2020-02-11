<?php

namespace MotionDots\Error;

/**
 * Class InternalErrors
 *
 * @package MotionDots\Error
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class InternalErrors extends AbstractErrors implements ErrorsInterface {

    public const SCHEMA_METHOD_EXISTS    = -1;
    public const PARAM_UNSUPPORTED       = -2;
    public const PARAM_UNKNOWN_RESOLVER  = -2;
    public const PARAM_REFLECTION_ERROR  = -2;
    public const PARAM_IS_REQUIRED       = -3;
    public const CONTEXT_UNDEFINED_FIELD = -4;
    public const METHOD_ACTION_UNDEFINED = -4;
    public const METHOD_UNDEFINED        = -5;

    /**
     * Errors Builder
     */
    public function build(): void {
        $this->addErrors([
            self::SCHEMA_METHOD_EXISTS,
            self::PARAM_UNSUPPORTED,
            self::PARAM_UNKNOWN_RESOLVER,
            self::PARAM_REFLECTION_ERROR,
            self::PARAM_IS_REQUIRED,
            self::CONTEXT_UNDEFINED_FIELD,
            self::METHOD_ACTION_UNDEFINED,
            self::METHOD_UNDEFINED,
        ]);
    }
}