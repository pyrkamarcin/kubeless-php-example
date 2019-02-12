<?php

require 'vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

function foo($event, $context) {
    $log = new Logger('name');
    $log->pushHandler(new StreamHandler("php://stdout", Logger::INFO));

    return json_encode(strtoupper($event->data));
}
