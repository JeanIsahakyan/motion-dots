<?php

/**
 * Class IntArrayType
 */
class IntArrayType extends \MotionDots\Type\AbstractType {

    /**
     * @return array
     */
    public function build(): array {
        return array_map('intval', explode(',', $this->field));
    }
}