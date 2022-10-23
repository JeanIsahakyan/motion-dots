<?php

use MotionDots\Process\Processor;
use MotionDots\Schema\Schema;
use API\Methods\Users;

require_once 'autoload.php';
set_time_limit(0);
@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

try {
  $params = array_merge($_GET, $_POST, $_FILES);
  header("Access-Control-Allow-Origin: *");
  header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
  header('Access-Control-Allow-Credential: true');
  header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
  header('Content-Type: application/json; charset=UTF-8');
  $schema = Schema::create()
    ->addMethods([
      new Users(),
      // etc..
    ]);
  $processor = new Processor($schema, '.');
  if (preg_match('/\/api\/([a-zA-Z\.]+)/i',  $_SERVER['REQUEST_URI'], $matches)) { // something like /api/users.getById?param=value
    [, $method] = $matches;
  } else {
    $method = 'system.getSchema'; // fallback to schema info as default
  }
  echo json_encode($processor->invokeProcess($method, $params));
} catch (\Exception $exception) {
  echo json_encode([
    'error' => [
      'error_code'    => $exception->getCode(),
      'error_message' => $exception->getMessage(),
    ]
  ]);
}




