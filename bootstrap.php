<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/configs/path_constants.php';
    

$env_path = file_exists(__DIR__ . '/../.env') ? __DIR__ . '/../' : __DIR__;

$dotenv = Dotenv::createImmutable($env_path);
$dotenv->load();

return require CONFIG_PATH . '/container/container.php';
