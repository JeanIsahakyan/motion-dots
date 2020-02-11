<?php

/**
 * Class UserResponse
 */
class ItemsResponse extends \MotionDots\Response\AbstractResponse {
    public $count;
    public $items;

    /**
     * ItemsResponse constructor.
     *
     * @param array $items
     * @param int $count
     */
    public function __construct(array $items, int $count = 0) {
        $this->items = $items;
        $this->count = $count;
    }
}