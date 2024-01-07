<?php

global $urlList;

use Core\Request;
use Core\Router;


require_once ('vendor/autoload.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


require_once ('common/routes.php');

    if($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
        http_response_code(200);
        exit();
    }

    $router = new Router($urlList);
    try {
        $router->route (new Request());
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
