<?php
namespace MotionDots\Type;

/**
 * Class BuiltinType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class BuiltinType extends AbstractType implements TypeInterface {

    /**
     * @return mixed
     */
    public function build() {
        [$type, $value] = $this->field;
        settype($value, $type);
        return $value;
    }

}
