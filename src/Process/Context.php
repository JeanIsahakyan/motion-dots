<?php
namespace MotionDots\Process;

use MotionDots\Exception\ErrorException;

/**
 * Class Context
 *
 * @package MotionDots\Process
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class Context {

    private \ArrayObject $fields;

    public function __construct() {
      $this->fields = new \ArrayObject();
    }

    /**
     * @param string $field
     *
     * @throws ErrorException
     *
     * @return mixed
     */
    public function get(string $field) {
        if (!$this->fields->offsetExists($field)) {
            throw new ErrorException(ErrorException::CONTEXT_UNDEFINED_FIELD, "Can't get undefined field `{$field}`");
        }
        return $this->fields->offsetGet($field);
    }

    /**
     * @param string $field
     * @param mixed $value
     *
     * @return void
     */
    public function set(string $field, $value): self {
        $this->fields->offsetSet($field, $value);
        return $this;
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
     * @return array
     */
    public function getAll(): array {
        return $this->fields->getArrayCopy();
    }
}
