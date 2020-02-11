<?php

namespace MotionDots\Error;

/**
 * Class AbstractErrors
 *
 * @package MotionDots\Errors
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractErrors implements ErrorsInterface {

    /**
     * @var array
     */
    private $errors_list = [];

    /**
     * @param int $error_code
     *
     * @return bool
     */
    public final function errorExists(int $error_code): bool {
        return array_key_exists($error_code, $this->errors_list);
    }

    /**
     * @param $error_code
     * @param string $message
     */
    public final function addError(int $error_code, string $message): void {
        if ($this->errorExists($error_code)) {
            return;
        }
        $this->errors_list[$error_code] = $message;
    }

    /**
     * @param array $errors
     */
    public final function addErrors(array $errors): void {
        foreach ($errors as $error_code => $message) {
            $this->addError($error_code, $message);
        }
    }

    /**
     * @return array
     */
    public final function getErrorsList(): array {
       return $this->errors_list;
    }
}
