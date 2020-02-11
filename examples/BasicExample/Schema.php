<?php
use MotionDots\Schema\AbstractSchema;

require_once './Methods/Users.php';
require_once './Methods/Context.php';
class Schema extends AbstractSchema {
    public function __construct()
    {
        $this->addMethods([
            new Users(),
            new Context(),
        ]);
    }

}