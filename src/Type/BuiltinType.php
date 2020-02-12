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
        if ($type === 'boolean') {
            if ($value === 'false' || $value === '0') {
                $value = false;
            } elseif ($type === 'true' || $value === '1') {
                $value = true;
            }
        }
        settype($value, $type);
        return $value;
    }

}
