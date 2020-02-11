<?php
use MotionDots\Method\AbstractMethod;
use MotionDots\Response\AbstractResponse;

require_once './Responses/ItemsResponse.php';
require_once './Responses/UserResponse.php';
require_once './Types/IntArrayType.php';

class Users extends AbstractMethod {

    public function getOne(int $user_id): AbstractResponse {
        return new UserResponse($user_id, 'Jane', 'Doe', new IntArrayType('1,2'));
    }

    public function getMany(IntArrayType $user_ids): AbstractResponse {
        $user_ids = $user_ids->build();
        $items = [];
        foreach ($user_ids as $user_id) {
            $items[] = $this->getOne($user_id);
        }
        return new ItemsResponse($items, count($items));
    }
}