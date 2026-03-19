<?php

declare(strict_types=1);

// Define BASE_PATH
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Load Composer Autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load Environment Variables
$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
$dotenv->safeLoad();

// Load Helpers
require_once BASE_PATH . '/app/Core/helpers.php';

// Bootstrap Application
$app = new App\Core\Application(BASE_PATH);

return $app;
