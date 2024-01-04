<?php

global $urlList;

use Controllers\AdminController;
use Controllers\FileAccessController;
use Controllers\FilesController;
use Controllers\FoldersFileController;
use Controllers\UserController;
use Core\Request;
use Core\Router;

require_once ('components/autoload.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:3000"); // указываем домен, с которого разрешаем запросы
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // указываем методы, которые разрешено использовать
header("Access-Control-Allow-Headers: Content-Type, Authorization");

    if($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
        http_response_code(200);
        exit();
    }

   require_once ('configs/routes.php');

    $router = new Router($urlList);
    try {
        $router->route (new Request());
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
