<?php

require_once '../../vendor/autoload.php';
require_once './Schema.php';

use MotionDots\Process\Processor;


header('Content-Type: application/json; charset=UTF-8');
try{
    $processor = new Processor(new Schema());
    $processor->getContext()->setMany([
        'user_id' => 1,
        'some_key' => 'ok',
    ]);
    echo json_encode([
        'response' => $processor->invokeProcess($_GET['method'], $_GET)
    ]);
} catch (\Exception $exception) {
    echo json_encode([
        'error_code'    => $exception->getCode(),
        'error_message' => $exception->getMessage(),
    ]);
}