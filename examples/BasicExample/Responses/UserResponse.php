<?php

/**
 * Class UserResponse
 */
class UserResponse extends \MotionDots\Response\AbstractResponse {
    public $user_id;
    public $first_name;
    public $last_name;
    public $categories;

    /**
     * UserResponse constructor.
     *
     * @param int $user_id
     * @param string $first_name
     * @param string $last_name
     * @param IntArrayType $categories
     */
    public function __construct(int $user_id, string $first_name, string $last_name, IntArrayType $categories) {
        $this->user_id = $user_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->categories = $categories;
    }
}