<?php

declare(strict_types = 1);

use App\Enum\AppEnvironment;
use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/configs/path_constants.php';


if ($_ENV['APP_ENV'] == AppEnvironment::Production->value){
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}else{
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

return require CONFIG_PATH . '/container/container.php';


