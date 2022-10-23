<?php

define('ROOT_DIR', __DIR__);

require_once 'vendor/autoload.php';

spl_autoload_register(function ($class_name) {
    require_once ROOT_DIR.'/'.str_replace('\\', '/', $class_name).'.php';
});
