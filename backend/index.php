<?php

use App\Controllers\Admin;
use App\Controllers\FileAccess;
use App\Controllers\Files;
use App\Controllers\FoldersFile;
use App\Controllers\User;
use App\Handler\TableValidator;
use App\Connect\Database;

require_once 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:3000"); // указываем домен, с которого разрешаем запросы
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // указываем методы, которые разрешено использовать
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    http_response_code(200);
    exit();
}
// Массив с маршрутами
    $urlList = [
        'GET' => [
            '/user/create/' => [User::class, 'createUser'],
            '/users/list/' => [User::class, 'getUsers'],
            '/users/get/{id:\d+}/' => [User::class, 'getUserById'],
            '/users/logout/{id:\d+}/' => [User::class, 'logout'],
            '/admin/users/list/' => [Admin::class, 'getUserList'],
            '/admin/users/get/{id:\d+}/' => [Admin::class, 'getUserById'],
            '/files/list/' => [Files::class, 'fileList'],
            '/files/get/{id:\d+}/' => [Files::class, 'getFile'], // added \d+ to make ID a number
            '/directories/get/{id:\d+}/' => [FoldersFile::class, 'getFolder'], // same here
            '/user/search/{email}/' => [User::class, 'searchByEmail'],
            '/get/shared/user/' => [FileAccess::class, 'getSharedUsers']
        ],
        'POST' => [
            '/users/login/' => [User::class, 'login'],
            '/users/reset_password' => [User::class, 'resetPassword'],
            '/files/add/' => [Files::class, 'addFile'],
            '/directories/add/' => [FoldersFile::class, 'addFolder'],
            '/users/register/' => [User::class, 'register']
        ],
        'PUT' => [
            '/users/update/{id:\d+}' => [User::class, 'updateUser'],
            '/admin/users/update/{id:\d+}' => [Admin::class, 'updateUser'],
            '/files/rename/{id:\d+}' => [Files::class, 'renameFile'],
            '/directories/rename/{id:\d+}' => [FoldersFile::class, 'renameFolder'], // added id with \d+ here as well
            '/files/share/{id:\d+}/{user_id:\d+}' => [FileAccess::class, 'addSharedUsers'] // same here
        ],
        'DELETE' => [
            '/admin/users/delete/{id:\d+}' => [Admin::class, 'deleteUsers'],
            '/user/delete/{id:\d+}' => [User::class, 'deleteUser'],
            '/files/remove/{id:\d+}' => [Files::class, 'removeFile'], // same here
            '/directories/delete/{id:\d+}' => [FoldersFile::class, 'removeFolder'], // and here
            '/files/share/{id:\d+}/{user_id:\d+}' => [FileAccess::class, 'removeSharedUser'] // and finally here
        ]
    ];

    $requestMethod = $_SERVER[ 'REQUEST_METHOD' ];
    $requestUrl = $_SERVER[ 'REQUEST_URI' ];
    $routeFound = false;

    if (!array_key_exists($requestMethod, $urlList)) {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        exit;
    }

    foreach ($urlList[ $requestMethod ] as $url => $action) {
        $urlPattern = preg_replace ( '/\//', '\\/', $url );
        $urlPattern = preg_replace ( '/\{([a-zA-Z0-9]+):([^\}]+)\}/', '(?P<\1>\2)', $urlPattern );
        $urlPattern = '/^' . $urlPattern . '$/';

        if (preg_match ( $urlPattern, $requestUrl, $matches )) {
            $routeFound = true;

            array_shift ( $matches );
            $paramValues = array_values ( $matches );
            $controllerName = $action[ 0 ];
            $methodName = $action[ 1 ];

            $dt = new Database();
            $body = file_get_contents ( 'php://input' );
            $data = json_decode ( $body, true );
            $controller = new $controllerName( $dt );

            try {
                $validator = new TableValidator( 'table_name' );
                $validator->check ();
            } catch (Exception $e) {

            }

//            $controller->$methodName( $data );
//            return;

            if (in_array ( $methodName, [ 'updateUser', 'renameFile', 'deleteUser', 'logout' ] )) {
                $id = $data[ 'id' ];
                $controller->$methodName( $data[ 'id' ], $data );
            } elseif ($methodName == 'login') {
                $controller->$methodName( ['email' => $data[ 'email' ], 'password' => $data[ 'password' ]] );
            } elseif ($methodName == 'register') {
                $controller->$methodName( $data[ 'email' ], $data[ 'name' ], $data[ 'password' ], $data[ 'role' ],
                    $data[ 'age' ], $data[ 'gender' ] );
            } elseif ($methodName == 'getUsers') {
               echo $controller->$methodName();
            } elseif ($methodName == 'getUserById') {
                echo $controller->$methodName($data[ 'id' ]);
            } else {
                $controller->$methodName( $data );
            }
        }
    }

    if (!$routeFound) {
        http_response_code ( 404 );
        echo json_encode ( ['error' => 'No such page'] );
    }
