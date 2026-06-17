<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use ErpStocco\Presentation\Routes\Router;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ? '1' : '0');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . ($_ENV['CORS_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();
$router->dispatch();
