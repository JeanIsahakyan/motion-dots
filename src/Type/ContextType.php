<?php
namespace MotionDots\Type;

use MotionDots\Process\ContextContainer;

/**
 * Class AbstractType
 *
 * @package MotionDots\Type
 *
 * @author Jean Isahakyan <jeanisahakyan@gmail.com>
 */
class ContextType extends AbstractType implements TypeInterface {

    /**
     * @return ContextContainer
     */
    public function build(): ContextContainer {
        return $this->field;
    }

}
