<?php
namespace MotionDots\Type;

/**
 * Class AbstractType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
abstract class AbstractType implements TypeInterface {

    /**
     * @var mixed
     */
    protected $field = null;

    /**
     * AbstractType constructor.
     *
     * @param mixed $field
     */
    public function __construct($field = null) {
        $this->field = $field;
    }

    /**
     * @return mixed
     */
    public function build() {
        return $this->field;
    }
}