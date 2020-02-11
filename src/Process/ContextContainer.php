<?php
namespace MotionDots\Process;

use MotionDots\Error\InternalErrors;
use MotionDots\Exception\ErrorException;

/**
 * Class ContextContainer
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class ContextContainer {

    /**
     * @var mixed[]
     */
    private $fields = [];


    /**
     * @param string $field
     *
     * @return bool
     */
    private function exists(string $field) {
        return array_key_exists($field, $this->fields);
    }

    /**
     * @param string $field
     *
     * @throws ErrorException
     *
     * @return mixed
     */
    public function get(string $field) {
        if (!$this->exists($field)) {
            throw new ErrorException(InternalErrors::CONTEXT_UNDEFINED_FIELD, "Can't get undefined field `{$field}`");
        }
        return $this->fields[$field];
    }

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return void
     */
    public function set(string $field, $value): void {
        $this->fields[$field] = $value;
    }


    /**
     * @param mixed[] $fields
     */
    public function setMany(array $fields): void {
        foreach ($fields as $field => $value) {
            $this->set($field, $value);
        }
    }


    /**
     * @return mixed[]
     */
    public function getAll(): array {
        return $this->fields;
    }
}