<?php

/**
 * Class ContextItemResponse
 */
class ContextItemResponse extends \MotionDots\Response\AbstractResponse {
    public $name;
    public $value;

    /**
     * ContextItemResponse constructor.
     *
     * @param string $name
     * @param mixed $value
     */
    public function __construct(string $name, $value) {
        $this->name = $name;
        $this->value = $value;
    }
}